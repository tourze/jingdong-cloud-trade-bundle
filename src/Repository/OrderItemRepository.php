<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\OrderItem;

/**
 * 京东云交易订单商品项仓储类
 *
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[] findAll()
 * @method OrderItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * 根据订单ID查询订单商品项
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    /**
     * 根据商品ID(SKU ID)查询订单商品项
     */
    public function findBySkuId(string $skuId): array
    {
        return $this->findBy(['skuId' => $skuId]);
    }
}
