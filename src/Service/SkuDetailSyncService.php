<?php

namespace JingdongCloudTradeBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\Client;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class SkuDetailSyncService
{
    public function __construct(
        private Client $client,
        private EntityManagerInterface $entityManager,
        private SkuRepository $skuRepository,
    ) {
    }

    /**
     * @param Account[] $accounts
     */
    public function syncSkuDetails(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品详情');
        $syncedCount = 0;
        $hasError = false;

        foreach ($accounts as $account) {
            $skus = $this->getSkusForDetailSync($account, $limit, $force);
            if (0 === count($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步详情的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . ' 个商品详情');
            $result = $this->processSkuDetails($io, $account, $skus);
            $syncedCount += $result['synced'];
            $hasError = $hasError || (bool) $result['hasError'];
        }

        $this->showDetailSyncResult($io, $syncedCount, $hasError);
    }

    /**
     * @return Sku[]
     */
    private function getSkusForDetailSync(Account $account, int $limit, bool $force): array
    {
        $criteria = ['account' => $account];
        if (!$force) {
            $dateThreshold = new \DateTime('-3 days');
            $criteria['detailUpdatedAt'] = ['$lt' => $dateThreshold];
        }

        return $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
    }

    /**
     * @param Sku[] $skus
     *
     * @return array{synced: int, hasError: bool}
     */
    private function processSkuDetails(SymfonyStyle $io, Account $account, array $skus): array
    {
        $io->progressStart(count($skus));
        $syncedCount = 0;
        $hasError = false;

        foreach ($skus as $sku) {
            $result = $this->syncSingleSkuDetail($io, $account, $sku);
            if ((bool) $result['synced']) {
                ++$syncedCount;
            }
            if ((bool) $result['hasError']) {
                $hasError = true;
            }

            $io->progressAdvance();

            // 每50个商品保存一次
            if (($syncedCount + 1) % 50 === 0) {
                $this->entityManager->flush();
            }

            // API限速，避免请求过快
            usleep(500000); // 500ms
        }

        $io->progressFinish();
        $this->entityManager->flush();

        return ['synced' => $syncedCount, 'hasError' => $hasError];
    }

    /**
     * @return array{synced: bool, hasError: bool}
     */
    private function syncSingleSkuDetail(SymfonyStyle $io, Account $account, Sku $sku): array
    {
        try {
            $skuData = $this->fetchSkuDetailFromApi($account, $sku);

            if (null === $skuData) {
                $this->logSkuDetailFetchError($io, $sku, '数据获取失败');

                return ['synced' => false, 'hasError' => false];
            }

            $this->updateSkuWithDetailData($sku, $skuData);

            return ['synced' => true, 'hasError' => false];
        } catch (\Throwable $e) {
            $this->logSkuDetailFetchError($io, $sku, $e->getMessage());

            return ['synced' => false, 'hasError' => true];
        }
    }

    /**
     * @return array<mixed>|null
     */
    private function fetchSkuDetailFromApi(Account $account, Sku $sku): ?array
    {
        $params = ['skuId' => $sku->getBaseInfo()->getSkuId()];
        $result = $this->client->execute($account, 'jingdong.ctp.ware.sku.getSkuDetail', $params);

        if (!$this->isApiResponseSuccessful($result)) {
            return null;
        }

        return $this->extractSkuDetailData($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<mixed>|null
     */
    private function extractSkuDetailData(array $result): ?array
    {
        if (!isset($result['result']) || !is_array($result['result'])) {
            return null;
        }

        $resultData = $result['result'];
        if (!isset($resultData['skuDetailVo'])) {
            return null;
        }

        $skuDataValue = $resultData['skuDetailVo'];

        return is_array($skuDataValue) ? $skuDataValue : null;
    }

    /**
     * @param array<mixed> $skuData
     */
    private function updateSkuWithDetailData(Sku $sku, array $skuData): void
    {
        // 验证并转换为字符串键数组
        $validatedData = [];
        foreach ($skuData as $key => $value) {
            if (is_string($key)) {
                $validatedData[$key] = $value;
            }
        }

        $this->fillSkuDetailData($sku, $validatedData);
        $sku->setDetailUpdateTime(new \DateTimeImmutable());
        $this->entityManager->persist($sku);
    }

    private function logSkuDetailFetchError(SymfonyStyle $io, Sku $sku, string $errorMessage): void
    {
        $skuId = $sku->getBaseInfo()->getSkuId();
        $io->note("商品 {$skuId} 详情同步错误: {$errorMessage}");
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuDetailData(Sku $sku, array $data): void
    {
        $this->fillSkuParameters($sku, $data);
        $this->fillSkuSpecs($sku, $data);
        $this->fillSkuIntroduction($sku, $data);
        $this->fillSkuExtAttributes($sku, $data);
        $this->fillSkuAfterSalesInfo($sku, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuParameters(Sku $sku, array $data): void
    {
        if (!isset($data['parameters']) || !is_array($data['parameters'])) {
            return;
        }

        /** @var array<int, array<string, mixed>> $parameters */
        $parameters = array_values($data['parameters']);
        $sku->getSpecification()->setParameters($parameters);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuSpecs(Sku $sku, array $data): void
    {
        if (!isset($data['specs']) || !is_array($data['specs'])) {
            return;
        }

        /** @var array<string, mixed> $specs */
        $specs = $data['specs'];
        $sku->getBaseInfo()->setSpecs($specs);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuIntroduction(Sku $sku, array $data): void
    {
        $introduction = '';
        if (isset($data['introduction'])) {
            $introValue = $data['introduction'];
            $introduction = is_string($introValue) || is_numeric($introValue) ? (string) $introValue : '';
        }
        $sku->getBigFieldInfo()->setIntroduction($introduction);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuExtAttributes(Sku $sku, array $data): void
    {
        if (!isset($data['attributes']) || !is_array($data['attributes'])) {
            return;
        }

        /** @var array<int, array<string, mixed>> $attributes */
        $attributes = array_values($data['attributes']);
        $sku->getSpecification()->setExtAttributes($attributes);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuAfterSalesInfo(Sku $sku, array $data): void
    {
        if (!isset($data['afterSales']) || !is_array($data['afterSales'])) {
            return;
        }

        /** @var array<int, array<string, mixed>> $afterSales */
        $afterSales = array_values($data['afterSales']);
        $sku->getSpecification()->setAfterSalesInfo($afterSales);
    }

    private function showDetailSyncResult(SymfonyStyle $io, int $syncedCount, bool $hasError): void
    {
        if ($hasError) {
            $io->warning('商品详情同步完成，但有部分商品同步失败');
        } else {
            $io->success("商品详情同步完成，共同步 {$syncedCount} 个商品详情");
        }
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
