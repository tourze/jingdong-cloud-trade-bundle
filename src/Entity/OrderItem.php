<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\OrderItemRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_order_item', options: ['comment' => '京东云交易订单商品项'])]
class OrderItem implements PlainArrayInterface, \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 关联京东账户
     */
    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '商品ID(SkuId)'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $skuId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '商品名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $skuName;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '商品数量'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $quantity;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '商品单价'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $price;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '商品总价'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $totalPrice;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品图片URL'])]
    #[Assert\Url]
    #[Assert\Length(max: 255)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品属性(JSON格式)'])]
    #[Assert\Length(max: 65535)]
    private ?string $attributes = null;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): void
    {
        if (null !== $order) {
            $this->order = $order;
        }
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }

    public function getSkuName(): string
    {
        return $this->skuName;
    }

    public function setSkuName(string $skuName): void
    {
        $this->skuName = $skuName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getAttributes(): ?string
    {
        return $this->attributes;
    }

    public function setAttributes(?string $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrder()->getId(),
            'skuId' => $this->getSkuId(),
            'skuName' => $this->getSkuName(),
            'quantity' => $this->getQuantity(),
            'price' => $this->getPrice(),
            'totalPrice' => $this->getTotalPrice(),
            'imageUrl' => $this->getImageUrl(),
            'attributes' => $this->getAttributes(),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('%s x%d', $this->skuName ?? 'Unknown Item', $this->quantity ?? 0);
    }
}
