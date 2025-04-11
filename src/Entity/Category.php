<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\CategoryRepository;
use JsonSerializable;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

/**
 * 京东商品分类
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'jingdong_cloud_trade_category', options: ['comment' => '京东商品分类表'])]
class Category implements JsonSerializable
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

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '所属京东账户'])]
    private Account $account;

    /**
     * 分类ID
     */
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '京东分类ID'])]
    private string $categoryId;

    /**
     * 分类名称
     */
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '分类名称'])]
    private string $categoryName;

    /**
     * 父分类ID
     */
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '父分类ID'])]
    private ?string $parentId = null;

    /**
     * 分类层级
     */
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '分类层级'])]
    private int $level;

    /**
     * 状态，0-无效 1-有效
     */
    #[ORM\Column(type: Types::STRING, length: 1, options: ['default' => '1', 'comment' => '状态：0-无效，1-有效'])]
    private string $state = '1';

    /**
     * 图标
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '分类图标URL'])]
    private ?string $icon = null;

    /**
     * 排序
     */
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0, 'comment' => '排序权重'])]
    private int $sort = 0;

    /**
     * 是否虚拟分类
     */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false, 'comment' => '是否虚拟分类'])]
    private bool $isVirtual = false;

    /**
     * 分类描述
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '分类描述'])]
    private ?string $description = null;

    /**
     * 额外信息（JSON格式）
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外信息（JSON格式）'])]
    private ?array $extraInfo = null;

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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function isVirtual(): ?bool
    {
        return $this->isVirtual;
    }

    public function setIsVirtual(bool $isVirtual): self
    {
        $this->isVirtual = $isVirtual;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExtraInfo(): ?array
    {
        return $this->extraInfo;
    }

    public function setExtraInfo(?array $extraInfo): self
    {
        $this->extraInfo = $extraInfo;

        return $this;
    }

    /**
     * 返回普通数组形式的数据
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'categoryId' => $this->getCategoryId(),
            'categoryName' => $this->getCategoryName(),
            'parentId' => $this->getParentId(),
            'level' => $this->getLevel(),
            'state' => $this->getState(),
            'icon' => $this->getIcon(),
            'sort' => $this->getSort(),
            'isVirtual' => $this->isVirtual(),
            'description' => $this->getDescription(),
            'extraInfo' => $this->getExtraInfo(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * 返回管理后台使用的数组数据
     */
    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->retrievePlainArray();
    }
}
