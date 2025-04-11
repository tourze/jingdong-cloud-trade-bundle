<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * 京东商品规格和属性信息
 */
#[ORM\Embeddable]
class SkuSpecification
{
    /**
     * 商品规格信息
     * 
     * 格式:
     * [
     *   {
     *     "groupName": "组名字信息",
     *     "attributes": [
     *       {
     *         "attName": "属性名",
     *         "valNames": ["属性名"]
     *       }
     *     ]
     *   }
     * ]
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品规格信息'])]
    private ?array $specifications = null;

    /**
     * 扩展属性
     * 
     * 格式:
     * [
     *   {
     *     "attName": "属性值名",
     *     "valNames": ["属性名"]
     *   }
     * ]
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '扩展属性'])]
    private ?array $extAttributes = null;

    /**
     * 参数信息
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数信息'])]
    private ?array $parameters = null;

    /**
     * 售后信息
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '售后信息'])]
    private ?array $afterSalesInfo = null;

    /**
     * 商品评分
     */
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true, options: ['comment' => '商品评分'])]
    private ?string $score = null;

    /**
     * 评价数量
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '评价数量'])]
    private ?int $commentCount = null;

    /**
     * 促销信息
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '促销信息'])]
    private ?array $promotionInfo = null;

    /**
     * 是否有促销
     */
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否有促销', 'default' => false])]
    private bool $hasPromotion = false;

    /**
     * 促销标签
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '促销标签'])]
    private ?string $promotionLabel = null;

    // Getters and Setters
    public function getSpecifications(): ?array
    {
        return $this->specifications;
    }

    public function setSpecifications(?array $specifications): self
    {
        $this->specifications = $specifications;
        return $this;
    }

    public function getExtAttributes(): ?array
    {
        return $this->extAttributes;
    }

    public function setExtAttributes(?array $extAttributes): self
    {
        $this->extAttributes = $extAttributes;
        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getAfterSalesInfo(): ?array
    {
        return $this->afterSalesInfo;
    }

    public function setAfterSalesInfo(?array $afterSalesInfo): self
    {
        $this->afterSalesInfo = $afterSalesInfo;
        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function setCommentCount(?int $commentCount): self
    {
        $this->commentCount = $commentCount;
        return $this;
    }

    public function getPromotionInfo(): ?array
    {
        return $this->promotionInfo;
    }

    public function setPromotionInfo(?array $promotionInfo): self
    {
        $this->promotionInfo = $promotionInfo;
        return $this;
    }

    public function hasPromotion(): bool
    {
        return $this->hasPromotion;
    }

    public function setHasPromotion(bool $hasPromotion): self
    {
        $this->hasPromotion = $hasPromotion;
        return $this;
    }

    public function getPromotionLabel(): ?string
    {
        return $this->promotionLabel;
    }

    public function setPromotionLabel(?string $promotionLabel): self
    {
        $this->promotionLabel = $promotionLabel;
        return $this;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'specifications' => $this->specifications,
            'extAttributes' => $this->extAttributes,
            'parameters' => $this->parameters,
            'afterSalesInfo' => $this->afterSalesInfo,
            'score' => $this->score,
            'commentCount' => $this->commentCount,
            'promotionInfo' => $this->promotionInfo,
            'hasPromotion' => $this->hasPromotion,
            'promotionLabel' => $this->promotionLabel,
        ];
    }
} 