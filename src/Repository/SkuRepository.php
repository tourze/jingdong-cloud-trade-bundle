<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Sku;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东云交易商品SKU仓储类
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 *
 * @extends ServiceEntityRepository<Sku>
 */
#[AsRepository(entityClass: Sku::class)]
class SkuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sku::class);
    }

    /**
     * 根据SKU ID查询商品
     */
    public function findBySkuId(string $skuId): ?Sku
    {
        return $this->findOneBy(['baseInfo.skuId' => $skuId]);
    }

    /**
     * 根据分类ID查询商品列表
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return Sku[]
     */
    public function findByCategoryId(string $categoryId, array $orderBy = ['createTime' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['baseInfo.categoryId' => $categoryId], $orderBy, $limit, $offset);
    }

    /**
     * 根据品牌ID查询商品列表
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return Sku[]
     */
    public function findByBrandId(string $brandId, array $orderBy = ['createTime' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['baseInfo.brandId' => $brandId], $orderBy, $limit, $offset);
    }

    /**
     * 查询参与促销的商品列表
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return array<Sku>
     */
    public function findPromotionSkus(array $orderBy = ['createTime' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.baseInfo.commission IS NOT NULL')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Sku) : [];
    }

    /**
     * 查询全球购商品列表
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return Sku[]
     */
    public function findGlobalBuySkus(array $orderBy = ['createTime' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['baseInfo.isGlobalBuy' => true], $orderBy, $limit, $offset);
    }

    /**
     * 根据关键词搜索商品
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return array<Sku>
     */
    public function searchByKeyword(string $keyword, array $orderBy = ['createTime' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.baseInfo.skuName LIKE :keyword OR s.baseInfo.categoryName LIKE :keyword OR s.baseInfo.brandName LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Sku) : [];
    }

    /**
     * 根据价格区间查询商品列表
     *
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return array<Sku>
     */
    public function findByPriceRange(string $minPrice, string $maxPrice, array $orderBy = ['price' => 'ASC'], int $limit = 20, int $offset = 0): array
    {
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.baseInfo.price >= :minPrice')
            ->andWhere('s.baseInfo.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('s.baseInfo.price', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Sku) : [];
    }

    public function save(Sku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
