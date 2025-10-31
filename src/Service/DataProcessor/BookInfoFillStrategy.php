<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBookInfo;
use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 图书信息填充策略
 */
readonly class BookInfoFillStrategy implements SkuDataFillStrategy
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
        if ('bookSkuBaseInfo' !== $section || !$this->validator->isValidArrayField($data, 'skuBaseInfo')) {
            return false;
        }

        $skuBaseInfo = $data['skuBaseInfo'];
        if (!is_array($skuBaseInfo)) {
            return false;
        }

        return isset($skuBaseInfo['bookSkuBaseInfo']) && is_array($skuBaseInfo['bookSkuBaseInfo']);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fill(Sku $sku, array $data, string $section): void
    {
        $skuBaseInfo = $data['skuBaseInfo'];
        if (!is_array($skuBaseInfo)) {
            return;
        }

        $rawBookInfo = $skuBaseInfo['bookSkuBaseInfo'];
        if (!is_array($rawBookInfo)) {
            return;
        }

        $bookInfo = $this->validator->validateStringKeyedArray($rawBookInfo);
        $this->fillSkuBookInfo($sku, $bookInfo);
    }

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillSkuBookInfo(Sku $sku, array $bookInfo): void
    {
        $skuBookInfo = $sku->getBookInfo();

        $this->fillBookBasicInfo($skuBookInfo, $bookInfo);
        $this->fillBookAuthorInfo($skuBookInfo, $bookInfo);
        $this->fillBookPublishInfo($skuBookInfo, $bookInfo);
        $this->fillBookPhysicalInfo($skuBookInfo, $bookInfo);
        $this->fillBookAdditionalInfo($skuBookInfo, $bookInfo);
    }

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillBookBasicInfo(SkuBookInfo $skuBookInfo, array $bookInfo): void
    {
        $basicMappings = [
            'id' => 'setId',
            'barCode' => 'setBarCode',
            'bookName' => 'setBookName',
            'foreignBookName' => 'setForeignBookName',
        ];

        $this->applyMappings($skuBookInfo, $bookInfo, $basicMappings);

        if (isset($bookInfo['ISBN']) || isset($bookInfo['isbn'])) {
            $isbn = $bookInfo['ISBN'] ?? $bookInfo['isbn'] ?? null;
            $skuBookInfo->setIsbn(is_string($isbn) ? $isbn : null);
        }
        if (isset($bookInfo['ISSN']) || isset($bookInfo['issn'])) {
            $issn = $bookInfo['ISSN'] ?? $bookInfo['issn'] ?? null;
            $skuBookInfo->setIssn(is_string($issn) ? $issn : null);
        }
    }

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillBookAuthorInfo(SkuBookInfo $skuBookInfo, array $bookInfo): void
    {
        $authorMappings = [
            'author' => 'setAuthor',
            'transfer' => 'setTransfer',
            'editer' => 'setEditer',
            'compile' => 'setCompile',
            'drawer' => 'setDrawer',
            'photography' => 'setPhotography',
            'proofreader' => 'setProofreader',
        ];

        $this->applyMappings($skuBookInfo, $bookInfo, $authorMappings);
    }

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillBookPublishInfo(SkuBookInfo $skuBookInfo, array $bookInfo): void
    {
        $publishMappings = [
            'publishers' => 'setPublishers',
            'publishNo' => 'setPublishNo',
            'publishTime' => 'setPublishTime',
            'printTime' => 'setPrintTime',
            'batchNo' => 'setBatchNo',
            'printNo' => 'setPrintNo',
        ];

        $this->applyMappings($skuBookInfo, $bookInfo, $publishMappings);
    }

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillBookPhysicalInfo(SkuBookInfo $skuBookInfo, array $bookInfo): void
    {
        $physicalMappings = [
            'pages' => 'setPages',
            'letters' => 'setLetters',
            'sizeAndHeight' => 'setSizeAndHeight',
            'packageStr' => 'setPackageStr',
            'format' => 'setFormat',
        ];

        $this->applyMappings($skuBookInfo, $bookInfo, $physicalMappings);

        if (isset($bookInfo['packNum'])) {
            $skuBookInfo->setPackNum($this->toInt($bookInfo['packNum']));
        }
        if (isset($bookInfo['attachmentNum'])) {
            $skuBookInfo->setAttachmentNum($this->toInt($bookInfo['attachmentNum']));
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

    /**
     * @param array<string, mixed> $bookInfo
     */
    private function fillBookAdditionalInfo(SkuBookInfo $skuBookInfo, array $bookInfo): void
    {
        $additionalMappings = [
            'series' => 'setSeries',
            'language' => 'setLanguage',
            'attachment' => 'setAttachment',
            'brand' => 'setBrand',
            'picNo' => 'setPicNo',
            'chinaCatalog' => 'setChinaCatalog',
            'marketPrice' => 'setMarketPrice',
            'remarker' => 'setRemarker',
        ];

        $this->applyMappings($skuBookInfo, $bookInfo, $additionalMappings);
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
}
