<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Payment;

/**
 * 京东云交易支付信息仓储类
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[] findAll()
 * @method Payment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
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
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }
    
    /**
     * 查询指定时间范围内的支付记录
     */
    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.paymentTime >= :startDate')
            ->andWhere('p.paymentTime <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('p.paymentTime', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
