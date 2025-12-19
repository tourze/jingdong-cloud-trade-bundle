<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Order;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易订单仓储类
 *
 * @extends ServiceEntityRepository<Order>
 */
#[AsRepository(entityClass: Order::class)]
final class OrderRepository extends ServiceEntityRepository
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
     *
     * @return Order[]
     */
    public function findByAccountId(int $accountId): array
    {
        return $this->findBy(['account' => $accountId], ['createTime' => 'DESC']);
    }

    public function save(Order $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
