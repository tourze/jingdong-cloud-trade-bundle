<?php

namespace JingdongCloudTradeBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\Client;
use JsonRpcServerBundle\Attribute\MethodDoc;
use JsonRpcServerBundle\Attribute\MethodExpose;
use JsonRpcServerBundle\Attribute\MethodParam;
use JsonRpcServerBundle\Attribute\MethodTag;
use JsonRpcServerBundle\Model\JsonRpcRequest;
use JsonRpcServerBundle\Procedure\CacheableProcedure;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 获取京东兄弟商品列表接口
 * 
 * 参考：https://developer.jdcloud.com/article/4117
 */
class SkuBrotherListProcedure extends CacheableProcedure
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var SkuRepository
     */
    private SkuRepository $skuRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    #[MethodParam(description: "商品SKU ID")]
    public string $skuId;

    #[MethodParam(description: "页码", optional: true)]
    public int $page = 1;

    #[MethodParam(description: "每页条数", optional: true)]
    public int $pageSize = 20;

    #[MethodParam(description: "账户ID", optional: true)]
    public ?int $accountId = null;

    #[MethodParam(description: "是否强制刷新缓存", optional: true)]
    public bool $forceRefresh = false;

    public function __construct(
        Client $client, 
        SkuRepository $skuRepository,
        EntityManagerInterface $entityManager,
        AccountRepository $accountRepository,
        CacheItemPoolInterface $cache,
        ValidatorInterface $validator
    ) {
        parent::__construct($cache, $validator);
        $this->client = $client;
        $this->skuRepository = $skuRepository;
        $this->entityManager = $entityManager;
        $this->accountRepository = $accountRepository;
    }

    /**
     * 获取兄弟商品列表
     * 兄弟商品通常指同款不同规格/颜色的商品
     */
    #[MethodTag('sku', 'brothers')]
    #[MethodDoc('获取京东商品兄弟商品列表信息')]
    #[MethodExpose('jd.sku.getBrotherList')]
    public function execute(): array
    {
        // 先检查本地是否已经有此商品
        $mainSku = $this->skuRepository->findBySkuId($this->skuId);
        if (!$mainSku) {
            // 如果本地没有此商品，先尝试获取商品详情
            $this->getSkuDetail();
            $mainSku = $this->skuRepository->findBySkuId($this->skuId);
            
            if (!$mainSku) {
                throw new \RuntimeException('商品不存在');
            }
        }
        
        // 调用京东API获取兄弟商品列表
        $params = [
            'skuId' => $this->skuId,
            'page' => $this->page,
            'pageSize' => $this->pageSize
        ];
        
        $account = $this->getAccount();
        
        $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getBrotherList', $params);

        if (!isset($result['result']['success']) || !$result['result']['success']) {
            throw new \RuntimeException('获取兄弟商品列表失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
        }

        $skuList = $result['result']['brotherSkuVoList'] ?? [];
        $total = $result['result']['total'] ?? 0;
        
        if (empty($skuList)) {
            return [
                'total' => 0,
                'page' => $this->page,
                'pageSize' => $this->pageSize,
                'list' => [],
            ];
        }

        // 保存/更新到数据库
        $skuEntities = [];
        foreach ($skuList as $skuData) {
            $skuId = $skuData['skuId'] ?? '';
            if (empty($skuId)) {
                continue;
            }
            
            $sku = $this->skuRepository->findBySkuId($skuId);
            if (!$sku) {
                $sku = new Sku();
                $sku->setSkuId($skuId);
                $sku->setAccount($account);
            }
            
            $this->fillSkuData($sku, $skuData);
            $this->entityManager->persist($sku);
            $skuEntities[] = $sku;
        }
        
        if (!empty($skuEntities)) {
            $this->entityManager->flush();
        }
        
        return [
            'total' => $total,
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'mainSku' => $mainSku->retrievePlainArray(),
            'list' => array_map(function (Sku $sku) {
                return $sku->retrievePlainArray();
            }, $skuEntities),
        ];
    }

    /**
     * 获取单个商品详情
     */
    private function getSkuDetail(): void
    {
        $params = [
            'skuId' => $this->skuId
        ];
        
        $account = $this->getAccount();
        
        $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuDetail', $params);

        if (!isset($result['result']['success']) || !$result['result']['success']) {
            throw new \RuntimeException('获取SKU详情失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
        }

        $skuData = $result['result']['skuDetailVo'] ?? null;
        if (!$skuData) {
            throw new \RuntimeException('获取SKU详情失败：返回数据为空');
        }

        // 保存到数据库
        $sku = new Sku();
        $sku->setSkuId($this->skuId);
        $sku->setAccount($account);
        
        $this->fillSkuDetailData($sku, $skuData);
        $this->entityManager->persist($sku);
        $this->entityManager->flush();
    }

    /**
     * 填充SKU基本数据
     */
    private function fillSkuData(Sku $sku, array $data): void
    {
        $sku->setSkuName($data['skuName'] ?? '');
        $sku->setPrice((string)($data['price'] ?? 0));
        $sku->setMarketPrice((string)($data['marketPrice'] ?? 0));
        $sku->setCategoryId((string)($data['category3Id'] ?? ''));
        $sku->setCategoryName($data['category3Name'] ?? '');
        $sku->setImageUrl($data['imageUrl'] ?? '');
        
        // 处理详情图片
        if (isset($data['detailImages'])) {
            if (is_string($data['detailImages'])) {
                $sku->setDetailImages(explode(',', $data['detailImages']));
            } elseif (is_array($data['detailImages'])) {
                $sku->setDetailImages($data['detailImages']);
            }
        }
        
        $sku->setBrandId((string)($data['brandId'] ?? ''));
        $sku->setBrandName($data['brandName'] ?? '');
        $sku->setState($data['state'] ?? '1'); // 默认上架状态
        $sku->setWeight($data['weight'] ?? null);
        
        // 销售属性
        if (isset($data['saleAttrs']) && is_array($data['saleAttrs'])) {
            $sku->setSaleAttrs($data['saleAttrs']);
        }
        
        $sku->setStock($data['stockNum'] ?? 0);
        $sku->setDescription($data['descriptionription'] ?? '');
        
        // 评分和评论数
        $sku->setScore($data['score'] ?? null);
        $sku->setCommentCount($data['commentCount'] ?? 0);
        
        // 促销信息
        $sku->setHasPromotion(isset($data['promoInfo']) && !empty($data['promoInfo']));
        if (isset($data['promoInfo']) && is_array($data['promoInfo'])) {
            $sku->setPromotionInfo($data['promoInfo']);
        }
        
        // 仓库信息
        $sku->setWarehouseId($data['warehouseId'] ?? null);
        $sku->setWarehouseName($data['warehouseName'] ?? null);
    }

    /**
     * 填充SKU详情数据
     */
    private function fillSkuDetailData(Sku $sku, array $data): void
    {
        // 先填充基本数据
        $this->fillSkuData($sku, $data);
        
        // 再填充详情特有的数据
        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $sku->setParameters($data['parameters']);
        }
        
        if (isset($data['specs']) && is_array($data['specs'])) {
            $sku->setSpecs($data['specs']);
        }
        
        $sku->setIntroduction($data['introduction'] ?? '');
        
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $sku->setAttributes($data['attributes']);
        }
        
        if (isset($data['afterSales']) && is_array($data['afterSales'])) {
            $sku->setAfterSalesInfo($data['afterSales']);
        }
    }

    /**
     * 获取账户
     */
    private function getAccount(): Account
    {
        if ($this->accountId) {
            $account = $this->accountRepository->find($this->accountId);
            if (!$account) {
                throw new \RuntimeException('账户不存在');
            }
            return $account;
        }
        
        // 如果没有指定accountId，获取默认账户
        $account = $this->accountRepository->findOneBy([], ['id' => 'ASC']);
        if (!$account) {
            throw new \RuntimeException('系统中没有配置京东账户');
        }
        
        return $account;
    }

    /**
     * 获取缓存键
     */
    protected function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        $key = 'jd_sku_brothers_' . ($params['skuId'] ?? 'unknown');
        $key .= '_p' . ($params['page'] ?? 1) . '_' . ($params['pageSize'] ?? 20);
        $key .= '_acc_' . ($params['accountId'] ?? 'default');
        
        return $key;
    }

    /**
     * 获取缓存时间（秒）
     */
    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return 3600; // 1小时
    }

    /**
     * 获取缓存标签
     */
    protected function getCacheTags(JsonRpcRequest $request): array
    {
        $params = $request->getParams();
        $tags = ['jd_sku_brothers'];
        
        if (isset($params['skuId'])) {
            $tags[] = 'jd_sku_' . $params['skuId'];
        }
        
        if (isset($params['accountId'])) {
            $tags[] = 'jd_sku_account_' . $params['accountId'];
        }
        
        return $tags;
    }
} 