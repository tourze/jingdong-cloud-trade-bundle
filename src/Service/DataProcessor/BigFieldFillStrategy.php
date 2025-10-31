<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 大字段信息填充策略
 */
readonly class BigFieldFillStrategy implements SkuDataFillStrategy
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
        return 'skuBigFieldInfo' === $section && $this->validator->isValidArrayField($data, $section);
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
        $bigFieldInfo = $this->validator->validateStringKeyedArray($rawData);
        $this->fillSkuBigFieldInfo($sku, $bigFieldInfo);
    }

    /**
     * @param array<string, mixed> $bigFieldInfo
     */
    private function fillSkuBigFieldInfo(Sku $sku, array $bigFieldInfo): void
    {
        $skuBigFieldInfo = $sku->getBigFieldInfo();

        if (isset($bigFieldInfo['description'])) {
            $skuBigFieldInfo->setDescription($this->toNullableString($bigFieldInfo['description']));
        }
        if (isset($bigFieldInfo['introduction'])) {
            $skuBigFieldInfo->setIntroduction($this->toNullableString($bigFieldInfo['introduction']));
        }
        if (isset($bigFieldInfo['wReadMe'])) {
            $skuBigFieldInfo->setWReadMe($this->toNullableString($bigFieldInfo['wReadMe']));
        }
        if (isset($bigFieldInfo['pcWdis'])) {
            $skuBigFieldInfo->setPcWdis($this->toNullableString($bigFieldInfo['pcWdis']));
        }
        if (isset($bigFieldInfo['pcHtmlContent'])) {
            $skuBigFieldInfo->setPcHtmlContent($this->toNullableString($bigFieldInfo['pcHtmlContent']));
        }
        if (isset($bigFieldInfo['pcJsContent'])) {
            $skuBigFieldInfo->setPcJsContent($this->toNullableString($bigFieldInfo['pcJsContent']));
        }
        if (isset($bigFieldInfo['pcCssContent'])) {
            $skuBigFieldInfo->setPcCssContent($this->toNullableString($bigFieldInfo['pcCssContent']));
        }
    }

    private function toNullableString(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
