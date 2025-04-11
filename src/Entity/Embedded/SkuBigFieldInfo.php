<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?string $pcWdis = null;

    /**
     * PC HTML标签内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC HTML标签内容'])]
    private ?string $pcHtmlContent = null;

    /**
     * PC js内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC js内容'])]
    private ?string $pcJsContent = null;

    /**
     * PC css样式内容
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'PC css样式内容'])]
    private ?string $pcCssContent = null;

    /**
     * 商品详情信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品详情信息'])]
    private ?string $description = null;

    /**
     * 商品介绍信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品介绍信息'])]
    private ?string $introduction = null;

    /**
     * 产品说明信息（wReadMe字段）
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '产品说明信息'])]
    private ?string $wReadMe = null;

    // Getters and Setters
    public function getPcWdis(): ?string
    {
        return $this->pcWdis;
    }

    public function setPcWdis(?string $pcWdis): self
    {
        $this->pcWdis = $pcWdis;
        return $this;
    }

    public function getPcHtmlContent(): ?string
    {
        return $this->pcHtmlContent;
    }

    public function setPcHtmlContent(?string $pcHtmlContent): self
    {
        $this->pcHtmlContent = $pcHtmlContent;
        return $this;
    }

    public function getPcJsContent(): ?string
    {
        return $this->pcJsContent;
    }

    public function setPcJsContent(?string $pcJsContent): self
    {
        $this->pcJsContent = $pcJsContent;
        return $this;
    }

    public function getPcCssContent(): ?string
    {
        return $this->pcCssContent;
    }

    public function setPcCssContent(?string $pcCssContent): self
    {
        $this->pcCssContent = $pcCssContent;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;
        return $this;
    }

    public function getWReadMe(): ?string
    {
        return $this->wReadMe;
    }

    public function setWReadMe(?string $wReadMe): self
    {
        $this->wReadMe = $wReadMe;
        return $this;
    }

    /**
     * 转换为数组
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