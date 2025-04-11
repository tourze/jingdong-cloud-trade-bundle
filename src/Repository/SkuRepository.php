<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use JingdongCloudTradeBundle\Entity\Sku;

/**
 * 京东云交易商品SKU仓储类
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 *
 * @method Sku|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sku|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sku[] findAll()
 * @method Sku[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkuRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sku::class);
    }

    /**
     * 根据SKU ID查询商品
     */
    public function findBySkuId(string $skuId): ?Sku
    {
        return $this->findOneBy(['skuId' => $skuId]);
    }

    /**
     * 根据分类ID查询商品列表
     */
    public function findByCategoryId(string $categoryId, array $orderBy = ['createdAt' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['categoryId' => $categoryId], $orderBy, $limit, $offset);
    }

    /**
     * 根据品牌ID查询商品列表
     */
    public function findByBrandId(string $brandId, array $orderBy = ['createdAt' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['brandId' => $brandId], $orderBy, $limit, $offset);
    }

    /**
     * 查询参与促销的商品列表
     */
    public function findPromotionSkus(array $orderBy = ['createdAt' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['hasPromotion' => true], $orderBy, $limit, $offset);
    }

    /**
     * 查询全球购商品列表
     */
    public function findGlobalBuySkus(array $orderBy = ['createdAt' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->findBy(['isGlobalBuy' => true], $orderBy, $limit, $offset);
    }

    /**
     * 根据关键词搜索商品
     */
    public function searchByKeyword(string $keyword, array $orderBy = ['createdAt' => 'DESC'], int $limit = 20, int $offset = 0): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.skuName LIKE :keyword OR s.categoryName LIKE :keyword OR s.brandName LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据价格区间查询商品列表
     */
    public function findByPriceRange(string $minPrice, string $maxPrice, array $orderBy = ['price' => 'ASC'], int $limit = 20, int $offset = 0): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.price >= :minPrice')
            ->andWhere('s.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('s.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
