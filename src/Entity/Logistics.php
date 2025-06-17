<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\LogisticsRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: LogisticsRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_logistics', options: ['comment' => '京东云交易物流信息'])]
class Logistics implements PlainArrayInterface
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

    /**
     * 关联京东账户
     */
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '物流公司编码'])]
    private string $logisticsCode;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '物流公司名称'])]
    private string $logisticsName;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '物流单号'])]
    #[IndexColumn]
    private string $waybillCode;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '物流轨迹信息(JSON格式)'])]
    private ?string $trackInfo = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '最后更新时间'])]
    private ?\DateTimeImmutable $lastUpdateTime = null;

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

    public function getLogisticsCode(): string
    {
        return $this->logisticsCode;
    }

    public function setLogisticsCode(string $logisticsCode): self
    {
        $this->logisticsCode = $logisticsCode;
        return $this;
    }

    public function getLogisticsName(): string
    {
        return $this->logisticsName;
    }

    public function setLogisticsName(string $logisticsName): self
    {
        $this->logisticsName = $logisticsName;
        return $this;
    }

    public function getWaybillCode(): string
    {
        return $this->waybillCode;
    }

    public function setWaybillCode(string $waybillCode): self
    {
        $this->waybillCode = $waybillCode;
        return $this;
    }

    public function getTrackInfo(): ?string
    {
        return $this->trackInfo;
    }

    public function setTrackInfo(?string $trackInfo): self
    {
        $this->trackInfo = $trackInfo;
        return $this;
    }

    public function getLastUpdateTime(): ?\DateTimeImmutable
    {
        return $this->lastUpdateTime;
    }

    public function setLastUpdateTime(?\DateTimeImmutable $lastUpdateTime): self
    {
        $this->lastUpdateTime = $lastUpdateTime;
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
            'logisticsCode' => $this->getLogisticsCode(),
            'logisticsName' => $this->getLogisticsName(),
            'waybillCode' => $this->getWaybillCode(),
            'lastUpdateTime' => $this->getLastUpdateTime()?->format('Y-m-d H:i:s'),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
} 