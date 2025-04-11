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
 * 获取京东商品库存状态接口
 * 
 * 参考：https://developer.jdcloud.com/article/4117
 */
class StockStatusProcedure extends CacheableProcedure
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

    #[MethodParam(description: "地区编码", optional: true)]
    public ?string $areaId = null;

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
     * 获取商品库存状态
     */
    #[MethodTag('sku', 'stock')]
    #[MethodDoc('获取京东商品库存状态信息')]
    #[MethodExpose('jd.sku.getStockStatus')]
    public function execute(): array
    {
        // 解析SKU ID
        $this->skuIdArray = array_unique(explode(',', $this->skuIds));
        if (empty($this->skuIdArray)) {
            throw new \InvalidArgumentException('SKU ID列表不能为空');
        }
        
        if (count($this->skuIdArray) > 100) {
            throw new \InvalidArgumentException('一次最多查询100个SKU的库存');
        }
        
        // 先检查本地是否有最近的库存记录且无需强制刷新
        if (!$this->forceRefresh) {
            $stockInfo = $this->getLocalStockInfo();
            
            // 如果所有的SKU都有最近的库存记录
            if (count($stockInfo) === count($this->skuIdArray)) {
                return [
                    'skuIds' => $this->skuIds,
                    'stockList' => $stockInfo,
                    'source' => 'local',
                ];
            }
        }
        
        // 调用京东API获取库存状态
        $params = [
            'skuIds' => $this->skuIds
        ];
        
        if ($this->areaId) {
            $params['areaId'] = $this->areaId;
        }
        
        $account = $this->getAccount();
        
        $result = $this->client->execute($account, 'jingdong.ctp.ware.stock.queryAreaStockState', $params);

        if (!isset($result['result']['success']) || !$result['result']['success']) {
            throw new \RuntimeException('获取库存状态失败：' . ($result['result']['errorMsg'] ?? '未知错误'));
        }

        $stockList = $result['result']['stockStateList'] ?? [];
        if (empty($stockList)) {
            return [
                'skuIds' => $this->skuIds,
                'stockList' => [],
                'source' => 'api',
            ];
        }

        // 更新本地数据库中的库存信息
        $this->updateLocalStockInfo($stockList, $account);
        
        // 整理返回结果
        $stockInfo = [];
        foreach ($stockList as $stockData) {
            $skuId = $stockData['skuId'] ?? '';
            
            if (empty($skuId)) {
                continue;
            }
            
            $stockInfo[] = [
                'skuId' => $skuId,
                'stockNum' => (int)($stockData['stockNum'] ?? 0),
                'stockState' => $stockData['stockState'] ?? '',
                'warehouseId' => $stockData['warehouseId'] ?? '',
                'warehouseName' => $stockData['warehouseName'] ?? '',
                'areaId' => $stockData['areaId'] ?? $this->areaId,
                'areaName' => $stockData['areaName'] ?? '',
                'deliveryDays' => $stockData['deliveryDays'] ?? 0,
                'hasDeliveryLimit' => $stockData['hasDeliveryLimit'] ?? false,
                'updateTime' => date('Y-m-d H:i:s')
            ];
        }
        
        return [
            'skuIds' => $this->skuIds,
            'stockList' => $stockInfo,
            'source' => 'api',
        ];
    }

    /**
     * 获取本地库存信息
     */
    private function getLocalStockInfo(): array
    {
        $result = [];
        $skus = $this->skuRepository->findBySkuIds($this->skuIdArray);
        
        foreach ($skus as $sku) {
            // 只取4小时内更新过库存的记录
            if ($sku->getStockUpdatedAt() && time() - $sku->getStockUpdatedAt()->getTimestamp() < 14400) {
                $result[] = [
                    'skuId' => $sku->getSkuId(),
                    'stockNum' => $sku->getStock(),
                    'stockState' => $sku->getStock() > 0 ? '有货' : '无货',
                    'warehouseId' => $sku->getWarehouseId(),
                    'warehouseName' => $sku->getWarehouseName(),
                    'areaId' => $this->areaId,
                    'areaName' => '',
                    'deliveryDays' => 0,
                    'hasDeliveryLimit' => false,
                    'updateTime' => $sku->getStockUpdatedAt()->format('Y-m-d H:i:s')
                ];
            }
        }
        
        return $result;
    }

    /**
     * 更新本地库存信息
     */
    private function updateLocalStockInfo(array $stockList, Account $account): void
    {
        $stockMap = [];
        foreach ($stockList as $stockData) {
            $skuId = $stockData['skuId'] ?? '';
            if ($skuId) {
                $stockMap[$skuId] = $stockData;
            }
        }
        
        $skus = $this->skuRepository->findBySkuIds(array_keys($stockMap));
        $skuMap = [];
        
        foreach ($skus as $sku) {
            $skuMap[$sku->getSkuId()] = $sku;
        }
        
        foreach ($stockMap as $skuId => $stockData) {
            if (isset($skuMap[$skuId])) {
                $sku = $skuMap[$skuId];
            } else {
                // 如果数据库中没有此SKU，创建一个新的记录
                $sku = new Sku();
                $sku->setSkuId($skuId);
                $sku->setAccount($account);
                $sku->setSkuName($stockData['skuName'] ?? '未知商品');
            }
            
            $sku->setStock($stockData['stockNum'] ?? 0);
            $sku->setWarehouseId($stockData['warehouseId'] ?? null);
            $sku->setWarehouseName($stockData['warehouseName'] ?? null);
            $sku->setStockUpdatedAt(new \DateTime());
            
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
        $key = 'jd_sku_stock_' . md5($params['skuIds'] ?? '');
        
        if (isset($params['areaId'])) {
            $key .= '_area_' . $params['areaId'];
        }
        
        $key .= '_acc_' . ($params['accountId'] ?? 'default');
        
        return $key;
    }

    /**
     * 获取缓存时间（秒）
     */
    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return 900; // 15分钟
    }

    /**
     * 获取缓存标签
     */
    protected function getCacheTags(JsonRpcRequest $request): array
    {
        $params = $request->getParams();
        $tags = ['jd_sku_stock'];
        
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