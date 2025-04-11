<?php

namespace JingdongCloudTradeBundle\Procedure;

use AppBundle\Helper\ArrayHelper;
use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\Client;
use JsonRpcServerBundle\Attribute\MethodDoc;
use JsonRpcServerBundle\Attribute\MethodExpose;
use JsonRpcServerBundle\Attribute\MethodParam;
use JsonRpcServerBundle\Model\JsonRpcRequest;
use JsonRpcServerBundle\Procedure\CacheableProcedure;

/**
 * 获取京东商品列表接口
 *
 * 参考：https://developer.jdcloud.com/article/4117
 */
#[MethodDoc('获取京东商品列表信息')]
#[MethodExpose('jd.sku.getList')]
class SkuListProcedure extends CacheableProcedure
{
    #[MethodParam(description: "类目ID", optional: true)]
    public ?string $categoryId = null;

    #[MethodParam(description: "品牌ID", optional: true)]
    public ?string $brandId = null;

    #[MethodParam(description: "关键词", optional: true)]
    public ?string $keyword = null;

    #[MethodParam(description: "最低价格", optional: true)]
    public ?string $minPrice = null;

    #[MethodParam(description: "最高价格", optional: true)]
    public ?string $maxPrice = null;

    #[MethodParam(description: "排序字段", optional: true)]
    public ?string $sortField = null;

    #[MethodParam(description: "排序方式", optional: true)]
    public ?string $sortType = null;

    #[MethodParam(description: "页码", optional: true)]
    public int $page = 1;

    #[MethodParam(description: "每页条数", optional: true)]
    public int $pageSize = 20;

    #[MethodParam(description: "是否只获取促销商品", optional: true)]
    public bool $promotionOnly = false;

    #[MethodParam(description: "是否只获取全球购商品", optional: true)]
    public bool $globalBuyOnly = false;

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
        // 如果有足够的条件搜索本地数据库，且不强制刷新
        if (!$this->forceRefresh && $this->canSearchLocal()) {
            return $this->searchLocalSkus();
        }

        // 调用京东API获取SKU列表
        $params = $this->buildApiParams();
        $account = $this->getAccount();
        
        $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuList', $params);

        if (!isset($result['result']['success']) || !$result['result']['success']) {
            throw new \RuntimeException('获取SKU列表失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
        }

        $skuList = $result['result']['materialSkuVoList'] ?? [];
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
            'list' => array_map(function (Sku $sku) {
                return $sku->retrievePlainArray();
            }, $skuEntities),
        ];
    }
    
    /**
     * 判断是否可以搜索本地数据库
     */
    private function canSearchLocal(): bool
    {
        // 如果有特定的参数，使用本地数据库搜索
        return $this->categoryId || $this->brandId || $this->keyword || 
               ($this->minPrice && $this->maxPrice) || $this->promotionOnly || 
               $this->globalBuyOnly;
    }
    
    /**
     * 搜索本地数据库
     */
    private function searchLocalSkus(): array
    {
        $offset = ($this->page - 1) * $this->pageSize;
        $orderBy = [];
        
        if ($this->sortField && $this->sortType) {
            $orderBy[$this->sortField] = strtolower($this->sortType) === 'description' ? 'DESC' : 'ASC';
        }
        
        $total = 0;
        $skuEntities = [];
        
        if ($this->categoryId) {
            $skuEntities = $this->skuRepository->findByCategoryId(
                $this->categoryId, 
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countByCategoryId($this->categoryId);
        } elseif ($this->brandId) {
            $skuEntities = $this->skuRepository->findByBrandId(
                $this->brandId, 
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countByBrandId($this->brandId);
        } elseif ($this->keyword) {
            $skuEntities = $this->skuRepository->searchByKeyword(
                $this->keyword, 
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countByKeyword($this->keyword);
        } elseif ($this->minPrice && $this->maxPrice) {
            $skuEntities = $this->skuRepository->findByPriceRange(
                $this->minPrice, 
                $this->maxPrice, 
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countByPriceRange($this->minPrice, $this->maxPrice);
        } elseif ($this->promotionOnly) {
            $skuEntities = $this->skuRepository->findPromotionSkus(
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countPromotionSkus();
        } elseif ($this->globalBuyOnly) {
            $skuEntities = $this->skuRepository->findGlobalBuySkus(
                $orderBy, 
                $this->pageSize, 
                $offset
            );
            // 获取总数
            $total = $this->skuRepository->countGlobalBuySkus();
        }
        
        return [
            'total' => $total,
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'list' => array_map(function (Sku $sku) {
                return $sku->retrievePlainArray();
            }, $skuEntities),
        ];
    }
    
    /**
     * 构建API参数
     */
    private function buildApiParams(): array
    {
        $params = [
            'page' => $this->page,
            'pageSize' => $this->pageSize,
        ];
        
        if ($this->categoryId) {
            $params['cid3'] = $this->categoryId;
        }
        
        if ($this->brandId) {
            $params['brandId'] = $this->brandId;
        }
        
        if ($this->keyword) {
            $params['keyword'] = $this->keyword;
        }
        
        if ($this->minPrice) {
            $params['minPrice'] = $this->minPrice;
        }
        
        if ($this->maxPrice) {
            $params['maxPrice'] = $this->maxPrice;
        }
        
        if ($this->sortField && $this->sortType) {
            $params['sortField'] = $this->sortField;
            $params['sortType'] = $this->sortType;
        }
        
        if ($this->promotionOnly) {
            $params['hasPromotion'] = true;
        }
        
        if ($this->globalBuyOnly) {
            $params['isGlobalBuy'] = true;
        }
        
        return $params;
    }

    /**
     * 填充SKU数据
     */
    private function fillSkuData(Sku $sku, array $data): void
    {
        $sku->setSkuName($data['skuName'] ?? '');
        $sku->setPrice((string)($data['price'] ?? 0));
        $sku->setMarketPrice((string)($data['marketPrice'] ?? 0));
        $sku->setCategoryId((string)($data['category3Id'] ?? ''));
        $sku->setCategoryName($data['category3Name'] ?? '');
        $sku->setImageUrl($data['imageUrl'] ?? '');
        
        // 处理详情图片，可能是字符串或数组
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
        
        // 配送区域
        if (isset($data['deliveryAreas']) && is_array($data['deliveryAreas'])) {
            $sku->setDeliveryAreas($data['deliveryAreas']);
        }
        
        // 全球购信息
        $sku->setIsGlobalBuy($data['isGlobalBuy'] ?? false);
        $sku->setOriginCountry($data['originCountry'] ?? null);
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
        $key = 'jd_sku_list';
        
        if (isset($params['categoryId'])) {
            $key .= '_cat_' . $params['categoryId'];
        }
        
        if (isset($params['brandId'])) {
            $key .= '_brand_' . $params['brandId'];
        }
        
        if (isset($params['keyword'])) {
            $key .= '_kw_' . $params['keyword'];
        }
        
        if (isset($params['minPrice']) && isset($params['maxPrice'])) {
            $key .= '_price_' . $params['minPrice'] . '_' . $params['maxPrice'];
        }
        
        if (isset($params['promotionOnly']) && $params['promotionOnly']) {
            $key .= '_promo';
        }
        
        if (isset($params['globalBuyOnly']) && $params['globalBuyOnly']) {
            $key .= '_global';
        }
        
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
        $tags = ['jd_sku_list'];
        
        if (isset($params['categoryId'])) {
            $tags[] = 'jd_sku_cat_' . $params['categoryId'];
        }
        
        if (isset($params['brandId'])) {
            $tags[] = 'jd_sku_brand_' . $params['brandId'];
        }
        
        if (isset($params['accountId'])) {
            $tags[] = 'jd_sku_account_' . $params['accountId'];
        }
        
        return $tags;
    }
}
