<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\CommentRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'jd_cloud_trade_comment', options: ['comment' => '京东云交易订单评论'])]
class Comment implements PlainArrayInterface, AdminArrayInterface, \Stringable
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
    #[Assert\NotNull]
    private Account $account;

    #[ORM\ManyToOne(targetEntity: Order::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: OrderItem::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private OrderItem $orderItem;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '评分：1-5分，1分最低，5分最高'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Choice(choices: ['1', '2', '3', '4', '5'])]
    private string $score;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '评论内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $content = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '评论图片URL列表'])]
    #[Assert\All(constraints: [
        new Assert\Url(),
    ])]
    private ?array $images = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否匿名评论'])]
    #[Assert\NotNull]
    private bool $isAnonymous = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '评论时间'])]
    #[Assert\NotNull]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private \DateTimeImmutable $commentTime;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否通过审核'])]
    #[Assert\NotNull]
    private bool $isApproved = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '审核时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $approveTime = null;

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

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(OrderItem $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    public function getScore(): string
    {
        return $this->score;
    }

    public function setScore(string $score): void
    {
        $this->score = $score;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array<string>|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @param array<string>|null $images
     */
    public function setImages(?array $images): void
    {
        $this->images = $images;
    }

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    public function setIsAnonymous(bool $isAnonymous): void
    {
        $this->isAnonymous = $isAnonymous;
    }

    public function getCommentTime(): \DateTimeImmutable
    {
        return $this->commentTime;
    }

    public function setCommentTime(\DateTimeImmutable $commentTime): void
    {
        $this->commentTime = $commentTime;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): void
    {
        $this->isApproved = $isApproved;
    }

    public function getApproveTime(): ?\DateTimeImmutable
    {
        return $this->approveTime;
    }

    public function setApproveTime(?\DateTimeImmutable $approveTime): void
    {
        $this->approveTime = $approveTime;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'approveTime' => $this->getApproveTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function __toString(): string
    {
        return sprintf('Comment #%d (Score: %s)', $this->id ?? 0, $this->score ?? 'N/A');
    }
}
