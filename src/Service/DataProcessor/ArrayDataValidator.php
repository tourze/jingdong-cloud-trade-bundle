<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

/**
 * 数组数据验证器，专门处理API响应数据的类型安全验证
 */
readonly class ArrayDataValidator
{
    /**
     * 验证并返回字符串键的数组
     *
     * @param array<mixed, mixed> $data
     * @return array<string, mixed>
     */
    public function validateStringKeyedArray(array $data): array
    {
        $validated = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /**
     * 验证并返回整数键的数组
     *
     * @param array<mixed, mixed> $data
     * @return array<int, mixed>
     */
    public function validateIntKeyedArray(array $data): array
    {
        $validated = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /**
     * 验证图片信息数组
     *
     * @param array<mixed, mixed> $imageInfos
     * @return array<int, array<string, mixed>>
     */
    public function validateImageInfoArray(array $imageInfos): array
    {
        $validated = [];
        foreach ($imageInfos as $index => $imageInfo) {
            if (is_int($index) && is_array($imageInfo)) {
                $validated[$index] = $this->validateStringKeyedArray($imageInfo);
            }
        }

        return $validated;
    }

    /**
     * 检查数据是否存在且为数组类型
     *
     * @param array<string, mixed> $data
     */
    public function isValidArrayField(array $data, string $field): bool
    {
        return isset($data[$field]) && is_array($data[$field]);
    }

    /**
     * 安全获取字符串类型字段
     *
     * @param array<string, mixed> $data
     */
    public function getStringField(array $data, string $field): ?string
    {
        $value = $data[$field] ?? null;

        return is_string($value) ? $value : null;
    }
}
