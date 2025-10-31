<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 规格信息填充策略
 */
readonly class SpecificationFillStrategy implements SkuDataFillStrategy
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
        return 'specifications' === $section && $this->validator->isValidArrayField($data, $section);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fill(Sku $sku, array $data, string $section): void
    {
        $rawSpecifications = $data[$section];
        if (!is_array($rawSpecifications)) {
            return;
        }

        $specificationsData = $this->validator->validateStringKeyedArray($rawSpecifications);
        $extAttsData = $this->extractExtAttributes($data);

        $this->fillSkuSpecificationInfo($sku, $specificationsData, $extAttsData);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function extractExtAttributes(array $data): array
    {
        $extAtts = $data['extAtts'] ?? [];
        if (!is_array($extAtts)) {
            return [];
        }

        return $this->validator->validateStringKeyedArray($extAtts);
    }

    /**
     * @param array<string, mixed> $specifications
     * @param array<string, mixed> $extAttributes
     */
    private function fillSkuSpecificationInfo(Sku $sku, array $specifications, array $extAttributes = []): void
    {
        $skuSpecification = $sku->getSpecification();

        // 转换为正确的数组格式并验证类型
        $convertedSpecs = $this->convertToArrayOfArrays(array_values($specifications));
        $convertedExtAttrs = $this->convertToArrayOfArrays(array_values($extAttributes));

        $skuSpecification->setSpecifications($convertedSpecs);
        $skuSpecification->setExtAttributes($convertedExtAttrs);
    }

    /**
     * @param list<mixed> $values
     * @return array<int, array<string, mixed>>
     */
    private function convertToArrayOfArrays(array $values): array
    {
        $result = [];
        foreach ($values as $index => $value) {
            if (is_array($value)) {
                $result[$index] = $this->validator->validateStringKeyedArray($value);
            }
        }

        return $result;
    }
}
