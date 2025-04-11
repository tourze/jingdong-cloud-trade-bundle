<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use JingdongCloudTradeBundle\Entity\Category;

/**
 * 京东商品分类仓储类
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[] findAll()
 * @method Category[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * 根据分类ID查询分类信息
     */
    public function findByCategoryId(string $categoryId): ?Category
    {
        return $this->findOneBy(['categoryId' => $categoryId]);
    }

    /**
     * 根据父分类ID查询子分类
     */
    public function findByParentId(string $parentId): array
    {
        return $this->findBy(['parentId' => $parentId], ['sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 根据分类层级查询分类
     */
    public function findByLevel(int $level): array
    {
        return $this->findBy(['level' => $level], ['sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 查询所有启用的分类
     */
    public function findAllEnabled(): array
    {
        return $this->findBy(['state' => '1'], ['level' => 'ASC', 'sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 根据分类名称搜索分类
     */
    public function searchByName(string $keyword): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.categoryName LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('c.level', 'ASC')
            ->addOrderBy('c.sort', 'ASC')
            ->addOrderBy('c.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * 获取分类树结构
     */
    public function getCategoryTree(): array
    {
        // 获取所有分类
        $categories = $this->findBy([], ['level' => 'ASC', 'sort' => 'ASC', 'id' => 'ASC']);
        
        // 建立分类索引
        $indexed = [];
        foreach ($categories as $category) {
            $indexed[$category->getCategoryId()] = [
                'id' => $category->getId(),
                'categoryId' => $category->getCategoryId(),
                'categoryName' => $category->getCategoryName(),
                'parentId' => $category->getParentId(),
                'level' => $category->getLevel(),
                'state' => $category->getState(),
                'icon' => $category->getIcon(),
                'sort' => $category->getSort(),
                'isVirtual' => $category->isVirtual(),
                'description' => $category->getDescription(),
                'children' => [],
            ];
        }
        
        // 构建树结构
        $tree = [];
        foreach ($indexed as $id => &$node) {
            // 如果是非根节点，添加到父节点的children中
            if (!empty($node['parentId']) && isset($indexed[$node['parentId']])) {
                $indexed[$node['parentId']]['children'][] = &$node;
            } else {
                // 根节点直接添加到树中
                $tree[] = &$node;
            }
        }
        
        return $tree;
    }

    /**
     * 获取特定分类的所有子分类ID（包括所有层级的子分类）
     */
    public function getAllChildCategoryIds(string $categoryId): array
    {
        $result = [];
        $this->findChildIds($categoryId, $result);
        return $result;
    }

    /**
     * 递归查找子分类ID
     */
    private function findChildIds(string $parentId, array &$result): void
    {
        $children = $this->findByParentId($parentId);

        foreach ($children as $child) {
            $childId = $child->getCategoryId();
            $result[] = $childId;
            $this->findChildIds($childId, $result);
        }
    }

    /**
     * 获取分类路径（从根分类到当前分类的完整路径）
     */
    public function getCategoryPath(string $categoryId): array
    {
        $path = [];
        $this->buildCategoryPath($categoryId, $path);
        return array_reverse($path);
    }

    /**
     * 递归构建分类路径
     */
    private function buildCategoryPath(string $categoryId, array &$path): void
    {
        $category = $this->findByCategoryId($categoryId);
        if (!$category) {
            return;
        }
        
        $path[] = [
            'id' => $category->getId(),
            'categoryId' => $category->getCategoryId(),
            'categoryName' => $category->getCategoryName(),
            'level' => $category->getLevel(),
        ];
        
        if ($category->getParentId()) {
            $this->buildCategoryPath($category->getParentId(), $path);
        }
    }
}
