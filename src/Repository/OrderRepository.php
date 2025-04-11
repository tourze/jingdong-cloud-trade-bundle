<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Order;

/**
 * 京东云交易订单仓储类
 * 
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[] findAll()
 * @method Order[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * 根据京东订单号查询订单
     */
    public function findByOrderId(string $orderId): ?Order
    {
        return $this->findOneBy(['orderId' => $orderId]);
    }

    /**
     * 根据账户ID查询订单
     */
    public function findByAccountId(int $accountId): array
    {
        return $this->findBy(['account' => $accountId], ['createTime' => 'DESC']);
    }
} 