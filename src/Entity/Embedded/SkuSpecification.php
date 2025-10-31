<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品规格信息'])]
    #[Assert\Type(type: 'array')]
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
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '扩展属性'])]
    #[Assert\Type(type: 'array')]
    private ?array $extAttributes = null;

    /**
     * 参数信息
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $parameters = null;

    /**
     * 售后信息
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '售后信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $afterSalesInfo = null;

    /**
     * 商品评分
     */
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true, options: ['comment' => '商品评分'])]
    #[Assert\Length(max: 10)]
    private ?string $score = null;

    /**
     * 评价数量
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '评价数量'])]
    private ?int $commentCount = null;

    /**
     * 促销信息
     *
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '促销信息'])]
    #[Assert\Type(type: 'array')]
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
    #[Assert\Length(max: 255)]
    private ?string $promotionLabel = null;

    /**
     * 促销价格
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '促销价格'])]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private ?string $promoPrice = null;

    /**
     * 价格更新时间
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '价格更新时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $priceUpdateTime = null;

    /**
     * 库存更新时间
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '库存更新时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $stockUpdateTime = null;

    // Getters and Setters
    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getSpecifications(): ?array
    {
        return $this->specifications;
    }

    /**
     * @param array<int, array<string, mixed>>|null $specifications
     */
    public function setSpecifications(?array $specifications): void
    {
        $this->specifications = $specifications;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getExtAttributes(): ?array
    {
        return $this->extAttributes;
    }

    /**
     * @param array<int, array<string, mixed>>|null $extAttributes
     */
    public function setExtAttributes(?array $extAttributes): void
    {
        $this->extAttributes = $extAttributes;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @param array<int, array<string, mixed>>|null $parameters
     */
    public function setParameters(?array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getAfterSalesInfo(): ?array
    {
        return $this->afterSalesInfo;
    }

    /**
     * @param array<int, array<string, mixed>>|null $afterSalesInfo
     */
    public function setAfterSalesInfo(?array $afterSalesInfo): void
    {
        $this->afterSalesInfo = $afterSalesInfo;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): void
    {
        $this->score = $score;
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function setCommentCount(?int $commentCount): void
    {
        $this->commentCount = $commentCount;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getPromotionInfo(): ?array
    {
        return $this->promotionInfo;
    }

    /**
     * @param array<int, array<string, mixed>>|null $promotionInfo
     */
    public function setPromotionInfo(?array $promotionInfo): void
    {
        $this->promotionInfo = $promotionInfo;
    }

    public function hasPromotion(): bool
    {
        return $this->hasPromotion;
    }

    public function setHasPromotion(bool $hasPromotion): void
    {
        $this->hasPromotion = $hasPromotion;
    }

    public function getPromotionLabel(): ?string
    {
        return $this->promotionLabel;
    }

    public function setPromotionLabel(?string $promotionLabel): void
    {
        $this->promotionLabel = $promotionLabel;
    }

    public function getPromoPrice(): ?string
    {
        return $this->promoPrice;
    }

    public function setPromoPrice(?string $promoPrice): void
    {
        $this->promoPrice = $promoPrice;
    }

    public function getPriceUpdateTime(): ?\DateTimeInterface
    {
        return $this->priceUpdateTime;
    }

    public function setPriceUpdateTime(?\DateTimeInterface $priceUpdateTime): void
    {
        $this->priceUpdateTime = $priceUpdateTime;
    }

    public function getStockUpdateTime(): ?\DateTimeInterface
    {
        return $this->stockUpdateTime;
    }

    public function setStockUpdateTime(?\DateTimeInterface $stockUpdateTime): void
    {
        $this->stockUpdateTime = $stockUpdateTime;
    }

    /**
     * 转换为数组
     *
     * @return array<string, mixed>
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
            'promoPrice' => $this->promoPrice,
            'priceUpdateTime' => $this->priceUpdateTime?->format('Y-m-d H:i:s'),
            'stockUpdateTime' => $this->stockUpdateTime?->format('Y-m-d H:i:s'),
        ];
    }
}
