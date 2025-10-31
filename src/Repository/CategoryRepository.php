<?php

namespace JingdongCloudTradeBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCloudTradeBundle\Entity\Category;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 京东商品分类仓储类
 *
 * @extends ServiceEntityRepository<Category>
 */
#[AsRepository(entityClass: Category::class)]
class CategoryRepository extends ServiceEntityRepository
{
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
     *
     * @return Category[]
     */
    public function findByParentId(string $parentId): array
    {
        return $this->findBy(['parentId' => $parentId], ['sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 根据分类层级查询分类
     *
     * @return Category[]
     */
    public function findByLevel(int $level): array
    {
        return $this->findBy(['level' => $level], ['sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 查询所有启用的分类
     *
     * @return Category[]
     */
    public function findAllEnabled(): array
    {
        return $this->findBy(['state' => '1'], ['level' => 'ASC', 'sort' => 'ASC', 'id' => 'ASC']);
    }

    /**
     * 根据分类名称搜索分类
     *
     * @return array<Category>
     */
    public function searchByName(string $keyword): array
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.categoryName LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('c.level', 'ASC')
            ->addOrderBy('c.sort', 'ASC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof Category) : [];
    }

    /**
     * 获取分类树结构
     *
     * @return list<array{id: int, categoryId: string, categoryName: string, parentId: string|null, level: int, state: string, icon: string|null, sort: int, isVirtual: bool, description: string|null, children: list<mixed>}>
     */
    public function getCategoryTree(): array
    {
        // 获取所有分类
        $categories = $this->findBy([], ['level' => 'ASC', 'sort' => 'ASC', 'id' => 'ASC']);

        // 建立分类索引
        $indexed = [];
        foreach ($categories as $category) {
            $categoryId = $category->getCategoryId();
            if (null !== $categoryId) {
                $indexed[$categoryId] = [
                    'id' => $category->getId(),
                    'categoryId' => $categoryId,
                    'categoryName' => $category->getCategoryName() ?? '',
                    'parentId' => $category->getParentId(),
                    'level' => $category->getLevel() ?? 0,
                    'state' => $category->getState() ?? '1',
                    'icon' => $category->getIcon(),
                    'sort' => $category->getSort() ?? 0,
                    'isVirtual' => $category->isVirtual() ?? false,
                    'description' => $category->getDescription(),
                    'children' => [],
                ];
            }
        }

        // 构建树结构
        $tree = [];

        // 首先添加所有根节点
        foreach ($indexed as $categoryId => $node) {
            if (null === $node['parentId'] || '' === $node['parentId']) {
                $tree[] = $indexed[$categoryId];
            }
        }

        // 递归地为每个节点构建子树
        return $this->buildChildrenForNodes($indexed, $tree);
    }

    /**
     * 为节点构建子树
     *
     * @param array<string, array{id: int, categoryId: string, categoryName: string, parentId: string|null, level: int, state: string, icon: string|null, sort: int, isVirtual: bool, description: string|null, children: list<mixed>}> $indexed
     * @param list<array{id: int, categoryId: string, categoryName: string, parentId: string|null, level: int, state: string, icon: string|null, sort: int, isVirtual: bool, description: string|null, children: list<mixed>}> $nodes
     *
     * @return list<array{id: int, categoryId: string, categoryName: string, parentId: string|null, level: int, state: string, icon: string|null, sort: int, isVirtual: bool, description: string|null, children: list<mixed>}>
     */
    private function buildChildrenForNodes(array $indexed, array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $children = [];
            foreach ($indexed as $candidateChild) {
                if ($candidateChild['parentId'] === $node['categoryId']) {
                    $children[] = $candidateChild;
                }
            }

            // 递归构建子节点的子树
            if (count($children) > 0) {
                $node['children'] = $this->buildChildrenForNodes($indexed, $children);
            }

            $result[] = $node;
        }

        return $result;
    }

    /**
     * 获取特定分类的所有子分类ID（包括所有层级的子分类）
     *
     * @return string[]
     */
    public function getAllChildCategoryIds(string $categoryId): array
    {
        return $this->findChildIds($categoryId, []);
    }

    /**
     * 递归查找子分类ID
     *
     * @param string[] $result
     *
     * @return string[]
     */
    private function findChildIds(string $parentId, array $result): array
    {
        $children = $this->findByParentId($parentId);

        foreach ($children as $child) {
            $childId = $child->getCategoryId();
            if (null !== $childId) {
                $result[] = $childId;
                $result = $this->findChildIds($childId, $result);
            }
        }

        return $result;
    }

    /**
     * 获取分类路径（从根分类到当前分类的完整路径）
     *
     * @return list<array{id: int, categoryId: string, categoryName: string, level: int}>
     */
    public function getCategoryPath(string $categoryId): array
    {
        $path = $this->buildCategoryPath($categoryId, []);

        return array_reverse($path);
    }

    /**
     * 递归构建分类路径
     *
     * @param list<array{id: int, categoryId: string, categoryName: string, level: int}> $path
     *
     * @return list<array{id: int, categoryId: string, categoryName: string, level: int}>
     */
    private function buildCategoryPath(string $categoryId, array $path): array
    {
        $category = $this->findByCategoryId($categoryId);
        if (null === $category) {
            return $path;
        }

        $categoryIdValue = $category->getCategoryId();
        $categoryNameValue = $category->getCategoryName();
        $levelValue = $category->getLevel();

        if (null !== $categoryIdValue && null !== $categoryNameValue && null !== $levelValue) {
            $path[] = [
                'id' => $category->getId(),
                'categoryId' => $categoryIdValue,
                'categoryName' => $categoryNameValue,
                'level' => $levelValue,
            ];
        }

        $parentId = $category->getParentId();
        if (null !== $parentId) {
            $path = $this->buildCategoryPath($parentId, $path);
        }

        return $path;
    }

    public function save(Category $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
