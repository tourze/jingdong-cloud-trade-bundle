<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易收货地址仓储类
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 *
 * @extends ServiceEntityRepository<DeliveryAddress>
 */
#[AsRepository(entityClass: DeliveryAddress::class)]
final class DeliveryAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryAddress::class);
    }

    /**
     * 根据用户ID查询收货地址列表
     *
     * @return DeliveryAddress[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['createdBy' => $userId], ['isDefault' => 'DESC', 'createTime' => 'DESC']);
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
     *
     * @return DeliveryAddress[]
     */
    public function findByReceiverMobile(string $receiverMobile, int $userId): array
    {
        return $this->findBy(['createdBy' => $userId, 'receiverMobile' => $receiverMobile]);
    }

    /**
     * 查询支持全球购的收货地址
     *
     * @return DeliveryAddress[]
     */
    public function findGlobalBuyAddresses(int $userId): array
    {
        return $this->findBy(['createdBy' => $userId, 'supportGlobalBuy' => true]);
    }

    public function save(DeliveryAddress $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeliveryAddress $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
