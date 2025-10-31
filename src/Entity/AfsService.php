<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\AfsServiceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AfsServiceRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_afs_service', options: ['comment' => '京东云交易售后服务单'])]
class AfsService implements PlainArrayInterface, AdminArrayInterface, \Stringable
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

    #[ORM\ManyToOne(targetEntity: Order::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '京东售后服务单号'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $afsServiceId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '售后类型：10-退货，20-换货，30-维修'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $afsType;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '服务单状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $afsServiceState;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '申请原因'])]
    #[Assert\Length(max: 255)]
    private ?string $applyReason = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '申请描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $applyDescription = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '申请时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $applyTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '审核时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $auditTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $completeTime = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '退款金额'])]
    #[Assert\Length(max: 13)]
    private ?string $refundAmount = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '物流公司'])]
    #[Assert\Length(max: 255)]
    private ?string $logisticsCompany = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '物流单号'])]
    #[Assert\Length(max: 255)]
    private ?string $logisticsNo = null;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getAfsServiceId(): string
    {
        return $this->afsServiceId;
    }

    public function setAfsServiceId(string $afsServiceId): void
    {
        $this->afsServiceId = $afsServiceId;
    }

    public function getAfsType(): string
    {
        return $this->afsType;
    }

    public function setAfsType(string $afsType): void
    {
        $this->afsType = $afsType;
    }

    public function getAfsServiceState(): string
    {
        return $this->afsServiceState;
    }

    public function setAfsServiceState(string $afsServiceState): void
    {
        $this->afsServiceState = $afsServiceState;
    }

    public function getApplyReason(): ?string
    {
        return $this->applyReason;
    }

    public function setApplyReason(?string $applyReason): void
    {
        $this->applyReason = $applyReason;
    }

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): void
    {
        $this->applyDescription = $applyDescription;
    }

    public function getApplyTime(): ?\DateTimeImmutable
    {
        return $this->applyTime;
    }

    public function setApplyTime(?\DateTimeImmutable $applyTime): void
    {
        $this->applyTime = $applyTime;
    }

    public function getAuditTime(): ?\DateTimeImmutable
    {
        return $this->auditTime;
    }

    public function setAuditTime(?\DateTimeImmutable $auditTime): void
    {
        $this->auditTime = $auditTime;
    }

    public function getCompleteTime(): ?\DateTimeImmutable
    {
        return $this->completeTime;
    }

    public function setCompleteTime(?\DateTimeImmutable $completeTime): void
    {
        $this->completeTime = $completeTime;
    }

    public function getRefundAmount(): ?string
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(?string $refundAmount): void
    {
        $this->refundAmount = $refundAmount;
    }

    public function getLogisticsCompany(): ?string
    {
        return $this->logisticsCompany;
    }

    public function setLogisticsCompany(?string $logisticsCompany): void
    {
        $this->logisticsCompany = $logisticsCompany;
    }

    public function getLogisticsNo(): ?string
    {
        return $this->logisticsNo;
    }

    public function setLogisticsNo(?string $logisticsNo): void
    {
        $this->logisticsNo = $logisticsNo;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrder()->getId(),
            'afsServiceId' => $this->getAfsServiceId(),
            'afsType' => $this->getAfsType(),
            'afsServiceState' => $this->getAfsServiceState(),
            'refundAmount' => $this->getRefundAmount(),
            'accountId' => $this->getAccount()->getId(),
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
            'applyReason' => $this->getApplyReason(),
            'applyDescription' => $this->getApplyDescription(),
            'applyTime' => $this->getApplyTime()?->format('Y-m-d H:i:s'),
            'auditTime' => $this->getAuditTime()?->format('Y-m-d H:i:s'),
            'completeTime' => $this->getCompleteTime()?->format('Y-m-d H:i:s'),
            'logisticsCompany' => $this->getLogisticsCompany(),
            'logisticsNo' => $this->getLogisticsNo(),
        ];
    }

    public function __toString(): string
    {
        return sprintf('AfsService %s', $this->afsServiceId);
    }
}
