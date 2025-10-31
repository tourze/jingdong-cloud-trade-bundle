<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\DTO\ApiResponse;
use JingdongCloudTradeBundle\DTO\SyncOptions;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Service\Client;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class SkuBasicInfoSyncService
{
    public function __construct(
        private Client $client,
        private EntityManagerInterface $entityManager,
        private SkuDataProcessor $skuDataProcessor,
    ) {
    }

    /**
     * @param Account[] $accounts
     * @param array<string, mixed> $options
     */
    public function syncBasicSkuInfo(SymfonyStyle $io, array $accounts, array $options): int
    {
        $io->section('正在同步商品基本信息');
        $syncOptions = SyncOptions::fromArray($options);
        $totalSynced = 0;

        foreach ($accounts as $account) {
            $io->info("账户: {$account->getName()} (ID: {$account->getId()})");
            $syncedCount = $this->syncAccountSkus($io, $account, $syncOptions);
            $totalSynced += $syncedCount;
            $io->success("账户 {$account->getName()} 同步完成，共同步 {$syncedCount} 个商品");
        }

        return $totalSynced;
    }

    private function syncAccountSkus(SymfonyStyle $io, Account $account, SyncOptions $options): int
    {
        $syncedCount = 0;
        $page = 1;
        $pageSize = 100;

        try {
            while ($syncedCount < $options->limit) {
                $params = $options->buildRequestParams($page, $pageSize);
                $response = $this->fetchSkuList($io, $account, $params);

                if (!$response->success) {
                    if (null !== $response->errorMsg) {
                        $io->error($response->errorMsg);
                    }
                    break;
                }

                if (!$response->hasData()) {
                    $io->text('没有获取到更多商品数据');
                    break;
                }

                $processedCount = $this->processSkuList($io, $account, $response->skuList, $options, $syncedCount);
                $syncedCount += $processedCount;

                $hasMore = $response->total > $page * $pageSize;
                if (!$hasMore) {
                    break;
                }
                ++$page;
            }
        } catch (\Throwable $e) {
            $io->error('同步发生错误: ' . $e->getMessage());
        }

        return $syncedCount;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function fetchSkuList(SymfonyStyle $io, Account $account, array $params): ApiResponse
    {
        $page = $params['page'] ?? 1;
        $pageNumber = is_int($page) || is_string($page) ? (string) $page : '1';
        $io->text("正在获取第 {$pageNumber} 页商品列表...");

        $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuList', $params);

        return ApiResponse::fromArray($result);
    }

    /**
     * @param array<string, mixed> $skuList
     */
    private function processSkuList(SymfonyStyle $io, Account $account, array $skuList, SyncOptions $options, int $currentCount): int
    {
        $io->progressStart(count($skuList));
        $processedCount = 0;

        foreach ($skuList as $skuData) {
            if ($currentCount + $processedCount >= $options->limit) {
                break;
            }

            if (!is_array($skuData)) {
                $io->progressAdvance();
                continue;
            }

            // 确保数组键是字符串类型
            $validatedSkuData = [];
            foreach ($skuData as $key => $value) {
                if (is_string($key)) {
                    $validatedSkuData[$key] = $value;
                }
            }

            if ($this->processSingleSku($account, $validatedSkuData, $options)) {
                ++$processedCount;
            }

            $io->progressAdvance();

            // 每100个商品保存一次
            if (($currentCount + $processedCount) % 100 === 0) {
                $this->entityManager->flush();
            }
        }

        $io->progressFinish();
        $this->entityManager->flush();

        return $processedCount;
    }

    /**
     * @param array<string, mixed> $skuData
     */
    private function processSingleSku(Account $account, array $skuData, SyncOptions $options): bool
    {
        $success = $this->skuDataProcessor->processSku($account, $skuData, $options);

        if ($success) {
            // Note: Actual Sku entity would be persisted inside skuDataProcessor
            // This is just a placeholder to maintain interface compatibility
        }

        return $success;
    }
}
