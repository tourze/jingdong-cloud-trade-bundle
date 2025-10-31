<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 京东商品分类
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'jingdong_cloud_trade_category', options: ['comment' => '京东商品分类表'])]
class Category implements PlainArrayInterface, AdminArrayInterface, \JsonSerializable, \Stringable
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

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, options: ['comment' => '所属京东账户'])]
    private ?Account $account = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '京东分类ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $categoryId;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '分类名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $categoryName;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '父分类ID'])]
    #[Assert\Length(max: 32)]
    private ?string $parentId = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '分类层级'])]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(value: 0)]
    private int $level;

    #[ORM\Column(type: Types::STRING, length: 1, options: ['default' => '1', 'comment' => '状态：0-无效，1-有效'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1)]
    #[Assert\Choice(choices: ['0', '1'])]
    private string $state = '1';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '分类图标URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $icon = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0, 'comment' => '排序权重'])]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(value: 0)]
    private int $sort = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false, 'comment' => '是否虚拟分类'])]
    #[Assert\NotNull]
    private bool $isVirtual = false;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '分类描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外信息（JSON格式）'])]
    #[Assert\Type(type: 'array')]
    private ?array $extraInfo = null;

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function isVirtual(): ?bool
    {
        return $this->isVirtual;
    }

    public function setIsVirtual(bool $isVirtual): void
    {
        $this->isVirtual = $isVirtual;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getExtraInfo(): ?array
    {
        return $this->extraInfo;
    }

    /**
     * @param array<string, mixed>|null $extraInfo
     */
    public function setExtraInfo(?array $extraInfo): void
    {
        $this->extraInfo = $extraInfo;
    }

    /**
     * 返回普通数组形式的数据
     */
    /**
     * @return array<string, mixed>
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
    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->retrievePlainArray();
    }

    public function __toString(): string
    {
        return '' !== $this->categoryName ? $this->categoryName : sprintf('Category #%d', $this->id ?? 0);
    }
}
