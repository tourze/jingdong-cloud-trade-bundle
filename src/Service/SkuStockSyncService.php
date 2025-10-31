<?php

namespace JingdongCloudTradeBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class SkuStockSyncService
{
    public function __construct(
        private readonly Client $client,
        private readonly EntityManagerInterface $entityManager,
        private readonly SkuRepository $skuRepository,
    ) {
    }

    /**
     * 同步商品库存信息
     *
     * 不考虑并发：本方法主要用于单机后台任务执行，不涉及并发场景
     * @param Account[] $accounts
     */
    public function syncSkuStocks(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品库存');
        $syncedCount = 0;

        foreach ($accounts as $account) {
            $skus = $this->getSkusForStockSync($account, $limit, $force);
            if (0 === count($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步库存的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . ' 个商品库存');
            $syncedCount += $this->processStockBatches($io, $account, $skus);
        }

        $io->success("商品库存同步完成，共同步 {$syncedCount} 个商品库存");
    }

    /**
     * 获取需要同步库存的商品
     *
     * 不考虑并发：数据查询操作，无并发冲突风险
     * @param mixed $account
     * @return array<Sku>
     */
    /**
     * @return Sku[]
     */
    private function getSkusForStockSync(Account $account, int $limit, bool $force): array
    {
        $criteria = ['account' => $account];
        if (!$force) {
            $dateThreshold = new \DateTime('-4 hours');
            $criteria['stockUpdatedAt'] = ['$lt' => $dateThreshold];
        }

        return $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
    }

    /**
     * 处理库存批次
     *
     * 不考虑并发：按顺序处理批次，无并发操作
     * @param mixed $account
     * @param array<Sku> $skus
     */
    /**
     * @param Sku[] $skus
     */
    private function processStockBatches(SymfonyStyle $io, Account $account, array $skus): int
    {
        $syncedCount = 0;
        $skuBatches = array_chunk($skus, 100);
        $io->progressStart(count($skuBatches));

        foreach ($skuBatches as $skuBatch) {
            $syncedCount += $this->processSingleStockBatch($io, $account, $skuBatch);
            $io->progressAdvance();
            usleep(500000); // API限速
        }

        $io->progressFinish();

        return $syncedCount;
    }

    /**
     * 处理单个库存批次
     *
     * 不考虑并发：单次API调用，无并发风险
     * @param mixed $account
     * @param array<Sku> $skuBatch
     */
    /**
     * @param Sku[] $skuBatch
     */
    private function processSingleStockBatch(SymfonyStyle $io, Account $account, array $skuBatch): int
    {
        $skuIds = array_map(fn ($sku) => $sku->getBaseInfo()->getSkuId(), $skuBatch);

        try {
            $result = $this->fetchStockDataFromApi($account, $skuIds);

            if (!$this->isApiResponseSuccessful($result)) {
                $this->handleStockApiError($io, $result);

                return 0;
            }

            $stockList = $this->extractStockListFromResult($result);

            return $this->updateSkuStocks($skuBatch, $stockList);
        } catch (\Throwable $e) {
            $io->note('库存同步错误: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * 从 API 获取库存数据
     *
     * @param string[] $skuIds
     * @return array<string, mixed>
     */
    private function fetchStockDataFromApi(Account $account, array $skuIds): array
    {
        $params = ['skuIds' => implode(',', $skuIds)];

        return $this->client->execute($account, 'jingdong.ctp.ware.stock.queryAreaStockState', $params);
    }

    /**
     * 处理库存 API 错误
     *
     * @param array<string, mixed> $result
     */
    private function handleStockApiError(SymfonyStyle $io, array $result): void
    {
        $errorMsg = '未知错误';
        if (isset($result['result']) && is_array($result['result']) && isset($result['result']['errorMsg'])) {
            $errorMsgValue = $result['result']['errorMsg'];
            $errorMsg = is_string($errorMsgValue) || is_numeric($errorMsgValue) ? (string) $errorMsgValue : '未知错误';
        }
        $io->note('批量获取库存失败：' . $errorMsg);
    }

    /**
     * 从 API 结果中提取库存列表
     *
     * @param array<string, mixed> $result
     * @return array<int, array<string, mixed>>
     */
    private function extractStockListFromResult(array $result): array
    {
        if (!isset($result['result']) || !is_array($result['result']) || !isset($result['result']['stockStateList'])) {
            return [];
        }

        $stockListValue = $result['result']['stockStateList'];

        if (!is_array($stockListValue)) {
            return [];
        }

        return $this->validateStockListArray($stockListValue);
    }

    /**
     * @param array<mixed> $stockList
     * @return array<int, array<string, mixed>>
     */
    private function validateStockListArray(array $stockList): array
    {
        $validatedList = [];
        foreach ($stockList as $index => $item) {
            if (is_int($index) && is_array($item)) {
                $validatedList[$index] = $this->validateStockItem($item);
            }
        }

        return $validatedList;
    }

    /**
     * @param array<mixed> $item
     * @return array<string, mixed>
     */
    private function validateStockItem(array $item): array
    {
        $validatedItem = [];
        foreach ($item as $key => $value) {
            if (is_string($key)) {
                $validatedItem[$key] = $value;
            }
        }

        return $validatedItem;
    }

    /**
     * 更新商品库存
     *
     * 不考虑并发：批量数据库操作，事务内执行
     * @param array<Sku> $skuBatch
     * @param array<mixed> $stockList
     */
    private function updateSkuStocks(array $skuBatch, array $stockList): int
    {
        if ([] === $stockList) {
            return 0;
        }

        $stockMap = $this->buildStockMap($stockList);
        $syncedCount = 0;

        foreach ($skuBatch as $sku) {
            $skuId = $sku->getBaseInfo()->getSkuId();
            if (isset($stockMap[$skuId])) {
                $this->applyStockData($sku, $stockMap[$skuId]);
                $this->entityManager->persist($sku);
                ++$syncedCount;
            }
        }

        $this->entityManager->flush();

        return $syncedCount;
    }

    /**
     * 构建库存映射
     *
     * 不考虑并发：纯数据处理，无状态变更
     * @param array<mixed> $stockList
     * @return array<string, array<string, mixed>>
     */
    private function buildStockMap(array $stockList): array
    {
        $stockMap = [];
        foreach ($stockList as $stockData) {
            if (!is_array($stockData)) {
                continue;
            }
            $skuIdValue = $stockData['skuId'] ?? '';
            $skuId = is_string($skuIdValue) || is_numeric($skuIdValue) ? (string) $skuIdValue : '';
            if ('' !== $skuId) {
                /** @var array<string, mixed> $stockData */
                $stockMap[$skuId] = $stockData;
            }
        }

        return $stockMap;
    }

    /**
     * 应用库存数据
     *
     * 不考虑并发：对象属性设置，无外部依赖
     * @param array<string, mixed> $stockData
     */
    private function applyStockData(Sku $sku, array $stockData): void
    {
        $stockNum = 0;
        if (isset($stockData['stockNum'])) {
            $stockNumValue = $stockData['stockNum'];
            $stockNum = is_numeric($stockNumValue) ? (int) $stockNumValue : 0;
        }
        $sku->getBaseInfo()->setStock($stockNum);

        // 更新仓库信息
        if (isset($stockData['warehouseId'])) {
            $warehouseIdValue = $stockData['warehouseId'];
            $warehouseId = is_string($warehouseIdValue) || is_numeric($warehouseIdValue) ? (string) $warehouseIdValue : null;
            $warehouseName = null;
            if (isset($stockData['warehouseName'])) {
                $warehouseNameValue = $stockData['warehouseName'];
                $warehouseName = is_string($warehouseNameValue) || is_numeric($warehouseNameValue) ? (string) $warehouseNameValue : null;
            }
            $sku->getBaseInfo()->setWarehouseId($warehouseId);
            $sku->getBaseInfo()->setWarehouseName($warehouseName);
        }

        $sku->getSpecification()->setStockUpdateTime(new \DateTime());
    }

    /**
     * @param array<string, mixed> $result
     */
    private function isApiResponseSuccessful(array $result): bool
    {
        if (!isset($result['result']) || !is_array($result['result'])) {
            return false;
        }
        $resultData = $result['result'];

        return isset($resultData['success']) && (bool) $resultData['success'];
    }
}
