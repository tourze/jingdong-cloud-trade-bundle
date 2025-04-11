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

/**
 * 获取京东商品详情接口
 * 
 * 参考：https://developer.jdcloud.com/article/4117
 */
#[MethodTag('sku', 'detail')]
#[MethodDoc('获取京东商品详情信息')]
#[MethodExpose('jd.sku.getDetail')]
class SkuDetailProcedure extends CacheableProcedure
{

    #[MethodParam(description: "商品SKU ID")]
    public string $skuId;

    #[MethodParam(description: "账户ID", optional: true)]
    public ?int $accountId = null;

    #[MethodParam(description: "是否强制刷新缓存", optional: true)]
    public bool $forceRefresh = false;

    public function __construct(
        private readonly Client $client,
        private readonly SkuRepository $skuRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function execute(): array
    {
        // 先检查本地是否已经有缓存且无需强制刷新
        if (!$this->forceRefresh) {
            $sku = $this->skuRepository->findBySkuId($this->skuId);
            if ($sku) {
                return $sku->retrievePlainArray();
            }
        }

        // 调用京东API获取SKU详情
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

        // 保存/更新到数据库
        $sku = $this->skuRepository->findBySkuId($this->skuId);
        if (!$sku) {
            $sku = new Sku();
            $sku->setSkuId($this->skuId);
            $sku->setAccount($account);
        }
        
        $this->fillSkuDetailData($sku, $skuData);
        $this->entityManager->persist($sku);
        $this->entityManager->flush();
        
        return $sku->retrievePlainArray();
    }

    /**
     * 填充SKU详情数据
     */
    private function fillSkuDetailData(Sku $sku, array $data): void
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
        
        // 处理商品参数
        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $sku->setParameters($data['parameters']);
        }
        
        $sku->setBrandId((string)($data['brandId'] ?? ''));
        $sku->setBrandName($data['brandName'] ?? '');
        $sku->setState($data['state'] ?? '1'); // 默认上架状态
        $sku->setWeight($data['weight'] ?? null);
        
        // 处理销售属性
        if (isset($data['saleAttrs']) && is_array($data['saleAttrs'])) {
            $sku->setSaleAttrs($data['saleAttrs']);
        }
        
        // 处理规格参数
        if (isset($data['specs']) && is_array($data['specs'])) {
            $sku->setSpecs($data['specs']);
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
        
        // 配送区域
        if (isset($data['deliveryAreas']) && is_array($data['deliveryAreas'])) {
            $sku->setDeliveryAreas($data['deliveryAreas']);
        }
        
        // 全球购信息
        $sku->setIsGlobalBuy($data['isGlobalBuy'] ?? false);
        $sku->setOriginCountry($data['originCountry'] ?? null);
        
        // 商品介绍（富文本）
        $sku->setIntroduction($data['introduction'] ?? '');
        
        // 附加属性（可能包含其他重要属性）
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $sku->setAttributes($data['attributes']);
        }
        
        // 售后服务信息
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
        $key = 'jd_sku_detail_' . ($params['skuId'] ?? 'unknown');
        $key .= '_acc_' . ($params['accountId'] ?? 'default');
        
        return $key;
    }

    /**
     * 获取缓存时间（秒）
     */
    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return 1800; // 30分钟
    }

    /**
     * 获取缓存标签
     */
    protected function getCacheTags(JsonRpcRequest $request): array
    {
        $params = $request->getParams();
        $tags = ['jd_sku_detail'];
        
        if (isset($params['skuId'])) {
            $tags[] = 'jd_sku_' . $params['skuId'];
        }
        
        if (isset($params['accountId'])) {
            $tags[] = 'jd_sku_account_' . $params['accountId'];
        }
        
        return $tags;
    }
}
