<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Payment;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易支付信息仓储类
 *
 * @extends ServiceEntityRepository<Payment>
 */
#[AsRepository(entityClass: Payment::class)]
final class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * 根据支付流水号查询支付信息
     */
    public function findByPaymentId(string $paymentId): ?Payment
    {
        return $this->findOneBy(['paymentId' => $paymentId]);
    }

    /**
     * 根据订单ID查询支付信息
     *
     * @return Payment[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    /**
     * 查询指定时间范围内的支付记录
     *
     * @return array<Payment>
     */
    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $result = $this->createQueryBuilder('p')
            ->andWhere('p.paymentTime >= :startDate')
            ->andWhere('p.paymentTime <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('p.paymentTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Payment) : [];
    }

    public function save(Payment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
