<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\DTO;

/**
 * 同步选项数据传输对象
 */
readonly class SyncOptions
{
    public function __construct(
        public int $limit = 1000,
        public bool $force = false,
        public ?string $categoryId = null,
        public ?string $brandId = null,
    ) {
    }

    /**
     * 从数组创建同步选项
     *
     * @param array<string, mixed> $options
     */
    public static function fromArray(array $options): self
    {
        $limit = $options['limit'] ?? 1000;
        $force = $options['force'] ?? false;
        $categoryId = $options['categoryId'] ?? null;
        $brandId = $options['brandId'] ?? null;

        return new self(
            limit: is_int($limit) ? $limit : (is_string($limit) || is_float($limit) ? (int) $limit : 1000),
            force: is_bool($force) ? $force : (bool) $force,
            categoryId: null !== $categoryId ? (is_string($categoryId) || is_int($categoryId) || is_float($categoryId) ? (string) $categoryId : null) : null,
            brandId: null !== $brandId ? (is_string($brandId) || is_int($brandId) || is_float($brandId) ? (string) $brandId : null) : null,
        );
    }

    /**
     * 构建API请求参数
     *
     * @return array<string, mixed>
     */
    public function buildRequestParams(int $page, int $pageSize): array
    {
        $params = [
            'page' => $page,
            'pageSize' => $pageSize,
        ];

        if (null !== $this->categoryId) {
            $params['cid3'] = $this->categoryId;
        }

        if (null !== $this->brandId) {
            $params['brandId'] = $this->brandId;
        }

        return $params;
    }
}
