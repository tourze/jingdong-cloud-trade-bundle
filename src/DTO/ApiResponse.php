<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\DTO;

/**
 * API响应数据传输对象
 */
readonly class ApiResponse
{
    /**
     * @param array<string, mixed> $skuList
     */
    public function __construct(
        public bool $success,
        public array $skuList,
        public int $total,
        public ?string $errorMsg = null,
    ) {
    }

    /**
     * 从API响应数组创建对象
     *
     * @param array<string, mixed> $result
     */
    public static function fromArray(array $result): self
    {
        $resultData = $result['result'] ?? [];
        if (!is_array($resultData)) {
            return new self(false, [], 0, '响应格式错误：result字段不是数组');
        }

        if (!isset($resultData['success']) || !(bool) $resultData['success']) {
            $errorMsg = is_string($resultData['errorMsg'] ?? null) ? $resultData['errorMsg'] : '未知错误';

            return new self(false, [], 0, $errorMsg);
        }

        $skuList = $resultData['materialSkuVoList'] ?? [];
        if (!is_array($skuList)) {
            return new self(false, [], 0, '响应格式错误：materialSkuVoList字段不是数组');
        }

        $total = $resultData['total'] ?? 0;
        if (!is_int($total) && !is_string($total) && !is_float($total)) {
            $total = 0;
        }

        return new self(
            success: true,
            skuList: self::validateSkuList($skuList),
            total: (int) $total,
        );
    }

    /**
     * 验证SKU列表数据格式
     *
     * @param array<mixed, mixed> $skuList
     * @return array<string, mixed>
     */
    private static function validateSkuList(array $skuList): array
    {
        $validSkuList = [];
        foreach ($skuList as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $validSkuList[$key] = $value;
            }
        }

        return $validSkuList;
    }

    public function hasData(): bool
    {
        return $this->success && 0 !== count($this->skuList);
    }
}
