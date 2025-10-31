<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBigFieldInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBookInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuImageInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuSpecification;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 京东云交易商品SKU
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 *
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: SkuRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_sku', options: ['comment' => '京东云交易商品SKU'])]
class Sku implements PlainArrayInterface, AdminArrayInterface, \Stringable
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
     * 京东账号
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    private Account $account;

    /**
     * 基础信息
     */
    #[ORM\Embedded(class: SkuBaseInfo::class)]
    #[Assert\Valid]
    private SkuBaseInfo $baseInfo;

    /**
     * 图片信息
     */
    #[ORM\Embedded(class: SkuImageInfo::class)]
    #[Assert\Valid]
    private SkuImageInfo $imageInfo;

    /**
     * 大字段信息
     */
    #[ORM\Embedded(class: SkuBigFieldInfo::class)]
    #[Assert\Valid]
    private SkuBigFieldInfo $bigFieldInfo;

    /**
     * 图书信息（仅适用于图书类商品）
     */
    #[ORM\Embedded(class: SkuBookInfo::class)]
    #[Assert\Valid]
    private SkuBookInfo $bookInfo;

    /**
     * 规格和属性信息
     */
    #[ORM\Embedded(class: SkuSpecification::class)]
    #[Assert\Valid]
    private SkuSpecification $specification;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '详情更新时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $detailUpdateTime = null;

    public function __construct()
    {
        $this->baseInfo = new SkuBaseInfo();
        $this->imageInfo = new SkuImageInfo();
        $this->bigFieldInfo = new SkuBigFieldInfo();
        $this->bookInfo = new SkuBookInfo();
        $this->specification = new SkuSpecification();
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getBaseInfo(): SkuBaseInfo
    {
        return $this->baseInfo;
    }

    public function setBaseInfo(SkuBaseInfo $baseInfo): void
    {
        $this->baseInfo = $baseInfo;
    }

    public function getImageInfo(): SkuImageInfo
    {
        return $this->imageInfo;
    }

    public function setImageInfo(SkuImageInfo $imageInfo): void
    {
        $this->imageInfo = $imageInfo;
    }

    public function getBigFieldInfo(): SkuBigFieldInfo
    {
        return $this->bigFieldInfo;
    }

    public function setBigFieldInfo(SkuBigFieldInfo $bigFieldInfo): void
    {
        $this->bigFieldInfo = $bigFieldInfo;
    }

    public function getBookInfo(): SkuBookInfo
    {
        return $this->bookInfo;
    }

    public function setBookInfo(SkuBookInfo $bookInfo): void
    {
        $this->bookInfo = $bookInfo;
    }

    public function getSpecification(): SkuSpecification
    {
        return $this->specification;
    }

    public function setSpecification(SkuSpecification $specification): void
    {
        $this->specification = $specification;
    }

    public function getDetailUpdateTime(): ?\DateTimeImmutable
    {
        return $this->detailUpdateTime;
    }

    public function setDetailUpdateTime(?\DateTimeImmutable $detailUpdateTime): void
    {
        $this->detailUpdateTime = $detailUpdateTime;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'skuId' => $this->getBaseInfo()->getSkuId(),
            'skuName' => $this->getBaseInfo()->getSkuName(),
            'brandName' => $this->getBaseInfo()->getBrandName(),
            'categoryId' => $this->getBaseInfo()->getCategoryId(),
            'categoryName' => $this->getBaseInfo()->getCategoryName(),
            'price' => $this->getBaseInfo()->getPrice(),
            'marketPrice' => $this->getBaseInfo()->getMarketPrice(),
            'stock' => $this->getBaseInfo()->getStock(),
            'stockState' => $this->getBaseInfo()->getStockState()->value,
            'state' => $this->getBaseInfo()->getState()->value,
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
            'accountId' => $this->getAccount()->getId(),
            'detailUpdateTime' => $this->getDetailUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        $skuName = $this->getBaseInfo()->getSkuName();

        return '' !== $skuName ? $skuName : 'SKU-' . $this->getId();
    }
}
