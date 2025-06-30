<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;

/**
 * 京东云交易收货地址仓储类
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 *
 * @method DeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryAddress[] findAll()
 * @method DeliveryAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryAddress::class);
    }

    /**
     * 根据用户ID查询收货地址列表
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['createdBy' => $userId], ['isDefault' => 'DESC', 'createdAt' => 'DESC']);
    }

    /**
     * 查询用户的默认收货地址
     */
    public function findDefaultByUserId(int $userId): ?DeliveryAddress
    {
        return $this->findOneBy(['createdBy' => $userId, 'isDefault' => true]);
    }

    /**
     * 根据收货人手机号查询收货地址
     */
    public function findByReceiverMobile(string $receiverMobile, int $userId): array
    {
        return $this->findBy(['createdBy' => $userId, 'receiverMobile' => $receiverMobile]);
    }

    /**
     * 查询支持全球购的收货地址
     */
    public function findGlobalBuyAddresses(int $userId): array
    {
        return $this->findBy(['createdBy' => $userId, 'supportGlobalBuy' => true]);
    }
} 