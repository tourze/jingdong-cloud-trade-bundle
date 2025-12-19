<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Logistics;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易物流信息仓储类
 *
 * @extends ServiceEntityRepository<Logistics>
 */
#[AsRepository(entityClass: Logistics::class)]
final class LogisticsRepository extends ServiceEntityRepository
{
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
     *
     * @return Logistics[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    public function save(Logistics $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Logistics $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
