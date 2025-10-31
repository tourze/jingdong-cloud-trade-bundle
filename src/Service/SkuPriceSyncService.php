<?php

namespace JingdongCloudTradeBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class SkuPriceSyncService
{
    public function __construct(
        private readonly Client $client,
        private readonly EntityManagerInterface $entityManager,
        private readonly SkuRepository $skuRepository,
    ) {
    }

    /**
     * @param Account[] $accounts
     */
    public function syncSkuPrices(SymfonyStyle $io, array $accounts, int $limit, bool $force): void
    {
        $io->section('正在同步商品价格');
        $syncedCount = 0;

        foreach ($accounts as $account) {
            $skus = $this->getSkusForPriceSync($account, $limit, $force);
            if (0 === count($skus)) {
                $io->info("账户 {$account->getName()} 没有需要同步价格的商品");
                continue;
            }

            $io->info("账户 {$account->getName()} 需要同步 " . count($skus) . ' 个商品价格');
            $syncedCount += $this->processPriceBatches($io, $account, $skus);
        }

        $io->success("商品价格同步完成，共同步 {$syncedCount} 个商品价格");
    }

    /**
     * @return Sku[]
     */
    private function getSkusForPriceSync(Account $account, int $limit, bool $force): array
    {
        $criteria = ['account' => $account];
        if (!$force) {
            $dateThreshold = new \DateTime('-1 days');
            $criteria['priceUpdatedAt'] = ['$lt' => $dateThreshold];
        }

        return $this->skuRepository->findBy($criteria, ['updateTime' => 'DESC'], $limit);
    }

    /**
     * @param Sku[] $skus
     */
    private function processPriceBatches(SymfonyStyle $io, Account $account, array $skus): int
    {
        $syncedCount = 0;
        $skuBatches = array_chunk($skus, 100);
        $io->progressStart(count($skuBatches));

        foreach ($skuBatches as $skuBatch) {
            $syncedCount += $this->processSinglePriceBatch($io, $account, $skuBatch);
            $io->progressAdvance();
            usleep(500000); // API限速
        }

        $io->progressFinish();

        return $syncedCount;
    }

    /**
     * @param Sku[] $skuBatch
     */
    private function processSinglePriceBatch(SymfonyStyle $io, Account $account, array $skuBatch): int
    {
        try {
            $priceList = $this->fetchPricesFromApi($account, $skuBatch);

            if (null === $priceList) {
                $this->logPriceFetchError($io, '批量获取价格失败');

                return 0;
            }

            return $this->updateSkuPrices($skuBatch, $priceList);
        } catch (\Throwable $e) {
            $this->logPriceFetchError($io, $e->getMessage());

            return 0;
        }
    }

    /**
     * @param Sku[] $skuBatch
     * @return array<mixed>|null
     */
    private function fetchPricesFromApi(Account $account, array $skuBatch): ?array
    {
        $skuIds = array_map(fn ($sku) => $sku->getBaseInfo()->getSkuId(), $skuBatch);
        $params = ['skuIds' => implode(',', $skuIds)];

        $result = $this->client->execute($account, 'jingdong.ctp.ware.price.getSkuPriceInfoList', $params);

        if (!$this->isApiResponseSuccessful($result)) {
            return null;
        }

        return $this->extractPriceListData($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<mixed>|null
     */
    private function extractPriceListData(array $result): ?array
    {
        if (!isset($result['result']) || !is_array($result['result'])) {
            return null;
        }

        $resultData = $result['result'];
        if (!isset($resultData['priceInfoVoList'])) {
            return [];
        }

        $priceListValue = $resultData['priceInfoVoList'];

        return is_array($priceListValue) ? $priceListValue : [];
    }

    private function logPriceFetchError(SymfonyStyle $io, string $errorMessage): void
    {
        $io->note('价格同步错误: ' . $errorMessage);
    }

    /**
     * @param Sku[] $skuBatch
     * @param array<mixed> $priceList
     */
    private function updateSkuPrices(array $skuBatch, array $priceList): int
    {
        if (0 === count($priceList)) {
            return 0;
        }

        $priceMap = $this->buildPriceMap($priceList);
        $syncedCount = 0;

        foreach ($skuBatch as $sku) {
            if (!$sku instanceof Sku) {
                continue;
            }
            $skuId = $sku->getBaseInfo()->getSkuId();
            if (isset($priceMap[$skuId])) {
                $this->applyPriceData($sku, $priceMap[$skuId]);
                $this->entityManager->persist($sku);
                ++$syncedCount;
            }
        }

        $this->entityManager->flush();

        return $syncedCount;
    }

    /**
     * @param array<mixed> $priceList
     * @return array<string, array<string, mixed>>
     */
    private function buildPriceMap(array $priceList): array
    {
        $priceMap = [];
        foreach ($priceList as $priceData) {
            if (!is_array($priceData)) {
                continue;
            }
            $skuIdValue = $priceData['skuId'] ?? '';
            $skuId = is_string($skuIdValue) || is_numeric($skuIdValue) ? (string) $skuIdValue : '';
            if ('' !== $skuId) {
                /** @var array<string, mixed> $priceData */
                $priceMap[$skuId] = $priceData;
            }
        }

        return $priceMap;
    }

    /**
     * @param array<string, mixed> $priceData
     */
    private function applyPriceData(Sku $sku, array $priceData): void
    {
        $price = '0';
        if (isset($priceData['price'])) {
            $priceValue = $priceData['price'];
            $price = is_string($priceValue) || is_numeric($priceValue) ? (string) $priceValue : '0';
        }
        $marketPrice = '0';
        if (isset($priceData['marketPrice'])) {
            $marketPriceValue = $priceData['marketPrice'];
            $marketPrice = is_string($marketPriceValue) || is_numeric($marketPriceValue) ? (string) $marketPriceValue : '0';
        }
        $sku->getBaseInfo()->setPrice($price);
        $sku->getBaseInfo()->setMarketPrice($marketPrice);

        $this->applyPromotionPriceData($sku, $priceData);
        $sku->getSpecification()->setPriceUpdateTime(new \DateTime());
    }

    /**
     * @param array<string, mixed> $priceData
     */
    private function applyPromotionPriceData(Sku $sku, array $priceData): void
    {
        if (!isset($priceData['promoPrice'])) {
            return;
        }

        $promoPrice = '0';
        if (isset($priceData['promoPrice'])) {
            $promoPriceValue = $priceData['promoPrice'];
            $promoPrice = is_string($promoPriceValue) || is_numeric($promoPriceValue) ? (string) $promoPriceValue : '0';
        }
        $sku->getSpecification()->setPromoPrice($promoPrice);
        $sku->getSpecification()->setHasPromotion(true);

        if (isset($priceData['promoStartTime'], $priceData['promoEndTime'])) {
            $promoInfo = [
                'promoPrice' => $priceData['promoPrice'],
                'startTime' => $priceData['promoStartTime'],
                'endTime' => $priceData['promoEndTime'],
            ];
            $sku->getSpecification()->setPromotionInfo([$promoInfo]);
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
