<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\OrderItemRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_order_item', options: ['comment' => '京东云交易订单商品项'])]
class OrderItem implements PlainArrayInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 关联京东账户
     */
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '商品ID(SkuId)'])]
    #[IndexColumn]
    private string $skuId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '商品名称'])]
    private string $skuName;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '商品数量'])]
    private int $quantity;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '商品单价'])]
    private string $price;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '商品总价'])]
    private string $totalPrice;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品图片URL'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '商品属性(JSON格式)'])]
    private ?string $attributes = null;

    use TimestampableAware;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): self
    {
        $this->skuId = $skuId;
        return $this;
    }

    public function getSkuName(): string
    {
        return $this->skuName;
    }

    public function setSkuName(string $skuName): self
    {
        $this->skuName = $skuName;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getAttributes(): ?string
    {
        return $this->attributes;
    }

    public function setAttributes(?string $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

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