<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\DTO\SyncOptions;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\SkuRepository;

/**
 * SKU数据处理器，专门处理SKU的创建、更新和数据填充
 */
readonly class SkuDataProcessor
{
    public function __construct(
        private SkuRepository $skuRepository,
        private SkuService $skuService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理单个SKU数据
     *
     * @param array<string, mixed> $skuData
     */
    public function processSku(Account $account, array $skuData, SyncOptions $options): bool
    {
        $skuId = $this->extractSkuId($skuData);
        if (null === $skuId) {
            return false;
        }

        $sku = $this->getOrCreateSku($skuId, $account);

        if (!$this->shouldUpdateSku($sku, $options->force)) {
            return false;
        }

        $this->fillSkuData($sku, $skuData);
        $this->entityManager->persist($sku);

        return true;
    }

    /**
     * @param array<string, mixed> $skuData
     */
    private function extractSkuId(array $skuData): ?string
    {
        $skuId = $skuData['skuId'] ?? '';
        if (!is_string($skuId) || '' === $skuId) {
            return null;
        }

        return $skuId;
    }

    private function getOrCreateSku(string $skuId, Account $account): Sku
    {
        $sku = $this->skuRepository->findBySkuId($skuId);

        if (null === $sku) {
            $sku = new Sku();
            $sku->getBaseInfo()->setSkuId($skuId);
            $sku->setAccount($account);
        }

        return $sku;
    }

    private function shouldUpdateSku(Sku $sku, bool $force): bool
    {
        if ($force) {
            return true;
        }

        $updateTime = $sku->getUpdateTime();
        if (null === $updateTime) {
            return true;
        }

        // 如果商品在1天内已更新过，则跳过
        return (time() - $updateTime->getTimestamp()) >= 86400;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillSkuData(Sku $sku, array $data): void
    {
        $baseInfoData = $this->prepareBaseInfoData($sku, $data);
        $this->skuService->fillSkuFromApiData($sku, $baseInfoData);
        $this->fillAdditionalData($sku, $data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function prepareBaseInfoData(Sku $sku, array $data): array
    {
        return [
            'skuBaseInfo' => $this->buildSkuBaseInfo($sku, $data),
            'specifications' => $data['specs'] ?? [],
            'extAtts' => $data['attributes'] ?? [],
            'skuBigFieldInfo' => [
                'pcWdis' => $data['description'] ?? '',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function buildSkuBaseInfo(Sku $sku, array $data): array
    {
        return [
            'skuId' => $sku->getBaseInfo()->getSkuId(),
            'skuName' => $this->extractStringValue($data, 'skuName'),
            'price' => $this->extractScalarAsString($data, 'price', '0'),
            'marketPrice' => $this->extractScalarAsString($data, 'marketPrice', '0'),
            'categoryId' => $this->extractScalarAsString($data, 'category3Id', ''),
            'categoryName' => $this->extractStringValue($data, 'category3Name'),
            'imgUrl' => $this->extractStringValue($data, 'imageUrl'),
            'brandId' => $this->extractScalarAsString($data, 'brandId', ''),
            'brandName' => $this->extractStringValue($data, 'brandName'),
            'skuStatus' => $data['state'] ?? '1',
            'weight' => $data['weight'] ?? null,
            'saleAttributesList' => $data['saleAttrs'] ?? [],
            'wareType' => (bool) ($data['isGlobalBuy'] ?? false) ? '2' : '1',
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractStringValue(array $data, string $key): string
    {
        $value = $data[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractScalarAsString(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillAdditionalData(Sku $sku, array $data): void
    {
        // 设置库存
        $stockNum = $data['stockNum'] ?? 0;
        if (!is_int($stockNum) && !is_string($stockNum) && !is_float($stockNum)) {
            $stockNum = 0;
        }
        $sku->getBaseInfo()->setStock((int) $stockNum);

        // 设置主图片
        $imageInfos = [
            [
                'path' => $data['imageUrl'] ?? '',
                'isPrimary' => '1',
                'orderSort' => '1',
            ],
        ];
        $this->skuService->fillSkuFromApiData($sku, ['imageInfos' => $imageInfos]);

        // 处理详情图片
        $this->fillDetailImages($sku, $data);
        $this->fillMetrics($sku, $data);
        $this->fillLogistics($sku, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillDetailImages(Sku $sku, array $data): void
    {
        if (!isset($data['detailImages'])) {
            return;
        }

        $detailImages = $this->extractDetailImages($data['detailImages']);
        $sku->getImageInfo()->setDetailImages($detailImages);
    }

    /**
     * @param mixed $detailImagesData
     * @return string[]
     */
    private function extractDetailImages($detailImagesData): array
    {
        if (is_string($detailImagesData)) {
            return explode(',', $detailImagesData);
        }

        if (!is_array($detailImagesData)) {
            return [];
        }

        $validImages = [];
        foreach ($detailImagesData as $image) {
            if (is_string($image)) {
                $validImages[] = $image;
            }
        }

        return $validImages;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillMetrics(Sku $sku, array $data): void
    {
        // 评分和评论数
        $score = $data['score'] ?? null;
        if (null !== $score && !is_string($score)) {
            $score = null;
        }
        $sku->getSpecification()->setScore($score);

        $commentCount = $data['commentCount'] ?? 0;
        if (!is_int($commentCount) && !is_string($commentCount) && !is_float($commentCount)) {
            $commentCount = 0;
        }
        $sku->getSpecification()->setCommentCount((int) $commentCount);

        // 促销信息
        $this->fillPromotionInfo($sku, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillPromotionInfo(Sku $sku, array $data): void
    {
        $promoInfo = $data['promoInfo'] ?? null;
        $hasPromotion = null !== $promoInfo && [] !== $promoInfo;
        $sku->getSpecification()->setHasPromotion($hasPromotion);

        if ($hasPromotion && is_array($promoInfo)) {
            $validPromoInfo = $this->validatePromotionInfoArray($promoInfo);
            $sku->getSpecification()->setPromotionInfo($validPromoInfo);
        }
    }

    /**
     * @param array<mixed> $promoInfo
     * @return array<int, array<string, mixed>>
     */
    private function validatePromotionInfoArray(array $promoInfo): array
    {
        $validPromoInfo = [];
        foreach ($promoInfo as $key => $value) {
            if (is_int($key) && is_array($value)) {
                $validPromoInfo[$key] = $this->validateStringKeyedArray($value);
            }
        }

        return $validPromoInfo;
    }

    /**
     * @param array<mixed> $data
     * @return array<string, mixed>
     */
    private function validateStringKeyedArray(array $data): array
    {
        $validated = [];
        foreach ($data as $k => $v) {
            if (is_string($k)) {
                $validated[$k] = $v;
            }
        }

        return $validated;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillLogistics(Sku $sku, array $data): void
    {
        $this->fillWarehouseInfo($sku, $data);
        $this->fillDeliveryAreas($sku, $data);
        $this->fillGlobalBuyInfo($sku, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillWarehouseInfo(Sku $sku, array $data): void
    {
        $warehouseId = $data['warehouseId'] ?? null;
        if (null !== $warehouseId && !is_string($warehouseId)) {
            $warehouseId = null;
        }
        $sku->getBaseInfo()->setWarehouseId($warehouseId);

        $warehouseName = $data['warehouseName'] ?? null;
        if (null !== $warehouseName && !is_string($warehouseName)) {
            $warehouseName = null;
        }
        $sku->getBaseInfo()->setWarehouseName($warehouseName);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillDeliveryAreas(Sku $sku, array $data): void
    {
        if (!isset($data['deliveryAreas']) || !is_array($data['deliveryAreas'])) {
            return;
        }

        $validDeliveryAreas = [];
        foreach ($data['deliveryAreas'] as $key => $value) {
            if (is_string($key)) {
                $validDeliveryAreas[$key] = $value;
            }
        }
        $sku->getBaseInfo()->setDeliveryAreas($validDeliveryAreas);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillGlobalBuyInfo(Sku $sku, array $data): void
    {
        $isGlobalBuy = $data['isGlobalBuy'] ?? false;
        if (!is_bool($isGlobalBuy)) {
            $isGlobalBuy = false;
        }
        $sku->getBaseInfo()->setIsGlobalBuy($isGlobalBuy);

        $originCountry = $data['originCountry'] ?? null;
        if (null !== $originCountry && !is_string($originCountry)) {
            $originCountry = null;
        }
        $sku->getBaseInfo()->setOriginCountry($originCountry);
    }
}
