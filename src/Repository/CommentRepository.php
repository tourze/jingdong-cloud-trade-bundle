<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Comment;

/**
 * 京东云交易订单评论仓储类
 * 
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[] findAll()
 * @method Comment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * 根据订单ID查询评论
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
     */
    public function findBySkuId(string $skuId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.orderItem', 'oi')
            ->andWhere('oi.skuId = :skuId')
            ->andWhere('c.isApproved = :approved')
            ->setParameter('skuId', $skuId)
            ->setParameter('approved', true)
            ->orderBy('c.commentTime', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * 获取待审核的评论
     */
    public function findPendingApproval(): array
    {
        return $this->findBy(['isApproved' => false], ['commentTime' => 'ASC']);
    }
} 