<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\DeliveryAddressRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 京东云交易收货地址
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 * @implements PlainArrayInterface<string, mixed>
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
    private int $id = 0;

    /**
     * 关联京东账户
     */
    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '收货人姓名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $receiverName;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '收货人手机号'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $receiverMobile;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '收货人固定电话'])]
    #[Assert\Length(max: 20)]
    private ?string $receiverPhone = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '省份'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $province;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '城市'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $city;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '区县'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $county;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '街道/乡镇'])]
    #[Assert\Length(max: 64)]
    private ?string $town = null;

    #[ORM\Column(type: Types::STRING, length: 512, options: ['comment' => '详细地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 512)]
    private string $detailAddress;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '邮政编码'])]
    #[Assert\Length(max: 20)]
    private ?string $postCode = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否默认地址'])]
    #[Assert\NotNull]
    private bool $isDefault = false;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '地址标签（如家、公司等）'])]
    #[Assert\Length(max: 64)]
    private ?string $addressTag = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '支持全球购'])]
    #[Assert\NotNull]
    private bool $supportGlobalBuy = false;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '身份证号（全球购必填）'])]
    #[Assert\Length(max: 20)]
    private ?string $idCardNo = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
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

    public function getReceiverPhone(): ?string
    {
        return $this->receiverPhone;
    }

    public function setReceiverPhone(?string $receiverPhone): void
    {
        $this->receiverPhone = $receiverPhone;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCounty(): string
    {
        return $this->county;
    }

    public function setCounty(string $county): void
    {
        $this->county = $county;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): void
    {
        $this->town = $town;
    }

    public function getDetailAddress(): string
    {
        return $this->detailAddress;
    }

    public function setDetailAddress(string $detailAddress): void
    {
        $this->detailAddress = $detailAddress;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getAddressTag(): ?string
    {
        return $this->addressTag;
    }

    public function setAddressTag(?string $addressTag): void
    {
        $this->addressTag = $addressTag;
    }

    public function supportGlobalBuy(): bool
    {
        return $this->supportGlobalBuy;
    }

    public function setSupportGlobalBuy(bool $supportGlobalBuy): void
    {
        $this->supportGlobalBuy = $supportGlobalBuy;
    }

    public function getIdCardNo(): ?string
    {
        return $this->idCardNo;
    }

    public function setIdCardNo(?string $idCardNo): void
    {
        $this->idCardNo = $idCardNo;
    }

    /**
     * 获取完整地址
     */
    public function getFullAddress(): string
    {
        $address = $this->province . ' ' . $this->city . ' ' . $this->county;
        if (null !== $this->town && '' !== $this->town) {
            $address .= ' ' . $this->town;
        }
        $address .= ' ' . $this->detailAddress;

        return $address;
    }

    /**
     * @return array<string, mixed>
     */
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
