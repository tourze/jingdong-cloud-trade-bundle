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
 * 获取京东商品价格信息接口
 * 
 * 参考：https://developer.jdcloud.com/article/4117
 */
class SkuPriceListProcedure extends CacheableProcedure
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

    #[MethodParam(description: "商品SKU ID列表，多个ID用逗号分隔")]
    public string $skuIds;

    #[MethodParam(description: "账户ID", optional: true)]
    public ?int $accountId = null;

    #[MethodParam(description: "是否强制刷新缓存", optional: true)]
    public bool $forceRefresh = false;

    /**
     * 解析后的SKU ID数组
     */
    private array $skuIdArray = [];

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
     * 获取商品价格信息
     */
    #[MethodTag('sku', 'price')]
    #[MethodDoc('获取京东商品价格信息')]
    #[MethodExpose('jd.sku.getPriceList')]
    public function execute(): array
    {
        // 解析SKU ID
        $this->skuIdArray = array_unique(explode(',', $this->skuIds));
        if (empty($this->skuIdArray)) {
            throw new \InvalidArgumentException('SKU ID列表不能为空');
        }
        
        if (count($this->skuIdArray) > 100) {
            throw new \InvalidArgumentException('一次最多查询100个SKU的价格');
        }
        
        // 先检查本地是否有最近的价格记录且无需强制刷新
        if (!$this->forceRefresh) {
            $priceInfo = $this->getLocalPriceInfo();
            
            // 如果所有的SKU都有最近的价格记录
            if (count($priceInfo) === count($this->skuIdArray)) {
                return [
                    'skuIds' => $this->skuIds,
                    'priceList' => $priceInfo,
                    'source' => 'local',
                ];
            }
        }
        
        // 调用京东API获取价格信息
        $params = [
            'skuIds' => $this->skuIds
        ];
        
        $account = $this->getAccount();
        
        $result = $this->client->execute($account, 'jingdong.ctp.ware.price.getSkuPriceInfoList', $params);

        if (!isset($result['result']['success']) || !$result['result']['success']) {
            throw new \RuntimeException('获取价格信息失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
        }

        $priceList = $result['result']['priceInfoVoList'] ?? [];
        if (empty($priceList)) {
            return [
                'skuIds' => $this->skuIds,
                'priceList' => [],
                'source' => 'api',
            ];
        }

        // 更新本地数据库中的价格信息
        $this->updateLocalPriceInfo($priceList, $account);
        
        // 整理返回结果
        $priceInfo = [];
        foreach ($priceList as $priceData) {
            $skuId = $priceData['skuId'] ?? '';
            
            if (empty($skuId)) {
                continue;
            }
            
            $priceItem = [
                'skuId' => $skuId,
                'price' => (float)($priceData['price'] ?? 0),
                'marketPrice' => (float)($priceData['marketPrice'] ?? 0),
                'updateTime' => date('Y-m-d H:i:s')
            ];
            
            // 如果有促销价
            if (isset($priceData['promoPrice'])) {
                $priceItem['hasPromotion'] = true;
                $priceItem['promoPrice'] = (float)$priceData['promoPrice'];
                
                if (isset($priceData['promoStartTime'])) {
                    $priceItem['promoStartTime'] = $priceData['promoStartTime'];
                }
                
                if (isset($priceData['promoEndTime'])) {
                    $priceItem['promoEndTime'] = $priceData['promoEndTime'];
                }
            } else {
                $priceItem['hasPromotion'] = false;
            }
            
            $priceInfo[] = $priceItem;
        }
        
        return [
            'skuIds' => $this->skuIds,
            'priceList' => $priceInfo,
            'source' => 'api',
        ];
    }

    /**
     * 获取本地价格信息
     */
    private function getLocalPriceInfo(): array
    {
        $result = [];
        $skus = $this->skuRepository->findBySkuIds($this->skuIdArray);
        
        foreach ($skus as $sku) {
            // 只取24小时内更新过价格的记录
            if ($sku->getPriceUpdatedAt() && time() - $sku->getPriceUpdatedAt()->getTimestamp() < 86400) {
                $priceItem = [
                    'skuId' => $sku->getSkuId(),
                    'price' => (float)$sku->getPrice(),
                    'marketPrice' => (float)$sku->getMarketPrice(),
                    'updateTime' => $sku->getPriceUpdatedAt()->format('Y-m-d H:i:s')
                ];
                
                if ($sku->getHasPromotion() && $sku->getPromoPrice()) {
                    $priceItem['hasPromotion'] = true;
                    $priceItem['promoPrice'] = (float)$sku->getPromoPrice();
                    
                    $promotionInfo = $sku->getPromotionInfo();
                    if (is_array($promotionInfo)) {
                        if (isset($promotionInfo['startTime'])) {
                            $priceItem['promoStartTime'] = $promotionInfo['startTime'];
                        }
                        
                        if (isset($promotionInfo['endTime'])) {
                            $priceItem['promoEndTime'] = $promotionInfo['endTime'];
                        }
                    }
                } else {
                    $priceItem['hasPromotion'] = false;
                }
                
                $result[] = $priceItem;
            }
        }
        
        return $result;
    }

    /**
     * 更新本地价格信息
     */
    private function updateLocalPriceInfo(array $priceList, Account $account): void
    {
        $priceMap = [];
        foreach ($priceList as $priceData) {
            $skuId = $priceData['skuId'] ?? '';
            if ($skuId) {
                $priceMap[$skuId] = $priceData;
            }
        }
        
        $skus = $this->skuRepository->findBySkuIds(array_keys($priceMap));
        $skuMap = [];
        
        foreach ($skus as $sku) {
            $skuMap[$sku->getSkuId()] = $sku;
        }
        
        foreach ($priceMap as $skuId => $priceData) {
            if (isset($skuMap[$skuId])) {
                $sku = $skuMap[$skuId];
            } else {
                // 如果数据库中没有此SKU，创建一个新的记录
                $sku = new Sku();
                $sku->setSkuId($skuId);
                $sku->setAccount($account);
                $sku->setSkuName($priceData['skuName'] ?? '未知商品');
            }
            
            $sku->setPrice((string)($priceData['price'] ?? 0));
            $sku->setMarketPrice((string)($priceData['marketPrice'] ?? 0));
            
            // 设置促销价格（如果有）
            if (isset($priceData['promoPrice'])) {
                $sku->setPromoPrice((string)$priceData['promoPrice']);
                $sku->setHasPromotion(true);
                
                if (isset($priceData['promoStartTime']) && isset($priceData['promoEndTime'])) {
                    $promoInfo = [
                        'promoPrice' => $priceData['promoPrice'],
                        'startTime' => $priceData['promoStartTime'],
                        'endTime' => $priceData['promoEndTime']
                    ];
                    $sku->setPromotionInfo($promoInfo);
                }
            } else {
                $sku->setHasPromotion(false);
                $sku->setPromoPrice(null);
            }
            
            $sku->setPriceUpdatedAt(new \DateTime());
            $this->entityManager->persist($sku);
        }
        
        $this->entityManager->flush();
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
        $key = 'jd_sku_price_' . md5($params['skuIds'] ?? '');
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
        $tags = ['jd_sku_price'];
        
        if (isset($params['skuIds'])) {
            $skuIds = explode(',', $params['skuIds']);
            foreach ($skuIds as $skuId) {
                $tags[] = 'jd_sku_' . $skuId;
            }
        }
        
        if (isset($params['accountId'])) {
            $tags[] = 'jd_sku_account_' . $params['accountId'];
        }
        
        return $tags;
    }
} 