<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_order', options: ['comment' => '京东云交易订单'])]
class Order implements PlainArrayInterface, AdminArrayInterface, \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '京东订单号'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $orderId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '订单状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $orderState;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '支付状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $paymentState;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '物流状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $logisticsState;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人姓名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverName;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人手机号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverMobile;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人省份'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverProvince;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人城市'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverCity;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人区县'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverCounty;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人详细地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverAddress;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '订单总金额'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $orderTotalPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '实付金额'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $orderPaymentPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '运费'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $freightPrice;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '下单时间'])]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private \DateTimeImmutable $orderTime;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '支付时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $paymentTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发货时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $deliveryTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $completionTime = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已同步到京东'])]
    #[Assert\Type(type: 'bool')]
    private bool $synced = false;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '运单号'])]
    #[Assert\Length(max: 255)]
    private ?string $waybillCode = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderState(): string
    {
        return $this->orderState;
    }

    public function setOrderState(string $orderState): void
    {
        $this->orderState = $orderState;
    }

    public function getPaymentState(): string
    {
        return $this->paymentState;
    }

    public function setPaymentState(string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    public function getLogisticsState(): string
    {
        return $this->logisticsState;
    }

    public function setLogisticsState(string $logisticsState): void
    {
        $this->logisticsState = $logisticsState;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function setReceiverName(string $receiverName): void
    {
        $this->receiverName = $receiverName;
    }

    public function getReceiverMobile(): string
    {
        return $this->receiverMobile;
    }

    public function setReceiverMobile(string $receiverMobile): void
    {
        $this->receiverMobile = $receiverMobile;
    }

    public function getReceiverProvince(): string
    {
        return $this->receiverProvince;
    }

    public function setReceiverProvince(string $receiverProvince): void
    {
        $this->receiverProvince = $receiverProvince;
    }

    public function getReceiverCity(): string
    {
        return $this->receiverCity;
    }

    public function setReceiverCity(string $receiverCity): void
    {
        $this->receiverCity = $receiverCity;
    }

    public function getReceiverCounty(): string
    {
        return $this->receiverCounty;
    }

    public function setReceiverCounty(string $receiverCounty): void
    {
        $this->receiverCounty = $receiverCounty;
    }

    public function getReceiverAddress(): string
    {
        return $this->receiverAddress;
    }

    public function setReceiverAddress(string $receiverAddress): void
    {
        $this->receiverAddress = $receiverAddress;
    }

    public function getOrderTotalPrice(): string
    {
        return $this->orderTotalPrice;
    }

    public function setOrderTotalPrice(string $orderTotalPrice): void
    {
        $this->orderTotalPrice = $orderTotalPrice;
    }

    public function getOrderPaymentPrice(): string
    {
        return $this->orderPaymentPrice;
    }

    public function setOrderPaymentPrice(string $orderPaymentPrice): void
    {
        $this->orderPaymentPrice = $orderPaymentPrice;
    }

    public function getFreightPrice(): string
    {
        return $this->freightPrice;
    }

    public function setFreightPrice(string $freightPrice): void
    {
        $this->freightPrice = $freightPrice;
    }

    public function getOrderTime(): \DateTimeImmutable
    {
        return $this->orderTime;
    }

    public function setOrderTime(\DateTimeImmutable $orderTime): void
    {
        $this->orderTime = $orderTime;
    }

    public function getPaymentTime(): ?\DateTimeImmutable
    {
        return $this->paymentTime;
    }

    public function setPaymentTime(?\DateTimeImmutable $paymentTime): void
    {
        $this->paymentTime = $paymentTime;
    }

    public function getDeliveryTime(): ?\DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?\DateTimeImmutable $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getCompletionTime(): ?\DateTimeImmutable
    {
        return $this->completionTime;
    }

    public function setCompletionTime(?\DateTimeImmutable $completionTime): void
    {
        $this->completionTime = $completionTime;
    }

    public function isSynced(): bool
    {
        return $this->synced;
    }

    public function setSynced(bool $synced): void
    {
        $this->synced = $synced;
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
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function getWaybillCode(): ?string
    {
        return $this->waybillCode;
    }

    public function setWaybillCode(?string $waybillCode): void
    {
        $this->waybillCode = $waybillCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrderId(),
            'orderState' => $this->getOrderState(),
            'paymentState' => $this->getPaymentState(),
            'logisticsState' => $this->getLogisticsState(),
            'orderTotalPrice' => $this->getOrderTotalPrice(),
            'orderPaymentPrice' => $this->getOrderPaymentPrice(),
            'freightPrice' => $this->getFreightPrice(),
            'waybillCode' => $this->getWaybillCode(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(): array
    {
        return $this->toArray() + [
            'receiverName' => $this->getReceiverName(),
            'receiverMobile' => $this->getReceiverMobile(),
            'receiverProvince' => $this->getReceiverProvince(),
            'receiverCity' => $this->getReceiverCity(),
            'receiverCounty' => $this->getReceiverCounty(),
            'receiverAddress' => $this->getReceiverAddress(),
            'orderTime' => $this->getOrderTime()->format('Y-m-d H:i:s'),
            'paymentTime' => $this->getPaymentTime()?->format('Y-m-d H:i:s'),
            'deliveryTime' => $this->getDeliveryTime()?->format('Y-m-d H:i:s'),
            'finishTime' => $this->getCompletionTime()?->format('Y-m-d H:i:s'),
            'synced' => $this->isSynced(),
            'accountId' => $this->getAccount()->getId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }

    public function __toString(): string
    {
        return sprintf('Order %s (¥%s)', $this->orderId ?? 'Unknown', $this->orderTotalPrice ?? '0.00');
    }
}
