<?php

namespace JingdongCloudTradeBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\Client;
use JingdongCloudTradeBundle\Service\SkuService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 同步京东商品数据命令
 */
#[AsCommand(
    name: self::NAME,
    description: '同步京东商品数据'
)]
class SkuSyncCommand extends Command
{
    public const NAME = 'jingdong:sku:sync';
    public function __construct(
        private readonly Client $client,
        private readonly EntityManagerInterface $entityManager,
        private readonly SkuRepository $skuRepository,
        private readonly SkuService $skuService,
        private readonly AccountRepository $accountRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('account-id', 'a', InputOption::VALUE_OPTIONAL, '指定同步的京东账户ID')
            ->addOption('category-id', 'c', InputOption::VALUE_OPTIONAL, '指定同步的商品分类ID')
            ->addOption('brand-id', 'b', InputOption::VALUE_OPTIONAL, '指定同步的品牌ID')
            ->addOption('force', 'f', InputOption::VALUE_NONE, '强制重新同步所有商品')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '同步商品数量限制', 500)
            ->addOption('detail', 'd', InputOption::VALUE_NONE, '是否同步商品详情')
            ->addOption('price', 'p', InputOption::VALUE_NONE, '是否同步商品价格')
            ->addOption('stock', 's', InputOption::VALUE_NONE, '是否同步商品库存');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('开始同步京东商品数据');

        // 解析参数
        $accountId = $input->getOption('account-id');
        $categoryId = $input->getOption('category-id');
        $brandId = $input->getOption('brand-id');
        $force = $input->getOption('force');
        $limit = (int)$input->getOption('limit');
        $syncDetail = $input->getOption('detail');
        $syncPrice = $input->getOption('price');
        $syncStock = $input->getOption('stock');

        // 确定要使用的账户
        if ($accountId !== null) {
            $account = $this->accountRepository->find($accountId);
            if ($account === null) {
                $io->error("账户ID: {$accountId} 不存在");
                return Command::FAILURE;
            }
            $accounts = [$account];
        } else {
            $accounts = $this->accountRepository->findAll();
            if (empty($accounts)) {
                $io->error('系统中没有配置京东账户');
                return Command::FAILURE;
            }
        }

        $io->section('正在同步商品基本信息');
        $totalSynced = 0;

        foreach ($accounts as $account) {
            $io->info("账户: {$account->getName()} (ID: {$account->getId()})");

            try {
                $page = 1;
                $pageSize = 100;
                $hasMore = true;
                $syncedCount = 0;

                while ($hasMore && $syncedCount < $limit) {
                    $params = [
                        'page' => $page,
                        'pageSize' => $pageSize,
                    ];

                    if ($categoryId !== null) {
                        $params['cid3'] = $categoryId;
                    }

                    if ($brandId !== null) {
                        $params['brandId'] = $brandId;
                    }

                    $io->text("正在获取第 {$page} 页商品列表...");
                    $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuList', $params);

                    if (!isset($result['result']['success']) || !$result['result']['success']) {
                        $io->error('获取SKU列表失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
                        continue 2; // 跳到下一个账户
                    }

                    $skuList = $result['result']['materialSkuVoList'] ?? [];
                    $total = $result['result']['total'] ?? 0;
                    
                    if (empty($skuList)) {
                        $io->text('没有获取到更多商品数据');
                        break;
                    }

                    $io->progressStart(count($skuList));

                    foreach ($skuList as $skuData) {
                        $skuId = $skuData['skuId'] ?? '';
                        if (empty($skuId)) {
                            $io->progressAdvance();
                            continue;
                        }
                        
                        $sku = $this->skuRepository->findBySkuId($skuId);
                        $isNew = false;
                        
                        if ($sku === null) {
                            $sku = new Sku();
                            $sku->getBaseInfo()->setSkuId($skuId);
                            $sku->setAccount($account);
                            $isNew = true;
                        } elseif ($force !== true && $sku->getUpdateTime() !== null && (time() - $sku->getUpdateTime()->getTimestamp() < 86400)) {
                            // 如果不是强制更新且商品在1天内已更新过，则跳过
                            $io->progressAdvance();
                            continue;
                        }
                        
                        $this->fillSkuData($sku, $skuData);
                        $this->entityManager->persist($sku);
                        
                        // 每100个商品保存一次
                        if (($syncedCount + 1) % 100 === 0) {
                            $this->entityManager->flush();
                        }
                        
                        $syncedCount++;
                        $totalSynced++;
                        $io->progressAdvance();
                        
                        // 如果达到限制，结束循环
                        if ($syncedCount >= $limit) {
                            break;
                        }
                    }
                    
                    $io->progressFinish();
                    $this->entityManager->flush();
                    
                    // 检查是否有更多页
                    $hasMore = $total > $page * $pageSize;
                    $page++;
                }
                
                $io->success("账户 {$account->getName()} 同步完成，共同步 {$syncedCount} 个商品");
                
            } catch (\Throwable $e) {
                $io->error("同步发生错误: " . $e->getMessage());
            }
        }

        // 同步商品详情
        if ($syncDetail === true) {
            $this->syncSkuDetails($io, $accounts, $limit, $force);
        }
        
        // 同步商品价格
        if ($syncPrice === true) {
            $this->syncSkuPrices($io, $accounts, $limit, $force);
        }
        
        // 同步商品库存
        if ($syncStock === true) {
            $this->syncSkuStocks($io, $accounts, $limit, $force);
        }

        $io->success("商品数据同步完成，共同步 {$totalSynced} 个商品");
        return Command::SUCCESS;
    }

    /**
     * 同步商品详情
     */
    private function syncSkuDetails(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品详情');

        $syncedCount = 0;
        $hasError = false;

        foreach ($accounts as $account) {
            // 获取需要同步详情的SKU列表
            $criteria = ['account' => $account];
            if (!$force) {
                // 如果不强制更新，则仅同步3天内未更新详情的商品
                $dateThreshold = new \DateTime('-3 days');
                $criteria['detailUpdatedAt'] = ['$lt' => $dateThreshold];
            }

            $skus = $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
            
            if (empty($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步详情的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . " 个商品详情");
            $io->progressStart(count($skus));

            foreach ($skus as $sku) {
                try {
                    $params = [
                        'skuId' => $sku->getBaseInfo()->getSkuId()
                    ];
                    
                    $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuDetail', $params);

                    if (!isset($result['result']['success']) || !$result['result']['success']) {
                        $io->note("商品 {$sku->getBaseInfo()->getSkuId()} 详情获取失败：" . ($result['result']['errorMsg'] ?? '未知错误'));
                        $io->progressAdvance();
                        continue;
                    }

                    $skuData = $result['result']['skuDetailVo'] ?? null;
                    if (!$skuData) {
                        $io->progressAdvance();
                        continue;
                    }

                    $this->fillSkuDetailData($sku, $skuData);
                    $sku->setDetailUpdatedAt(new \DateTimeImmutable());
                    $this->entityManager->persist($sku);
                    
                    // 每50个商品保存一次
                    if (($syncedCount + 1) % 50 === 0) {
                        $this->entityManager->flush();
                    }
                    
                    $syncedCount++;
                } catch (\Throwable $e) {
                    $io->note("商品 {$sku->getBaseInfo()->getSkuId()} 详情同步错误: " . $e->getMessage());
                    $hasError = true;
                }
                
                $io->progressAdvance();
                
                // API限速，避免请求过快
                usleep(500000); // 500ms
            }
            
            $io->progressFinish();
            $this->entityManager->flush();
        }

        if ($hasError) {
            $io->warning("商品详情同步完成，但有部分商品同步失败");
        } else {
            $io->success("商品详情同步完成，共同步 {$syncedCount} 个商品详情");
        }
    }

    /**
     * 同步商品价格
     */
    private function syncSkuPrices(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品价格');
        $syncedCount = 0;

        foreach ($accounts as $account) {
            // 获取需要同步价格的SKU
            $criteria = ['account' => $account];
            if (!$force) {
                // 如果不强制更新，则仅同步1天内未更新价格的商品
                $dateThreshold = new \DateTime('-1 days');
                $criteria['priceUpdatedAt'] = ['$lt' => $dateThreshold];
            }

            $skus = $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
            
            if (empty($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步价格的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . " 个商品价格");
            
            // 批量同步价格，每批100个
            $skuBatches = array_chunk($skus, 100);
            $io->progressStart(count($skuBatches));
            
            foreach ($skuBatches as $skuBatch) {
                $skuIds = array_map(function ($sku) {
                    return $sku->getBaseInfo()->getSkuId();
                }, $skuBatch);
                
                try {
                    $params = [
                        'skuIds' => implode(',', $skuIds)
                    ];
                    
                    $result = $this->client->execute($account, 'jingdong.ctp.ware.price.getSkuPriceInfoList', $params);

                    if (!isset($result['result']['success']) || !$result['result']['success']) {
                        $io->note("批量获取价格失败：" . ($result['result']['errorMsg'] ?? '未知错误'));
                        $io->progressAdvance();
                        continue;
                    }

                    $priceList = $result['result']['priceInfoVoList'] ?? [];
                    if (empty($priceList)) {
                        $io->progressAdvance();
                        continue;
                    }

                    // 用SKU ID作为键组织价格数据
                    $priceMap = [];
                    foreach ($priceList as $priceData) {
                        $skuId = $priceData['skuId'] ?? '';
                        if ($skuId) {
                            $priceMap[$skuId] = $priceData;
                        }
                    }
                    
                    // 更新商品价格信息
                    foreach ($skuBatch as $sku) {
                        $skuId = $sku->getBaseInfo()->getSkuId();
                        if (isset($priceMap[$skuId])) {
                            $priceData = $priceMap[$skuId];
                            $sku->getBaseInfo()->setPrice((string)($priceData['price'] ?? 0));
                            $sku->getBaseInfo()->setMarketPrice((string)($priceData['marketPrice'] ?? 0));
                            
                            // 设置促销价格（如果有）
                            if (isset($priceData['promoPrice'])) {
                                $sku->getSpecification()->setPromoPrice((string)$priceData['promoPrice']);
                                $sku->getSpecification()->setHasPromotion(true);
                                
                                if (isset($priceData['promoStartTime']) && isset($priceData['promoEndTime'])) {
                                    $promoInfo = [
                                        'promoPrice' => $priceData['promoPrice'],
                                        'startTime' => $priceData['promoStartTime'],
                                        'endTime' => $priceData['promoEndTime']
                                    ];
                                    $sku->getSpecification()->setPromotionInfo($promoInfo);
                                }
                            }
                            
                            $sku->getSpecification()->setPriceUpdatedAt(new \DateTime());
                            $this->entityManager->persist($sku);
                            $syncedCount++;
                        }
                    }
                    
                    $this->entityManager->flush();
                    
                } catch (\Throwable $e) {
                    $io->note("价格同步错误: " . $e->getMessage());
                }
                
                $io->progressAdvance();
                
                // API限速
                usleep(500000); // 500ms
            }
            
            $io->progressFinish();
        }

        $io->success("商品价格同步完成，共同步 {$syncedCount} 个商品价格");
    }

    /**
     * 同步商品库存
     */
    private function syncSkuStocks(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品库存');
        $syncedCount = 0;

        foreach ($accounts as $account) {
            // 获取需要同步库存的SKU
            $criteria = ['account' => $account];
            if (!$force) {
                // 如果不强制更新，则仅同步4小时内未更新库存的商品
                $dateThreshold = new \DateTime('-4 hours');
                $criteria['stockUpdatedAt'] = ['$lt' => $dateThreshold];
            }

            $skus = $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
            
            if (empty($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步库存的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . " 个商品库存");
            
            // 批量同步库存，每批100个
            $skuBatches = array_chunk($skus, 100);
            $io->progressStart(count($skuBatches));
            
            foreach ($skuBatches as $skuBatch) {
                $skuIds = array_map(function ($sku) {
                    return $sku->getBaseInfo()->getSkuId();
                }, $skuBatch);
                
                try {
                    $params = [
                        'skuIds' => implode(',', $skuIds)
                    ];
                    
                    $result = $this->client->execute($account, 'jingdong.ctp.ware.stock.queryAreaStockState', $params);

                    if (!isset($result['result']['success']) || !$result['result']['success']) {
                        $io->note("批量获取库存失败：" . ($result['result']['errorMsg'] ?? '未知错误'));
                        $io->progressAdvance();
                        continue;
                    }

                    $stockList = $result['result']['stockStateList'] ?? [];
                    if (empty($stockList)) {
                        $io->progressAdvance();
                        continue;
                    }

                    // 用SKU ID作为键组织库存数据
                    $stockMap = [];
                    foreach ($stockList as $stockData) {
                        $skuId = $stockData['skuId'] ?? '';
                        if ($skuId) {
                            $stockMap[$skuId] = $stockData;
                        }
                    }
                    
                    // 更新商品库存信息
                    foreach ($skuBatch as $sku) {
                        $skuId = $sku->getBaseInfo()->getSkuId();
                        if (isset($stockMap[$skuId])) {
                            $stockData = $stockMap[$skuId];
                            $sku->getBaseInfo()->setStock($stockData['stockNum'] ?? 0);
                            
                            // 更新仓库信息
                            if (isset($stockData['warehouseId'])) {
                                $sku->getBaseInfo()->setWarehouseId($stockData['warehouseId']);
                                $sku->getBaseInfo()->setWarehouseName($stockData['warehouseName'] ?? null);
                            }
                            
                            $sku->getSpecification()->setStockUpdatedAt(new \DateTime());
                            $this->entityManager->persist($sku);
                            $syncedCount++;
                        }
                    }
                    
                    $this->entityManager->flush();
                    
                } catch (\Throwable $e) {
                    $io->note("库存同步错误: " . $e->getMessage());
                }
                
                $io->progressAdvance();
                
                // API限速
                usleep(500000); // 500ms
            }
            
            $io->progressFinish();
        }

        $io->success("商品库存同步完成，共同步 {$syncedCount} 个商品库存");
    }

    /**
     * 填充SKU数据
     */
    private function fillSkuData(Sku $sku, array $data): void
    {
        $this->skuService->fillSkuFromApiData($sku, [
            'skuBaseInfo' => [
                'skuId' => $sku->getBaseInfo()->getSkuId(), // 保持SKU ID不变
                'skuName' => $data['skuName'] ?? '',
                'price' => (string)($data['price'] ?? 0),
                'marketPrice' => (string)($data['marketPrice'] ?? 0),
                'categoryId' => (string)($data['category3Id'] ?? ''),
                'categoryName' => $data['category3Name'] ?? '',
                'imgUrl' => $data['imageUrl'] ?? '',
                'brandId' => (string)($data['brandId'] ?? ''),
                'brandName' => $data['brandName'] ?? '',
                'skuStatus' => $data['state'] ?? '1', // 默认上架状态
                'weight' => $data['weight'] ?? null,
                'saleAttributesList' => $data['saleAttrs'] ?? [],
                'wareType' => $data['isGlobalBuy'] ? '2' : '1',
            ],
            'imageInfos' => [
                [
                    'path' => $data['imageUrl'] ?? '',
                    'isPrimary' => '1',
                    'orderSort' => '1',
                ]
            ],
            'specifications' => $data['specs'] ?? [],
            'extAtts' => $data['attributes'] ?? [],
            'skuBigFieldInfo' => [
                'pcWdis' => $data['description'] ?? '',
            ],
        ]);
        
        // 设置库存（这是由其他API单独获取的）
        $sku->getBaseInfo()->setStock($data['stockNum'] ?? 0);
        
        // 处理详情图片，可能是字符串或数组
        if (isset($data['detailImages'])) {
            if (is_string($data['detailImages'])) {
                $sku->getImageInfo()->setDetailImages(explode(',', $data['detailImages']));
            } elseif (is_array($data['detailImages'])) {
                $sku->getImageInfo()->setDetailImages($data['detailImages']);
            }
        }
        
        // 评分和评论数
        $sku->getSpecification()->setScore($data['score'] ?? null);
        $sku->getSpecification()->setCommentCount($data['commentCount'] ?? 0);
        
        // 促销信息
        $sku->getSpecification()->setHasPromotion(isset($data['promoInfo']) && !empty($data['promoInfo']));
        if (isset($data['promoInfo']) && is_array($data['promoInfo'])) {
            $sku->getSpecification()->setPromotionInfo($data['promoInfo']);
        }
        
        // 仓库信息
        $sku->getBaseInfo()->setWarehouseId($data['warehouseId'] ?? null);
        $sku->getBaseInfo()->setWarehouseName($data['warehouseName'] ?? null);
        
        // 配送区域
        if (isset($data['deliveryAreas']) && is_array($data['deliveryAreas'])) {
            $sku->getBaseInfo()->setDeliveryAreas($data['deliveryAreas']);
        }
        
        // 全球购信息
        $sku->getBaseInfo()->setIsGlobalBuy($data['isGlobalBuy'] ?? false);
        $sku->getBaseInfo()->setOriginCountry($data['originCountry'] ?? null);
    }

    /**
     * 填充SKU详情数据
     */
    private function fillSkuDetailData(Sku $sku, array $data): void
    {
        // 处理商品参数
        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $sku->getSpecification()->setParameters($data['parameters']);
        }
        
        // 处理规格参数
        if (isset($data['specs']) && is_array($data['specs'])) {
            $sku->getBaseInfo()->setSpecs($data['specs']);
        }
        
        // 商品介绍（富文本）
        $sku->getBigFieldInfo()->setIntroduction($data['introduction'] ?? '');
        
        // 附加属性
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $sku->getSpecification()->setExtAttributes($data['attributes']);
        }
        
        // 售后服务信息
        if (isset($data['afterSales']) && is_array($data['afterSales'])) {
            $sku->getSpecification()->setAfterSalesInfo($data['afterSales']);
        }
    }
}
