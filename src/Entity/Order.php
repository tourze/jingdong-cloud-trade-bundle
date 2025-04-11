<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\OrderRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_order', options: ['comment' => '京东云交易订单'])]
class Order implements PlainArrayInterface, AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '京东订单号'])]
    #[IndexColumn]
    private string $orderId;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '订单状态'])]
    private string $orderState;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '支付状态'])]
    private string $paymentState;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '物流状态'])]
    private string $logisticsState;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人姓名'])]
    private string $receiverName;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人手机号'])]
    private string $receiverMobile;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人省份'])]
    private string $receiverProvince;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人城市'])]
    private string $receiverCity;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人区县'])]
    private string $receiverCounty;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '收货人详细地址'])]
    private string $receiverAddress;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '订单总金额'])]
    private string $orderTotalPrice;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '实付金额'])]
    private string $orderPaymentPrice;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => '运费'])]
    private string $freightPrice;

    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '下单时间'])]
    private \DateTimeImmutable $orderTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '支付时间'])]
    private ?\DateTimeImmutable $paymentTime = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '发货时间'])]
    private ?\DateTimeImmutable $deliveryTime = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '完成时间'])]
    private ?\DateTimeImmutable $finishTime = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否已同步到京东'])]
    private bool $synced = false;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '运单号'])]
    private ?string $waybillCode = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getOrderState(): string
    {
        return $this->orderState;
    }

    public function setOrderState(string $orderState): self
    {
        $this->orderState = $orderState;
        return $this;
    }

    public function getPaymentState(): string
    {
        return $this->paymentState;
    }

    public function setPaymentState(string $paymentState): self
    {
        $this->paymentState = $paymentState;
        return $this;
    }

    public function getLogisticsState(): string
    {
        return $this->logisticsState;
    }

    public function setLogisticsState(string $logisticsState): self
    {
        $this->logisticsState = $logisticsState;
        return $this;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function setReceiverName(string $receiverName): self
    {
        $this->receiverName = $receiverName;
        return $this;
    }

    public function getReceiverMobile(): string
    {
        return $this->receiverMobile;
    }

    public function setReceiverMobile(string $receiverMobile): self
    {
        $this->receiverMobile = $receiverMobile;
        return $this;
    }

    public function getReceiverProvince(): string
    {
        return $this->receiverProvince;
    }

    public function setReceiverProvince(string $receiverProvince): self
    {
        $this->receiverProvince = $receiverProvince;
        return $this;
    }

    public function getReceiverCity(): string
    {
        return $this->receiverCity;
    }

    public function setReceiverCity(string $receiverCity): self
    {
        $this->receiverCity = $receiverCity;
        return $this;
    }

    public function getReceiverCounty(): string
    {
        return $this->receiverCounty;
    }

    public function setReceiverCounty(string $receiverCounty): self
    {
        $this->receiverCounty = $receiverCounty;
        return $this;
    }

    public function getReceiverAddress(): string
    {
        return $this->receiverAddress;
    }

    public function setReceiverAddress(string $receiverAddress): self
    {
        $this->receiverAddress = $receiverAddress;
        return $this;
    }

    public function getOrderTotalPrice(): string
    {
        return $this->orderTotalPrice;
    }

    public function setOrderTotalPrice(string $orderTotalPrice): self
    {
        $this->orderTotalPrice = $orderTotalPrice;
        return $this;
    }

    public function getOrderPaymentPrice(): string
    {
        return $this->orderPaymentPrice;
    }

    public function setOrderPaymentPrice(string $orderPaymentPrice): self
    {
        $this->orderPaymentPrice = $orderPaymentPrice;
        return $this;
    }

    public function getFreightPrice(): string
    {
        return $this->freightPrice;
    }

    public function setFreightPrice(string $freightPrice): self
    {
        $this->freightPrice = $freightPrice;
        return $this;
    }

    public function getOrderTime(): \DateTimeImmutable
    {
        return $this->orderTime;
    }

    public function setOrderTime(\DateTimeImmutable $orderTime): self
    {
        $this->orderTime = $orderTime;
        return $this;
    }

    public function getPaymentTime(): ?\DateTimeImmutable
    {
        return $this->paymentTime;
    }

    public function setPaymentTime(?\DateTimeImmutable $paymentTime): self
    {
        $this->paymentTime = $paymentTime;
        return $this;
    }

    public function getDeliveryTime(): ?\DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?\DateTimeImmutable $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    public function getFinishTime(): ?\DateTimeImmutable
    {
        return $this->finishTime;
    }

    public function setFinishTime(?\DateTimeImmutable $finishTime): self
    {
        $this->finishTime = $finishTime;
        return $this;
    }

    public function isSynced(): bool
    {
        return $this->synced;
    }

    public function setSynced(bool $synced): self
    {
        $this->synced = $synced;
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

    public function setWaybillCode(?string $waybillCode): self
    {
        $this->waybillCode = $waybillCode;
        return $this;
    }

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
            'paymentTime' => $this->getPaymentTime() ? $this->getPaymentTime()->format('Y-m-d H:i:s') : null,
            'deliveryTime' => $this->getDeliveryTime() ? $this->getDeliveryTime()->format('Y-m-d H:i:s') : null,
            'finishTime' => $this->getFinishTime() ? $this->getFinishTime()->format('Y-m-d H:i:s') : null,
            'synced' => $this->isSynced(),
            'accountId' => $this->getAccount()->getId(),
        ];
    }

    public function retrievePlainArray(): array
    {
        return $this->toArray();
    }

    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }
}
