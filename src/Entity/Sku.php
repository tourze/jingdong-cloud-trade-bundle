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
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

/**
 * 京东云交易商品SKU
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 */
#[ORM\Entity(repositoryClass: SkuRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_sku', options: ['comment' => '京东云交易商品SKU'])]
class Sku
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
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
    private SkuBaseInfo $baseInfo;
    
    /**
     * 图片信息
     */
    #[ORM\Embedded(class: SkuImageInfo::class)]
    private SkuImageInfo $imageInfo;
    
    /**
     * 大字段信息
     */
    #[ORM\Embedded(class: SkuBigFieldInfo::class)]
    private SkuBigFieldInfo $bigFieldInfo;
    
    /**
     * 图书信息（仅适用于图书类商品）
     */
    #[ORM\Embedded(class: SkuBookInfo::class)]
    private SkuBookInfo $bookInfo;
    
    /**
     * 规格和属性信息
     */
    #[ORM\Embedded(class: SkuSpecification::class)]
    private SkuSpecification $specification;
    
    /**
     * 详情更新时间
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '详情更新时间'])]
    private ?\DateTimeInterface $detailUpdatedAt = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

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

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getBaseInfo(): SkuBaseInfo
    {
        return $this->baseInfo;
    }

    public function setBaseInfo(SkuBaseInfo $baseInfo): self
    {
        $this->baseInfo = $baseInfo;
        return $this;
    }

    public function getImageInfo(): SkuImageInfo
    {
        return $this->imageInfo;
    }

    public function setImageInfo(SkuImageInfo $imageInfo): self
    {
        $this->imageInfo = $imageInfo;
        return $this;
    }

    public function getBigFieldInfo(): SkuBigFieldInfo
    {
        return $this->bigFieldInfo;
    }

    public function setBigFieldInfo(SkuBigFieldInfo $bigFieldInfo): self
    {
        $this->bigFieldInfo = $bigFieldInfo;
        return $this;
    }

    public function getBookInfo(): SkuBookInfo
    {
        return $this->bookInfo;
    }

    public function setBookInfo(SkuBookInfo $bookInfo): self
    {
        $this->bookInfo = $bookInfo;
        return $this;
    }

    public function getSpecification(): SkuSpecification
    {
        return $this->specification;
    }

    public function setSpecification(SkuSpecification $specification): self
    {
        $this->specification = $specification;
        return $this;
    }
    
    public function getDetailUpdatedAt(): ?\DateTimeInterface
    {
        return $this->detailUpdatedAt;
    }

    public function setDetailUpdatedAt(?\DateTimeInterface $detailUpdatedAt): self
    {
        $this->detailUpdatedAt = $detailUpdatedAt;
        return $this;
    }
}
