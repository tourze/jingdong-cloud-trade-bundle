<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use JingdongCloudTradeBundle\Entity\Invoice;

/**
 * 京东云交易发票信息仓储类
 * 
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[] findAll()
 * @method Invoice[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * 根据订单ID查询发票信息
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
} 