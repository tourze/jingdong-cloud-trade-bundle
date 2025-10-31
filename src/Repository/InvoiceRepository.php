<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Invoice;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易发票信息仓储类
 *
 * @extends ServiceEntityRepository<Invoice>
 */
#[AsRepository(entityClass: Invoice::class)]
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * 根据订单ID查询发票信息
     *
     * @return Invoice[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    /**
     * 根据发票代码和发票号码查询发票信息
     */
    public function findByInvoiceCodeAndNumber(string $invoiceCode, string $invoiceNumber): ?Invoice
    {
        return $this->findOneBy(['invoiceCode' => $invoiceCode, 'invoiceNumber' => $invoiceNumber]);
    }

    public function save(Invoice $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Invoice $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
