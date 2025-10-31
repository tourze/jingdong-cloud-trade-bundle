<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTitleTypeEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;
use JingdongCloudTradeBundle\Repository\InvoiceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_invoice', options: ['comment' => '京东云交易发票信息'])]
class Invoice implements PlainArrayInterface, \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 1, enumType: InvoiceTypeEnum::class, options: ['comment' => '发票类型'])]
    #[Assert\Choice(callback: [InvoiceTypeEnum::class, 'cases'])]
    private InvoiceTypeEnum $invoiceType = InvoiceTypeEnum::ELECTRONIC;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: InvoiceTitleTypeEnum::class, options: ['comment' => '发票抬头类型'])]
    #[Assert\Choice(callback: [InvoiceTitleTypeEnum::class, 'cases'])]
    private InvoiceTitleTypeEnum $titleType = InvoiceTitleTypeEnum::PERSONAL;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '发票抬头'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $title;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '纳税人识别号'])]
    #[Assert\Length(max: 255)]
    private ?string $taxpayerIdentity = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '注册地址'])]
    #[Assert\Length(max: 255)]
    private ?string $registeredAddress = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '注册电话'])]
    #[Assert\Length(max: 255)]
    private ?string $registeredPhone = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '开户银行'])]
    #[Assert\Length(max: 255)]
    private ?string $bankName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '银行账户'])]
    #[Assert\Length(max: 255)]
    private ?string $bankAccount = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '发票代码'])]
    #[IndexColumn]
    #[Assert\Length(max: 255)]
    private ?string $invoiceCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '发票号码'])]
    #[IndexColumn]
    #[Assert\Length(max: 255)]
    private ?string $invoiceNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '发票金额'])]
    #[Assert\Length(max: 13)]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private ?string $invoiceAmount = null;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: InvoiceStateEnum::class, nullable: true, options: ['comment' => '发票状态'])]
    #[Assert\Choice(callback: [InvoiceStateEnum::class, 'cases'])]
    private ?InvoiceStateEnum $invoiceState = InvoiceStateEnum::NOT_APPLIED;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: InvoiceContentEnum::class, nullable: true, options: ['comment' => '发票内容'])]
    #[Assert\Choice(callback: [InvoiceContentEnum::class, 'cases'])]
    private ?InvoiceContentEnum $invoiceContent = InvoiceContentEnum::GOODS;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '电子发票下载URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $downloadUrl = null;

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

    public function getInvoiceType(): InvoiceTypeEnum
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(InvoiceTypeEnum $invoiceType): void
    {
        $this->invoiceType = $invoiceType;
    }

    public function getTitleType(): InvoiceTitleTypeEnum
    {
        return $this->titleType;
    }

    public function setTitleType(InvoiceTitleTypeEnum $titleType): void
    {
        $this->titleType = $titleType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTaxpayerIdentity(): ?string
    {
        return $this->taxpayerIdentity;
    }

    public function setTaxpayerIdentity(?string $taxpayerIdentity): void
    {
        $this->taxpayerIdentity = $taxpayerIdentity;
    }

    public function getRegisteredAddress(): ?string
    {
        return $this->registeredAddress;
    }

    public function setRegisteredAddress(?string $registeredAddress): void
    {
        $this->registeredAddress = $registeredAddress;
    }

    public function getRegisteredPhone(): ?string
    {
        return $this->registeredPhone;
    }

    public function setRegisteredPhone(?string $registeredPhone): void
    {
        $this->registeredPhone = $registeredPhone;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getBankAccount(): ?string
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?string $bankAccount): void
    {
        $this->bankAccount = $bankAccount;
    }

    public function getInvoiceCode(): ?string
    {
        return $this->invoiceCode;
    }

    public function setInvoiceCode(?string $invoiceCode): void
    {
        $this->invoiceCode = $invoiceCode;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount(?string $invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    public function getInvoiceState(): ?InvoiceStateEnum
    {
        return $this->invoiceState;
    }

    public function setInvoiceState(?InvoiceStateEnum $invoiceState): void
    {
        $this->invoiceState = $invoiceState;
    }

    public function getInvoiceContent(): ?InvoiceContentEnum
    {
        return $this->invoiceContent;
    }

    public function setInvoiceContent(?InvoiceContentEnum $invoiceContent): void
    {
        $this->invoiceContent = $invoiceContent;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(?string $downloadUrl): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrder()->getId(),
            'invoiceType' => $this->getInvoiceType()->value,
            'invoiceTypeName' => $this->getInvoiceType()->getLabel(),
            'titleType' => $this->getTitleType()->value,
            'titleTypeName' => $this->getTitleType()->getLabel(),
            'title' => $this->getTitle(),
            'taxpayerIdentity' => $this->getTaxpayerIdentity(),
            'registeredAddress' => $this->getRegisteredAddress(),
            'registeredPhone' => $this->getRegisteredPhone(),
            'bankName' => $this->getBankName(),
            'bankAccount' => $this->getBankAccount(),
            'invoiceCode' => $this->getInvoiceCode(),
            'invoiceNumber' => $this->getInvoiceNumber(),
            'invoiceAmount' => $this->getInvoiceAmount(),
            'invoiceState' => $this->getInvoiceState()?->value,
            'invoiceStateName' => $this->getInvoiceState()?->getLabel(),
            'invoiceContent' => $this->getInvoiceContent()?->value,
            'invoiceContentName' => $this->getInvoiceContent()?->getLabel(),
            'downloadUrl' => $this->getDownloadUrl(),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('Invoice #%d - %s', $this->id ?? 0, $this->title ?? 'Untitled');
    }
}
