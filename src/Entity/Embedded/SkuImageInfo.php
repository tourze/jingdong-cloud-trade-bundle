<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\Url]
    #[Assert\Length(max: 512)]
    private ?string $imageUrl = null;

    /**
     * 商品详情图片URL列表
     *
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品详情图片URL列表'])]
    #[Assert\Type(type: 'array')]
    #[Assert\All(constraints: [
        new Assert\Url(message: 'The URL is not valid.'),
    ])]
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
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品图片信息列表'])]
    #[Assert\Type(type: 'array')]
    private ?array $imageInfos = null;

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return array<string>|null
     */
    public function getDetailImages(): ?array
    {
        return $this->detailImages;
    }

    /**
     * @param array<string>|null $detailImages
     */
    public function setDetailImages(?array $detailImages): void
    {
        $this->detailImages = $detailImages;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getImageInfos(): ?array
    {
        return $this->imageInfos;
    }

    /**
     * @param array<int, array<string, mixed>>|null $imageInfos
     */
    public function setImageInfos(?array $imageInfos): void
    {
        $this->imageInfos = $imageInfos;
    }

    /**
     * 转换为数组
     *
     * @return array<string, mixed>
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
