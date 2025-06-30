<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\AfsService;

/**
 * 京东云交易售后服务单仓储类
 *
 * @method AfsService|null find($id, $lockMode = null, $lockVersion = null)
 * @method AfsService|null findOneBy(array $criteria, array $orderBy = null)
 * @method AfsService[] findAll()
 * @method AfsService[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AfsServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AfsService::class);
    }

    /**
     * 根据京东售后服务单号查询
     */
    public function findByAfsServiceId(string $afsServiceId): ?AfsService
    {
        return $this->findOneBy(['afsServiceId' => $afsServiceId]);
    }

    /**
     * 根据订单ID查询售后服务单
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }
} 