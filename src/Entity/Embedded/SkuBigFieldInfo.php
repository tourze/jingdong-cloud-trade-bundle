<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 京东商品大字段信息
 */
#[ORM\Embeddable]
class SkuBigFieldInfo
{
    /**
     * PC端商品介绍信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC端商品介绍信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $pcWdis = null;

    /**
     * PC HTML标签内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC HTML标签内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $pcHtmlContent = null;

    /**
     * PC js内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC js内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $pcJsContent = null;

    /**
     * PC css样式内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC css样式内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $pcCssContent = null;

    /**
     * 商品详情信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品详情信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    /**
     * 商品介绍信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品介绍信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $introduction = null;

    /**
     * 产品说明信息（wReadMe字段）
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '产品说明信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $wReadMe = null;

    // Getters and Setters
    public function getPcWdis(): ?string
    {
        return $this->pcWdis;
    }

    public function setPcWdis(?string $pcWdis): void
    {
        $this->pcWdis = $pcWdis;
    }

    public function getPcHtmlContent(): ?string
    {
        return $this->pcHtmlContent;
    }

    public function setPcHtmlContent(?string $pcHtmlContent): void
    {
        $this->pcHtmlContent = $pcHtmlContent;
    }

    public function getPcJsContent(): ?string
    {
        return $this->pcJsContent;
    }

    public function setPcJsContent(?string $pcJsContent): void
    {
        $this->pcJsContent = $pcJsContent;
    }

    public function getPcCssContent(): ?string
    {
        return $this->pcCssContent;
    }

    public function setPcCssContent(?string $pcCssContent): void
    {
        $this->pcCssContent = $pcCssContent;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    public function getWReadMe(): ?string
    {
        return $this->wReadMe;
    }

    public function setWReadMe(?string $wReadMe): void
    {
        $this->wReadMe = $wReadMe;
    }

    /**
     * 转换为数组
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'pcWdis' => $this->pcWdis,
            'pcHtmlContent' => $this->pcHtmlContent,
            'pcJsContent' => $this->pcJsContent,
            'pcCssContent' => $this->pcCssContent,
            'description' => $this->description,
            'introduction' => $this->introduction,
            'wReadMe' => $this->wReadMe,
        ];
    }
}
