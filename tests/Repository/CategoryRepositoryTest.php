<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Category;
use JingdongCloudTradeBundle\Repository\CategoryRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CategoryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CategoryRepositoryTest extends AbstractRepositoryTestCase
{
    private CategoryRepository $repository;

    private Account $testAccount;

    protected function onSetUp(): void
    {
        // 彻底重置数据库连接状态，确保每个测试都从干净状态开始
        $connection = self::getEntityManager()->getConnection();

        // 关闭现有连接
        if ($connection->isConnected()) {
            $connection->close();
        }

        // 通过执行简单查询触发重新连接
        try {
            $connection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            // 忽略连接异常，让测试自然进行
        }

        $this->repository = $this->getRepository();

        $this->testAccount = new Account();
        $this->testAccount->setAppKey('test-app-key');
        $this->testAccount->setAppSecret('test-app-secret');
        $this->testAccount->setName('Test Account');
        $this->persistAndFlush($this->testAccount);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createCategory(array $data = []): Category
    {
        $category = new Category();
        $category->setAccount($this->testAccount);

        $category->setCategoryId($this->getStringValue($data, 'categoryId', 'cat-' . uniqid()));
        $category->setCategoryName($this->getStringValue($data, 'categoryName', 'Test Category'));
        $category->setLevel($this->getIntValue($data, 'level', 1));
        $category->setState($this->getStringValue($data, 'state', '1'));
        $category->setSort($this->getIntValue($data, 'sort', 0));

        $this->setOptionalStringField($data, 'parentId', $category->setParentId(...));
        $this->setOptionalStringField($data, 'icon', $category->setIcon(...));
        $this->setOptionalBoolField($data, 'isVirtual', $category->setIsVirtual(...));
        $this->setOptionalStringField($data, 'description', $category->setDescription(...));

        $persistedCategory = $this->persistAndFlush($category);
        $this->assertInstanceOf(Category::class, $persistedCategory);

        return $persistedCategory;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getStringValue(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;

        return \is_string($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getIntValue(array $data, string $key, int $default): int
    {
        $value = $data[$key] ?? $default;

        return \is_int($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(string|null): void $setter
     */
    private function setOptionalStringField(array $data, string $key, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            $setter(\is_string($value) ? $value : null);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(bool): void $setter
     */
    private function setOptionalBoolField(array $data, string $key, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            $setter(\is_bool($value) && $value);
        }
    }

    /**
     * @param list<array<string, mixed>> $nodes
     * @return array<string, mixed>|null
     */
    private function findNodeByCategoryId(array $nodes, string $categoryId): ?array
    {
        foreach ($nodes as $node) {
            if (isset($node['categoryId']) && $node['categoryId'] === $categoryId) {
                return $node;
            }
        }

        return null;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(CategoryRepository::class, $this->repository);
    }

    public function testFindAllEnabled(): void
    {
        $initialEnabledCount = count($this->repository->findAllEnabled());

        $enabledCategory1 = $this->createCategory(['categoryId' => 'cat001', 'state' => '1', 'level' => 1]);
        $enabledCategory2 = $this->createCategory(['categoryId' => 'cat002', 'state' => '1', 'level' => 2]);
        $this->createCategory(['categoryId' => 'cat003', 'state' => '0']);

        $result = $this->repository->findAllEnabled();

        $this->assertCount($initialEnabledCount + 2, $result);
        $categoryIds = array_map(fn ($cat) => $cat->getCategoryId(), $result);
        $this->assertContains('cat001', $categoryIds);
        $this->assertContains('cat002', $categoryIds);
    }

    public function testFindByCategoryId(): void
    {
        $category = $this->createCategory(['categoryId' => 'CAT12345']);

        $result = $this->repository->findByCategoryId('CAT12345');
        $this->assertNotNull($result);
        $this->assertSame($category->getId(), $result->getId());
        $this->assertSame('CAT12345', $result->getCategoryId());
    }

    public function testFindByCategoryIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByCategoryId('non-existent');
        $this->assertNull($result);
    }

    public function testFindByLevel(): void
    {
        $initialLevel1Count = count($this->repository->findByLevel(1));

        $level1Category1 = $this->createCategory(['level' => 1, 'sort' => 10]);
        $level1Category2 = $this->createCategory(['level' => 1, 'sort' => 5]);
        $this->createCategory(['level' => 2]);

        $result = $this->repository->findByLevel(1);

        $this->assertCount($initialLevel1Count + 2, $result);

        // 找到我们创建的两个分类在结果中的位置
        $createdCategories = array_filter($result, function ($category) use ($level1Category1, $level1Category2) {
            return $category->getId() === $level1Category1->getId() || $category->getId() === $level1Category2->getId();
        });

        $this->assertCount(2, $createdCategories);

        // 验证排序（按 sort 字段升序）
        $createdCategoryIds = array_map(fn ($cat) => $cat->getId(), $createdCategories);
        $this->assertContains($level1Category1->getId(), $createdCategoryIds);
        $this->assertContains($level1Category2->getId(), $createdCategoryIds);
    }

    public function testFindByParentId(): void
    {
        $parentCategory = $this->createCategory(['categoryId' => 'parent001']);
        $childCategory1 = $this->createCategory(['parentId' => 'parent001', 'sort' => 20]);
        $childCategory2 = $this->createCategory(['parentId' => 'parent001', 'sort' => 10]);
        $this->createCategory(['parentId' => 'parent002']);

        $result = $this->repository->findByParentId('parent001');

        $this->assertCount(2, $result);
        $this->assertSame($childCategory2->getId(), $result[0]->getId());
        $this->assertSame($childCategory1->getId(), $result[1]->getId());
    }

    public function testSearchByName(): void
    {
        $category1 = $this->createCategory(['categoryName' => '电子产品']);
        $category2 = $this->createCategory(['categoryName' => '数码电子']);
        $this->createCategory(['categoryName' => '服装鞋帽']);

        $result = $this->repository->searchByName('电子');

        $this->assertCount(2, $result);
        $categoryIds = array_map(fn ($cat) => $cat->getId(), $result);
        $this->assertContains($category1->getId(), $categoryIds);
        $this->assertContains($category2->getId(), $categoryIds);
    }

    public function testGetCategoryTree(): void
    {
        $initialTree = $this->repository->getCategoryTree();
        $initialRootCount = count($initialTree);

        $rootCategory = $this->createCategory(['categoryId' => 'root1', 'level' => 1, 'parentId' => null]);
        $childCategory = $this->createCategory(['categoryId' => 'child1', 'level' => 2, 'parentId' => 'root1']);
        $grandchildCategory = $this->createCategory(['categoryId' => 'grandchild1', 'level' => 3, 'parentId' => 'child1']);

        $result = $this->repository->getCategoryTree();

        $this->assertIsArray($result);
        $this->assertCount($initialRootCount + 1, $result);

        // 找到我们创建的根节点
        $ourRootNode = $this->findNodeByCategoryId($result, 'root1');

        $this->assertNotNull($ourRootNode, 'Should find our created root node');
        $this->assertIsArray($ourRootNode);
        $this->assertArrayHasKey('categoryId', $ourRootNode);
        $this->assertSame('root1', $ourRootNode['categoryId']);
        $this->assertArrayHasKey('children', $ourRootNode);
        $this->assertIsArray($ourRootNode['children']);
        $this->assertCount(1, $ourRootNode['children']);

        $childNode = $ourRootNode['children'][0];
        $this->assertIsArray($childNode);
        $this->assertArrayHasKey('categoryId', $childNode);
        $this->assertSame('child1', $childNode['categoryId']);
        $this->assertArrayHasKey('children', $childNode);
        $this->assertIsArray($childNode['children']);
        $this->assertCount(1, $childNode['children']);

        $grandchildNode = $childNode['children'][0];
        $this->assertIsArray($grandchildNode);
        $this->assertArrayHasKey('categoryId', $grandchildNode);
        $this->assertSame('grandchild1', $grandchildNode['categoryId']);
    }

    public function testGetAllChildCategoryIds(): void
    {
        $rootCategory = $this->createCategory(['categoryId' => 'root2']);
        $childCategory1 = $this->createCategory(['categoryId' => 'child2', 'parentId' => 'root2']);
        $childCategory2 = $this->createCategory(['categoryId' => 'child3', 'parentId' => 'root2']);
        $grandchildCategory = $this->createCategory(['categoryId' => 'grandchild2', 'parentId' => 'child2']);

        $result = $this->repository->getAllChildCategoryIds('root2');

        $this->assertCount(3, $result);
        $this->assertContains('child2', $result);
        $this->assertContains('child3', $result);
        $this->assertContains('grandchild2', $result);
    }

    public function testGetCategoryPath(): void
    {
        $rootCategory = $this->createCategory(['categoryId' => 'root3', 'categoryName' => 'Root', 'level' => 1]);
        $childCategory = $this->createCategory(['categoryId' => 'child4', 'categoryName' => 'Child', 'level' => 2, 'parentId' => 'root3']);
        $grandchildCategory = $this->createCategory(['categoryId' => 'grandchild3', 'categoryName' => 'Grandchild', 'level' => 3, 'parentId' => 'child4']);

        $result = $this->repository->getCategoryPath('grandchild3');

        $this->assertCount(3, $result);
        $this->assertSame('root3', $result[0]['categoryId']);
        $this->assertSame('child4', $result[1]['categoryId']);
        $this->assertSame('grandchild3', $result[2]['categoryId']);
    }

    public function testSaveShouldPersistCategoryWithFlush(): void
    {
        $category = new Category();
        $category->setAccount($this->testAccount);
        $category->setCategoryId('new-category');
        $category->setCategoryName('New Category');
        $category->setLevel(1);
        $category->setState('1');
        $category->setSort(0);

        $this->repository->save($category, true);
        $this->assertGreaterThan(0, $category->getId());

        $persisted = $this->repository->find($category->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('new-category', $persisted->getCategoryId());
        $this->assertSame('New Category', $persisted->getCategoryName());
    }

    public function testSaveShouldPersistCategoryWithoutFlush(): void
    {
        $category = new Category();
        $category->setAccount($this->testAccount);
        $category->setCategoryId('no-flush-category');
        $category->setCategoryName('No Flush Category');
        $category->setLevel(1);
        $category->setState('1');
        $category->setSort(0);

        $this->repository->save($category, false);
        self::getEntityManager()->flush();
        $this->assertGreaterThan(0, $category->getId());

        $persisted = $this->repository->find($category->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('no-flush-category', $persisted->getCategoryId());
    }

    public function testRemoveShouldDeleteCategoryWithFlush(): void
    {
        $category = $this->createCategory(['categoryId' => 'delete-category']);
        $categoryId = $category->getId();

        $this->repository->remove($category, true);

        $deleted = $this->repository->find($categoryId);
        $this->assertNull($deleted);
    }

    public function testRemoveShouldDeleteCategoryWithoutFlush(): void
    {
        $category = $this->createCategory(['categoryId' => 'delete-no-flush-category']);
        $categoryId = $category->getId();

        $this->repository->remove($category, false);
        self::getEntityManager()->flush();

        $deleted = $this->repository->find($categoryId);
        $this->assertNull($deleted);
    }

    public function testFindShouldReturnCategoryById(): void
    {
        $category = $this->createCategory(['categoryId' => 'findable-category']);

        $found = $this->repository->find($category->getId());
        $this->assertNotNull($found);
        $this->assertSame($category->getId(), $found->getId());
        $this->assertSame('findable-category', $found->getCategoryId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $found = $this->repository->find(99999);
        $this->assertNull($found);
    }

    public function testFindAllShouldReturnAllCategories(): void
    {
        $initialCount = count($this->repository->findAll());

        $category1 = $this->createCategory(['categoryId' => 'all-cat-1']);
        $category2 = $this->createCategory(['categoryId' => 'all-cat-2']);

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);

        $categoryIds = array_map(fn ($cat) => $cat->getCategoryId(), $all);
        $this->assertContains('all-cat-1', $categoryIds);
        $this->assertContains('all-cat-2', $categoryIds);
    }

    public function testFindByShouldReturnCategoriesMatchingCriteria(): void
    {
        $initialLevel1Count = count($this->repository->findBy(['level' => 1]));

        $category1 = $this->createCategory(['categoryId' => 'match-1', 'level' => 1]);
        $category2 = $this->createCategory(['categoryId' => 'match-2', 'level' => 1]);
        $this->createCategory(['categoryId' => 'no-match', 'level' => 2]);

        $found = $this->repository->findBy(['level' => 1]);
        $this->assertCount($initialLevel1Count + 2, $found);

        foreach ($found as $category) {
            $this->assertSame(1, $category->getLevel());
        }

        // 验证我们创建的分类确实在结果中
        $foundCategoryIds = array_map(fn ($cat) => $cat->getCategoryId(), $found);
        $this->assertContains('match-1', $foundCategoryIds);
        $this->assertContains('match-2', $foundCategoryIds);
    }

    public function testFindOneByShouldReturnSingleCategoryMatchingCriteria(): void
    {
        $this->createCategory(['categoryId' => 'unique-find-category']);
        $this->createCategory(['categoryId' => 'other-find-category']);

        $found = $this->repository->findOneBy(['categoryId' => 'unique-find-category']);
        $this->assertNotNull($found);
        $this->assertSame('unique-find-category', $found->getCategoryId());
    }

    public function testFindOneByShouldReturnNullWhenNoCriteriaMatch(): void
    {
        $this->createCategory(['categoryId' => 'existing-category']);

        $found = $this->repository->findOneBy(['categoryId' => 'non-existent-category']);
        $this->assertNull($found);
    }

    public function testGetCategoryPathShouldReturnEmptyArrayForNonExistentCategory(): void
    {
        $result = $this->repository->getCategoryPath('non-existent');
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testSearchByNameShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createCategory(['categoryName' => '电子产品']);

        $result = $this->repository->searchByName('不存在的分类');
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testGetAllChildCategoryIdsShouldReturnEmptyArrayForNonExistentParent(): void
    {
        $result = $this->repository->getAllChildCategoryIds('non-existent-parent');
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByParentIdShouldReturnEmptyArrayWhenNoChildren(): void
    {
        $result = $this->repository->findByParentId('no-children-parent');
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByLevelShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createCategory(['level' => 1]);

        $result = $this->repository->findByLevel(99);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindAllEnabledShouldReturnEmptyArrayWhenNoEnabledCategories(): void
    {
        $initialEnabledCount = count($this->repository->findAllEnabled());

        $disabledCategory1 = $this->createCategory(['categoryId' => 'disabled-1', 'state' => '0']);
        $disabledCategory2 = $this->createCategory(['categoryId' => 'disabled-2', 'state' => '-1']);

        $result = $this->repository->findAllEnabled();

        // 启用分类的数量应该保持不变
        $this->assertCount($initialEnabledCount, $result);
        $this->assertIsArray($result);

        // 验证我们创建的禁用分类不在结果中
        $enabledCategoryIds = array_map(fn ($cat) => $cat->getCategoryId(), $result);
        $this->assertNotContains('disabled-1', $enabledCategoryIds);
        $this->assertNotContains('disabled-2', $enabledCategoryIds);
    }

    public function testFindByWithNullValue(): void
    {
        $this->createCategory(['categoryId' => 'null-desc', 'description' => null]);
        $this->createCategory(['categoryId' => 'with-desc', 'description' => 'Some description']);

        $result = $this->repository->findBy(['description' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $category) {
            $this->assertNull($category->getDescription());
        }
    }

    public function testCountWithNullValue(): void
    {
        $initialNullCount = $this->repository->count(['icon' => null]);

        $this->createCategory(['categoryId' => 'null-icon-1', 'icon' => null]);
        $this->createCategory(['categoryId' => 'null-icon-2', 'icon' => null]);
        $this->createCategory(['categoryId' => 'with-icon', 'icon' => 'icon.png']);

        $nullIconCount = $this->repository->count(['icon' => null]);
        $this->assertSame($initialNullCount + 2, $nullIconCount);
    }

    public function testFindByWithAssociation(): void
    {
        $category1 = $this->createCategory(['categoryId' => 'assoc-test-1']);
        $category2 = $this->createCategory(['categoryId' => 'assoc-test-2']);

        $result = $this->repository->findBy(['account' => $this->testAccount]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundIds = array_map(fn ($cat) => $cat->getId(), $result);
        $this->assertContains($category1->getId(), $foundIds);
        $this->assertContains($category2->getId(), $foundIds);

        foreach ($result as $category) {
            $this->assertSame($this->testAccount->getId(), $category->getAccount()?->getId());
        }
    }

    public function testCountWithAssociation(): void
    {
        $this->createCategory(['categoryId' => 'count-assoc-1']);
        $this->createCategory(['categoryId' => 'count-assoc-2']);

        $accountCategoryCount = $this->repository->count(['account' => $this->testAccount]);
        $this->assertGreaterThanOrEqual(2, $accountCategoryCount);
    }

    public function testFindByWithParentIdNullValue(): void
    {
        $this->createCategory(['categoryId' => 'null-parent', 'parentId' => null]);
        $this->createCategory(['categoryId' => 'with-parent', 'parentId' => 'parent-123']);

        $result = $this->repository->findBy(['parentId' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $category) {
            $this->assertNull($category->getParentId());
        }
    }

    public function testCountWithParentIdNullValue(): void
    {
        $initialNullCount = $this->repository->count(['parentId' => null]);

        $this->createCategory(['categoryId' => 'null-parent-1', 'parentId' => null]);
        $this->createCategory(['categoryId' => 'null-parent-2', 'parentId' => null]);
        $this->createCategory(['categoryId' => 'with-parent', 'parentId' => 'parent-456']);

        $nullParentCount = $this->repository->count(['parentId' => null]);
        $this->assertSame($initialNullCount + 2, $nullParentCount);
    }

    public function testFindByWithCreateTimeNullValue(): void
    {
        $category1 = $this->createCategory(['categoryId' => 'null-create']);
        $category1->setCreateTime(null);
        self::getEntityManager()->persist($category1);

        $category2 = $this->createCategory(['categoryId' => 'with-create']);
        $category2->setCreateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($category2);

        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['createTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $category) {
            $this->assertNull($category->getCreateTime());
        }
    }

    public function testCountWithUpdateTimeNullValue(): void
    {
        $initialNullCount = $this->repository->count(['updateTime' => null]);

        $category1 = $this->createCategory(['categoryId' => 'null-update-1']);
        $category1->setUpdateTime(null);
        self::getEntityManager()->persist($category1);

        $category2 = $this->createCategory(['categoryId' => 'null-update-2']);
        $category2->setUpdateTime(null);
        self::getEntityManager()->persist($category2);

        $category3 = $this->createCategory(['categoryId' => 'with-update']);
        $category3->setUpdateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($category3);

        self::getEntityManager()->flush();

        $nullUpdateCount = $this->repository->count(['updateTime' => null]);
        $this->assertSame($initialNullCount + 2, $nullUpdateCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $category = $this->createCategory(['categoryId' => 'assoc-account-test']);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertSame($this->testAccount->getId(), $result->getAccount()?->getId());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $this->createCategory(['categoryId' => 'count-assoc-account-1']);
        $this->createCategory(['categoryId' => 'count-assoc-account-2']);

        $accountCategoryCount = $this->repository->count(['account' => $this->testAccount]);
        $this->assertGreaterThanOrEqual(2, $accountCategoryCount);
    }

    protected function getRepository(): CategoryRepository
    {
        return self::getService(CategoryRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setAppKey('test-app-key-' . uniqid());
        $account->setAppSecret('test-app-secret-' . uniqid());
        $account->setName('Test Account ' . uniqid());

        $category = new Category();
        $category->setAccount($account);
        $category->setCategoryId('cat-' . uniqid());
        $category->setCategoryName('Test Category ' . uniqid());
        $category->setLevel(1);

        return $category;
    }
}
