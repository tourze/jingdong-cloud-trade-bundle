<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Comment;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易订单评论仓储类
 *
 * @extends ServiceEntityRepository<Comment>
 */
#[AsRepository(entityClass: Comment::class)]
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * 根据订单ID查询评论
     *
     * @return Comment[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId], ['commentTime' => 'DESC']);
    }

    /**
     * 根据订单商品项ID查询评论
     */
    public function findByOrderItemId(int $orderItemId): ?Comment
    {
        return $this->findOneBy(['orderItem' => $orderItemId]);
    }

    /**
     * 根据商品ID(SKU ID)查询评论
     *
     * @return array<Comment>
     */
    public function findBySkuId(string $skuId): array
    {
        $result = $this->createQueryBuilder('c')
            ->join('c.orderItem', 'oi')
            ->andWhere('oi.skuId = :skuId')
            ->andWhere('c.isApproved = :approved')
            ->setParameter('skuId', $skuId)
            ->setParameter('approved', true)
            ->orderBy('c.commentTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Comment) : [];
    }

    /**
     * 获取待审核的评论
     *
     * @return Comment[]
     */
    public function findPendingApproval(): array
    {
        return $this->findBy(['isApproved' => false], ['commentTime' => 'ASC']);
    }

    public function save(Comment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
