<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * 京东商品图片信息
 */
#[ORM\Embeddable]
class SkuImageInfo
{
    /**
     * 商品主图URL
     */
    #[ORM\Column(type: Types::STRING, length: 512, nullable: true, options: ['comment' => '商品主图URL'])]
    private ?string $imageUrl = null;

    /**
     * 商品详情图片URL列表
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品详情图片URL列表'])]
    private ?array $detailImages = null;

    /**
     * 图片信息列表，包含多个图片及其属性
     * 
     * 格式：
     * [
     *   {
     *     "path": "图片路径信息",
     *     "features": "特征信息",
     *     "orderSort": "1",
     *     "isPrimary": "1",
     *     "position": "1",
     *     "type": "1"
     *   },
     *   ...
     * ]
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品图片信息列表'])]
    private ?array $imageInfos = null;

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getDetailImages(): ?array
    {
        return $this->detailImages;
    }

    public function setDetailImages(?array $detailImages): self
    {
        $this->detailImages = $detailImages;
        return $this;
    }

    public function getImageInfos(): ?array
    {
        return $this->imageInfos;
    }

    public function setImageInfos(?array $imageInfos): self
    {
        $this->imageInfos = $imageInfos;
        return $this;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'imageUrl' => $this->imageUrl,
            'detailImages' => $this->detailImages,
            'imageInfos' => $this->imageInfos,
        ];
    }
} 