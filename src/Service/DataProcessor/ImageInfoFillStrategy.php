<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 图片信息填充策略
 */
readonly class ImageInfoFillStrategy implements SkuDataFillStrategy
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
        return 'imageInfos' === $section && $this->validator->isValidArrayField($data, $section);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fill(Sku $sku, array $data, string $section): void
    {
        $rawImageInfo = $data[$section];
        if (!is_array($rawImageInfo)) {
            return;
        }

        $imageInfoData = $this->validator->validateImageInfoArray($rawImageInfo);
        $imgUrl = $this->extractImgUrlFromBaseInfo($data);

        $this->fillSkuImageInfo($sku, $imageInfoData, $imgUrl);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractImgUrlFromBaseInfo(array $data): ?string
    {
        if (!$this->validator->isValidArrayField($data, 'skuBaseInfo')) {
            return null;
        }

        $skuBaseInfo = $data['skuBaseInfo'];
        if (!is_array($skuBaseInfo)) {
            return null;
        }

        $validatedSkuBaseInfo = $this->validator->validateStringKeyedArray($skuBaseInfo);

        return $this->validator->getStringField($validatedSkuBaseInfo, 'imgUrl');
    }

    /**
     * @param array<int, array<string, mixed>> $imageInfos
     */
    private function fillSkuImageInfo(Sku $sku, array $imageInfos, ?string $imgUrl = null): void
    {
        $skuImageInfo = $sku->getImageInfo();
        $skuImageInfo->setImageInfos($imageInfos);

        $primaryImageUrl = $this->extractPrimaryImageUrl($imageInfos);

        if (null !== $primaryImageUrl) {
            $skuImageInfo->setImageUrl($primaryImageUrl);
        } elseif (null !== $imgUrl) {
            $skuImageInfo->setImageUrl($imgUrl);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $imageInfos
     */
    private function extractPrimaryImageUrl(array $imageInfos): ?string
    {
        foreach ($imageInfos as $img) {
            if (isset($img['isPrimary']) && '1' === $img['isPrimary'] && isset($img['path'])) {
                $path = $img['path'];

                return is_string($path) ? $path : null;
            }
        }

        return null;
    }
}
