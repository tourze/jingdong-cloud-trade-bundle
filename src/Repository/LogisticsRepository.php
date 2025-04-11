<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use JingdongCloudTradeBundle\Entity\Logistics;

/**
 * 京东云交易物流信息仓储类
 * 
 * @method Logistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logistics[] findAll()
 * @method Logistics[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogisticsRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logistics::class);
    }

    /**
     * 根据运单号查询物流信息
     */
    public function findByWaybillCode(string $waybillCode): ?Logistics
    {
        return $this->findOneBy(['waybillCode' => $waybillCode]);
    }

    /**
     * 根据订单ID查询物流信息
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }
} 