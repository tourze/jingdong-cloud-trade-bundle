<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\DeliveryAddressRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 京东云交易收货地址
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 */
#[ORM\Entity(repositoryClass: DeliveryAddressRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_delivery_address', options: ['comment' => '京东云交易收货地址'])]
class DeliveryAddress implements PlainArrayInterface, \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    /**
     * 关联京东账户
     */
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人姓名'])]
    private string $receiverName;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '收货人手机号'])]
    #[IndexColumn]
    private string $receiverMobile;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '收货人固定电话'])]
    private ?string $receiverPhone = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '省份'])]
    private string $province;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '城市'])]
    private string $city;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '区县'])]
    private string $county;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '街道/乡镇'])]
    private ?string $town = null;

    #[ORM\Column(type: Types::STRING, length: 512, options: ['comment' => '详细地址'])]
    private string $detailAddress;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '邮政编码'])]
    private ?string $postCode = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否默认地址'])]
    private bool $isDefault = false;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '地址标签（如家、公司等）'])]
    private ?string $addressTag = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '支持全球购'])]
    private bool $supportGlobalBuy = false;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '身份证号（全球购必填）'])]
    private ?string $idCardNo = null;

    public function getId(): ?int
    {
        return $this->id;
    }public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
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

    public function getReceiverPhone(): ?string
    {
        return $this->receiverPhone;
    }

    public function setReceiverPhone(?string $receiverPhone): self
    {
        $this->receiverPhone = $receiverPhone;
        return $this;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): self
    {
        $this->province = $province;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCounty(): string
    {
        return $this->county;
    }

    public function setCounty(string $county): self
    {
        $this->county = $county;
        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;
        return $this;
    }

    public function getDetailAddress(): string
    {
        return $this->detailAddress;
    }

    public function setDetailAddress(string $detailAddress): self
    {
        $this->detailAddress = $detailAddress;
        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function getAddressTag(): ?string
    {
        return $this->addressTag;
    }

    public function setAddressTag(?string $addressTag): self
    {
        $this->addressTag = $addressTag;
        return $this;
    }

    public function supportGlobalBuy(): bool
    {
        return $this->supportGlobalBuy;
    }

    public function setSupportGlobalBuy(bool $supportGlobalBuy): self
    {
        $this->supportGlobalBuy = $supportGlobalBuy;
        return $this;
    }

    public function getIdCardNo(): ?string
    {
        return $this->idCardNo;
    }

    public function setIdCardNo(?string $idCardNo): self
    {
        $this->idCardNo = $idCardNo;
        return $this;
    }


    /**
     * 获取完整地址
     */
    public function getFullAddress(): string
    {
        $address = $this->province . ' ' . $this->city . ' ' . $this->county;
        if ($this->town !== null && $this->town !== '') {
            $address .= ' ' . $this->town;
        }
        $address .= ' ' . $this->detailAddress;
        return $address;
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'receiverName' => $this->getReceiverName(),
            'receiverMobile' => $this->getReceiverMobile(),
            'receiverPhone' => $this->getReceiverPhone(),
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'county' => $this->getCounty(),
            'town' => $this->getTown(),
            'detailAddress' => $this->getDetailAddress(),
            'fullAddress' => $this->getFullAddress(),
            'postCode' => $this->getPostCode(),
            'isDefault' => $this->isDefault(),
            'addressTag' => $this->getAddressTag(),
            'supportGlobalBuy' => $this->supportGlobalBuy(),
            'createdBy' => $this->getCreatedBy(),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->receiverName, $this->getFullAddress());
    }
} 