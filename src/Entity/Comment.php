<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\CommentRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_comment', options: ['comment' => '京东云交易订单评论'])]
class Comment implements PlainArrayInterface, AdminArrayInterface
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

    #[ORM\ManyToOne(targetEntity: OrderItem::class)]
    #[ORM\JoinColumn(nullable: false)]
    private OrderItem $orderItem;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '评分：1-5分，1分最低，5分最高'])]
    private string $score;

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => '评论内容'])]
    private ?string $content = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '评论图片URL列表'])]
    private ?array $images = null;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否匿名评论'])]
    private bool $isAnonymous = false;

    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '评论时间'])]
    private \DateTimeImmutable $commentTime;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否通过审核'])]
    private bool $isApproved = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '审核时间'])]
    private ?\DateTimeImmutable $approveTime = null;

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

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(OrderItem $orderItem): self
    {
        $this->orderItem = $orderItem;
        return $this;
    }

    public function getScore(): string
    {
        return $this->score;
    }

    public function setScore(string $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    public function setIsAnonymous(bool $isAnonymous): self
    {
        $this->isAnonymous = $isAnonymous;
        return $this;
    }

    public function getCommentTime(): \DateTimeImmutable
    {
        return $this->commentTime;
    }

    public function setCommentTime(\DateTimeImmutable $commentTime): self
    {
        $this->commentTime = $commentTime;
        return $this;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getApproveTime(): ?\DateTimeImmutable
    {
        return $this->approveTime;
    }

    public function setApproveTime(?\DateTimeImmutable $approveTime): self
    {
        $this->approveTime = $approveTime;
        return $this;
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrder()->getId(),
            'orderItemId' => $this->getOrderItem()->getId(),
            'skuId' => $this->getOrderItem()->getSkuId(),
            'skuName' => $this->getOrderItem()->getSkuName(),
            'score' => $this->getScore(),
            'content' => $this->getContent(),
            'images' => $this->getImages(),
            'isAnonymous' => $this->isAnonymous(),
            'commentTime' => $this->getCommentTime()->format('Y-m-d H:i:s'),
            'isApproved' => $this->isApproved(),
            'accountId' => $this->getAccount()->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'approveTime' => $this->getApproveTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
