<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\AfsServiceRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AfsServiceRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_afs_service', options: ['comment' => '京东云交易售后服务单'])]
class AfsService implements PlainArrayInterface, AdminArrayInterface
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

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '京东售后服务单号'])]
    #[IndexColumn]
    private string $afsServiceId;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '售后类型：10-退货，20-换货，30-维修'])]
    private string $afsType;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '服务单状态'])]
    private string $afsServiceState;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '申请原因'])]
    private ?string $applyReason = null;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '申请描述'])]
    private ?string $applyDescription = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '申请时间'])]
    private ?\DateTimeImmutable $applyTime = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '审核时间'])]
    private ?\DateTimeImmutable $auditTime = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '完成时间'])]
    private ?\DateTimeImmutable $completeTime = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true, options: ['comment' => '退款金额'])]
    private ?string $refundAmount = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '物流公司'])]
    private ?string $logisticsCompany = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '物流单号'])]
    private ?string $logisticsNo = null;

    use TimestampableAware;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getAfsServiceId(): string
    {
        return $this->afsServiceId;
    }

    public function setAfsServiceId(string $afsServiceId): self
    {
        $this->afsServiceId = $afsServiceId;
        return $this;
    }

    public function getAfsType(): string
    {
        return $this->afsType;
    }

    public function setAfsType(string $afsType): self
    {
        $this->afsType = $afsType;
        return $this;
    }

    public function getAfsServiceState(): string
    {
        return $this->afsServiceState;
    }

    public function setAfsServiceState(string $afsServiceState): self
    {
        $this->afsServiceState = $afsServiceState;
        return $this;
    }

    public function getApplyReason(): ?string
    {
        return $this->applyReason;
    }

    public function setApplyReason(?string $applyReason): self
    {
        $this->applyReason = $applyReason;
        return $this;
    }

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): self
    {
        $this->applyDescription = $applyDescription;
        return $this;
    }

    public function getApplyTime(): ?\DateTimeImmutable
    {
        return $this->applyTime;
    }

    public function setApplyTime(?\DateTimeImmutable $applyTime): self
    {
        $this->applyTime = $applyTime;
        return $this;
    }

    public function getAuditTime(): ?\DateTimeImmutable
    {
        return $this->auditTime;
    }

    public function setAuditTime(?\DateTimeImmutable $auditTime): self
    {
        $this->auditTime = $auditTime;
        return $this;
    }

    public function getCompleteTime(): ?\DateTimeImmutable
    {
        return $this->completeTime;
    }

    public function setCompleteTime(?\DateTimeImmutable $completeTime): self
    {
        $this->completeTime = $completeTime;
        return $this;
    }

    public function getRefundAmount(): ?string
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(?string $refundAmount): self
    {
        $this->refundAmount = $refundAmount;
        return $this;
    }

    public function getLogisticsCompany(): ?string
    {
        return $this->logisticsCompany;
    }

    public function setLogisticsCompany(?string $logisticsCompany): self
    {
        $this->logisticsCompany = $logisticsCompany;
        return $this;
    }

    public function getLogisticsNo(): ?string
    {
        return $this->logisticsNo;
    }

    public function setLogisticsNo(?string $logisticsNo): self
    {
        $this->logisticsNo = $logisticsNo;
        return $this;
    }

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

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'applyReason' => $this->getApplyReason(),
            'applyDescription' => $this->getApplyDescription(),
            'applyTime' => $this->getApplyTime() ? $this->getApplyTime()->format('Y-m-d H:i:s') : null,
            'auditTime' => $this->getAuditTime() ? $this->getAuditTime()->format('Y-m-d H:i:s') : null,
            'completeTime' => $this->getCompleteTime() ? $this->getCompleteTime()->format('Y-m-d H:i:s') : null,
            'logisticsCompany' => $this->getLogisticsCompany(),
            'logisticsNo' => $this->getLogisticsNo(),
        ];
    }
} 