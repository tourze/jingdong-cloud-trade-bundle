<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 基础信息填充策略
 */
readonly class BaseInfoFillStrategy implements SkuDataFillStrategy
{
    public function __construct(
        private ArrayDataValidator $validator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function canHandle(array $data, string $section): bool
    {
        return 'skuBaseInfo' === $section && $this->validator->isValidArrayField($data, $section);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fill(Sku $sku, array $data, string $section): void
    {
        $rawData = $data[$section];
        if (!is_array($rawData)) {
            return;
        }
        $baseInfoData = $this->validator->validateStringKeyedArray($rawData);
        $this->fillSkuBaseInfo($sku, $baseInfoData);
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillSkuBaseInfo(Sku $sku, array $baseInfo): void
    {
        $skuBaseInfo = $sku->getBaseInfo();

        $this->fillBasicMappings($skuBaseInfo, $baseInfo);
        $this->fillCategoryInfo($skuBaseInfo, $baseInfo);
        $this->fillBrandInfo($skuBaseInfo, $baseInfo);
        $this->fillNumericFields($skuBaseInfo, $baseInfo);
        $this->fillSpecialFields($skuBaseInfo, $baseInfo);
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillBasicMappings(SkuBaseInfo $skuBaseInfo, array $baseInfo): void
    {
        $basicMappings = [
            'skuId' => 'setSkuId',
            'skuName' => 'setSkuName',
            'price' => 'setPrice',
            'marketPrice' => 'setMarketPrice',
            'skuStatus' => 'setState',
            'venderName' => 'setVendorName',
            'shopName' => 'setShopName',
            'delivery' => 'setDelivery',
            'unit' => 'setUnit',
            'model' => 'setModel',
            'color' => 'setColor',
            'colorSequence' => 'setColorSequence',
            'size' => 'setSize',
            'sizeSequence' => 'setSizeSequence',
            'packageType' => 'setPackageType',
            'warranty' => 'setWarranty',
            'placeOfProduction' => 'setPlaceOfProduction',
            'fare' => 'setFare',
            'tax' => 'setTax',
            'upcCode' => 'setUpcCode',
        ];

        $this->applyMappings($skuBaseInfo, $baseInfo, $basicMappings);
    }

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $mappings
     */
    private function applyMappings(object $target, array $data, array $mappings): void
    {
        foreach ($mappings as $key => $method) {
            if (isset($data[$key]) && method_exists($target, $method)) {
                $callable = [$target, $method];
                if (is_callable($callable)) {
                    call_user_func($callable, $data[$key]);
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillCategoryInfo(SkuBaseInfo $skuBaseInfo, array $baseInfo): void
    {
        $categoryMappings = [
            'categoryId' => 'setCategoryId',
            'categoryName' => 'setCategoryName',
            'categoryId1' => 'setCategoryId1',
            'categoryName1' => 'setCategoryName1',
            'categoryId2' => 'setCategoryId2',
            'categoryName2' => 'setCategoryName2',
        ];

        $this->applyMappings($skuBaseInfo, $baseInfo, $categoryMappings);
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillBrandInfo(SkuBaseInfo $skuBaseInfo, array $baseInfo): void
    {
        $brandMappings = [
            'brandId' => 'setBrandId',
            'brandName' => 'setBrandName',
        ];

        $this->applyMappings($skuBaseInfo, $baseInfo, $brandMappings);
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillNumericFields(SkuBaseInfo $skuBaseInfo, array $baseInfo): void
    {
        if (isset($baseInfo['weight'])) {
            $skuBaseInfo->setWeight($this->toInt($baseInfo['weight']));
        }
        if (isset($baseInfo['width'])) {
            $skuBaseInfo->setWidth($this->toFloat($baseInfo['width']));
        }
        if (isset($baseInfo['height'])) {
            $skuBaseInfo->setHeight($this->toFloat($baseInfo['height']));
        }
        if (isset($baseInfo['length'])) {
            $skuBaseInfo->setLength($this->toFloat($baseInfo['length']));
        }
        if (isset($baseInfo['shelfLife'])) {
            $skuBaseInfo->setShelfLife($this->toInt($baseInfo['shelfLife']));
        }
    }

    private function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) || is_float($value)) {
            return (int) $value;
        }

        return 0;
    }

    private function toFloat(mixed $value): float
    {
        if (is_float($value)) {
            return $value;
        }
        if (is_int($value) || is_string($value)) {
            return (float) $value;
        }

        return 0.0;
    }

    /**
     * @param array<string, mixed> $baseInfo
     */
    private function fillSpecialFields(SkuBaseInfo $skuBaseInfo, array $baseInfo): void
    {
        if (isset($baseInfo['saleAttributesList'])) {
            $saleAttrs = $baseInfo['saleAttributesList'];
            if (is_array($saleAttrs)) {
                // saleAttributesList 是一个数字索引数组，直接使用，不需要验证字符串键
                /** @var array<string, mixed> $validatedSaleAttrs */
                $validatedSaleAttrs = $saleAttrs;
                $skuBaseInfo->setSaleAttrs($validatedSaleAttrs);
            }
        }

        if (isset($baseInfo['wareType']) && '2' === $baseInfo['wareType']) {
            $skuBaseInfo->setIsGlobalBuy(true);
        }
    }
}
