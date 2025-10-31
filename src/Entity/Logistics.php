<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\LogisticsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: LogisticsRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_logistics', options: ['comment' => '京东云交易物流信息'])]
class Logistics implements PlainArrayInterface, \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '物流公司编码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $logisticsCode;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '物流公司名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $logisticsName;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '物流单号'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $waybillCode;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '物流轨迹信息(JSON格式)'])]
    #[Assert\Length(max: 65535)]
    private ?string $trackInfo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后更新时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $lastModificationTime = null;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getLogisticsCode(): string
    {
        return $this->logisticsCode;
    }

    public function setLogisticsCode(string $logisticsCode): void
    {
        $this->logisticsCode = $logisticsCode;
    }

    public function getLogisticsName(): string
    {
        return $this->logisticsName;
    }

    public function setLogisticsName(string $logisticsName): void
    {
        $this->logisticsName = $logisticsName;
    }

    public function getWaybillCode(): string
    {
        return $this->waybillCode;
    }

    public function setWaybillCode(string $waybillCode): void
    {
        $this->waybillCode = $waybillCode;
    }

    public function getTrackInfo(): ?string
    {
        return $this->trackInfo;
    }

    public function setTrackInfo(?string $trackInfo): void
    {
        $this->trackInfo = $trackInfo;
    }

    public function getLastModificationTime(): ?\DateTimeImmutable
    {
        return $this->lastModificationTime;
    }

    public function setLastModificationTime(?\DateTimeImmutable $lastModificationTime): void
    {
        $this->lastModificationTime = $lastModificationTime;
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
            'logisticsCode' => $this->getLogisticsCode(),
            'logisticsName' => $this->getLogisticsName(),
            'waybillCode' => $this->getWaybillCode(),
            'lastUpdateTime' => $this->getLastModificationTime()?->format('Y-m-d H:i:s'),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('%s: %s', $this->logisticsName ?? 'Unknown Logistics', $this->waybillCode ?? 'No Waybill');
    }
}
