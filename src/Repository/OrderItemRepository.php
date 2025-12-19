<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\OrderItem;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易订单商品项仓储类
 *
 * @extends ServiceEntityRepository<OrderItem>
 */
#[AsRepository(entityClass: OrderItem::class)]
final class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * 根据订单ID查询订单商品项
     *
     * @return OrderItem[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    /**
     * 根据商品ID(SKU ID)查询订单商品项
     *
     * @return OrderItem[]
     */
    public function findBySkuId(string $skuId): array
    {
        return $this->findBy(['skuId' => $skuId]);
    }

    public function save(OrderItem $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderItem $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
