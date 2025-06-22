<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use JingdongCloudTradeBundle\Repository\PaymentRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_payment', options: ['comment' => '京东云交易支付信息'])]
class Payment implements PlainArrayInterface, AdminArrayInterface, \Stringable
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
     * 关联订单
     */
    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '支付流水号'])]
    #[IndexColumn]
    private string $paymentId;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: PaymentMethodEnum::class, options: ['comment' => '支付方式'])]
    private PaymentMethodEnum $paymentMethod = PaymentMethodEnum::ONLINE;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: PaymentChannelEnum::class, nullable: true, options: ['comment' => '支付渠道'])]
    private ?PaymentChannelEnum $paymentChannel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '支付金额'])]
    private string $paymentAmount;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: PaymentStateEnum::class, options: ['comment' => '支付状态'])]
    private PaymentStateEnum $paymentState = PaymentStateEnum::PENDING;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '支付时间'])]
    private ?\DateTimeImmutable $paymentTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '支付备注信息'])]
    private ?string $paymentNote = null;

    use TimestampableAware;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): self
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    public function getPaymentMethod(): PaymentMethodEnum
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodEnum $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getPaymentChannel(): ?PaymentChannelEnum
    {
        return $this->paymentChannel;
    }

    public function setPaymentChannel(?PaymentChannelEnum $paymentChannel): self
    {
        $this->paymentChannel = $paymentChannel;
        return $this;
    }

    public function getPaymentAmount(): string
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(string $paymentAmount): self
    {
        $this->paymentAmount = $paymentAmount;
        return $this;
    }

    public function getPaymentState(): PaymentStateEnum
    {
        return $this->paymentState;
    }

    public function setPaymentState(PaymentStateEnum $paymentState): self
    {
        $this->paymentState = $paymentState;
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

    public function getPaymentNote(): ?string
    {
        return $this->paymentNote;
    }

    public function setPaymentNote(?string $paymentNote): self
    {
        $this->paymentNote = $paymentNote;
        return $this;
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrder()->getId(),
            'paymentId' => $this->getPaymentId(),
            'paymentMethod' => $this->getPaymentMethod()->value,
            'paymentMethodName' => $this->getPaymentMethod()->getLabel(),
            'paymentChannel' => $this->getPaymentChannel()?->value,
            'paymentChannelName' => $this->getPaymentChannel()?->getLabel(),
            'paymentAmount' => $this->getPaymentAmount(),
            'paymentState' => $this->getPaymentState()->value,
            'paymentStateName' => $this->getPaymentState()->getLabel(),
            'paymentTime' => $this->getPaymentTime()?->format('Y-m-d H:i:s'),
            'paymentNote' => $this->getPaymentNote(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'orderNumber' => $this->getOrder()->getOrderId(),
        ];
    }

    public function __toString(): string
    {
        return sprintf('Payment %s (%s)', $this->paymentId, $this->paymentAmount);
    }
}
