<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use JingdongCloudTradeBundle\Repository\PaymentRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_payment', options: ['comment' => '京东云交易支付信息'])]
class Payment implements PlainArrayInterface, AdminArrayInterface, \Stringable
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
     * 关联订单
     */
    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '支付流水号'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $paymentId;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: PaymentMethodEnum::class, options: ['comment' => '支付方式'])]
    #[Assert\Choice(callback: [PaymentMethodEnum::class, 'cases'])]
    private PaymentMethodEnum $paymentMethod = PaymentMethodEnum::ONLINE;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: PaymentChannelEnum::class, nullable: true, options: ['comment' => '支付渠道'])]
    #[Assert\Choice(callback: [PaymentChannelEnum::class, 'cases'])]
    private ?PaymentChannelEnum $paymentChannel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '支付金额'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private string $paymentAmount;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: PaymentStateEnum::class, options: ['comment' => '支付状态'])]
    #[Assert\Choice(callback: [PaymentStateEnum::class, 'cases'])]
    private PaymentStateEnum $paymentState = PaymentStateEnum::PENDING;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '支付时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $paymentTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '支付备注信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $paymentNote = null;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentMethod(): PaymentMethodEnum
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodEnum $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getPaymentChannel(): ?PaymentChannelEnum
    {
        return $this->paymentChannel;
    }

    public function setPaymentChannel(?PaymentChannelEnum $paymentChannel): void
    {
        $this->paymentChannel = $paymentChannel;
    }

    public function getPaymentAmount(): string
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(string $paymentAmount): void
    {
        $this->paymentAmount = $paymentAmount;
    }

    public function getPaymentState(): PaymentStateEnum
    {
        return $this->paymentState;
    }

    public function setPaymentState(PaymentStateEnum $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    public function getPaymentTime(): ?\DateTimeImmutable
    {
        return $this->paymentTime;
    }

    public function setPaymentTime(?\DateTimeImmutable $paymentTime): void
    {
        $this->paymentTime = $paymentTime;
    }

    public function getPaymentNote(): ?string
    {
        return $this->paymentNote;
    }

    public function setPaymentNote(?string $paymentNote): void
    {
        $this->paymentNote = $paymentNote;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
