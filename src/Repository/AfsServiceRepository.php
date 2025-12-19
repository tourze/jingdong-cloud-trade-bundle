<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\AfsService;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易售后服务单仓储类
 *
 * @extends ServiceEntityRepository<AfsService>
 */
#[AsRepository(entityClass: AfsService::class)]
final class AfsServiceRepository extends ServiceEntityRepository
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
     *
     * @return AfsService[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    public function save(AfsService $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AfsService $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
