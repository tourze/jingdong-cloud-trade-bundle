<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;

/**
 * SKU数据填充策略接口
 */
interface SkuDataFillStrategy
{
    /**
     * 判断是否可以处理指定的数据段
     *
     * @param array<string, mixed> $data
     */
    public function canHandle(array $data, string $section): bool;

    /**
     * 填充SKU数据
     *
     * @param array<string, mixed> $data
     */
    public function fill(Sku $sku, array $data, string $section): void;
}
