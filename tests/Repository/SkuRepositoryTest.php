<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SkuRepository::class)]
#[RunTestsInSeparateProcesses]
final class SkuRepositoryTest extends AbstractRepositoryTestCase
{
    private SkuRepository $repository;

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
    private function createSku(array $data = []): Sku
    {
        $sku = new Sku();
        $this->setupSkuAccount($sku, $data);
        $this->setupSkuBaseInfo($sku->getBaseInfo(), $data);
        $this->setupSkuOptionalData($sku, $data);

        $persistedSku = $this->persistAndFlush($sku);
        $this->assertInstanceOf(Sku::class, $persistedSku);

        return $persistedSku;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setupSkuAccount(Sku $sku, array $data): void
    {
        $account = $data['account'] ?? $this->testAccount;
        if ($account instanceof Account) {
            $sku->setAccount($account);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setupSkuBaseInfo(SkuBaseInfo $baseInfo, array $data): void
    {
        $skuId = $data['skuId'] ?? 'test-sku-' . uniqid();
        if (is_string($skuId)) {
            $baseInfo->setSkuId($skuId);
        }

        $skuName = $data['skuName'] ?? 'Test SKU';
        if (is_string($skuName)) {
            $baseInfo->setSkuName($skuName);
        }

        $price = $data['price'] ?? '100.00';
        if (is_string($price)) {
            $baseInfo->setPrice($price);
        }

        $categoryId = $data['categoryId'] ?? 'cat123';
        if (is_string($categoryId)) {
            $baseInfo->setCategoryId($categoryId);
        }

        $categoryName = $data['categoryName'] ?? 'Test Category';
        if (is_string($categoryName)) {
            $baseInfo->setCategoryName($categoryName);
        }

        $brandId = $data['brandId'] ?? 'brand123';
        if (is_string($brandId)) {
            $baseInfo->setBrandId($brandId);
        }

        $brandName = $data['brandName'] ?? 'Test Brand';
        if (is_string($brandName)) {
            $baseInfo->setBrandName($brandName);
        }

        $isGlobalBuy = $data['isGlobalBuy'] ?? false;
        if (is_bool($isGlobalBuy)) {
            $baseInfo->setIsGlobalBuy($isGlobalBuy);
        }

        if (isset($data['hasPromotion']) && true === $data['hasPromotion']) {
            $baseInfo->setCommission('10.00');
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setupSkuOptionalData(Sku $sku, array $data): void
    {
        if (isset($data['createdAt']) && $data['createdAt'] instanceof \DateTimeImmutable) {
            $sku->setCreateTime($data['createdAt']);
        }
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(SkuRepository::class, $this->repository);
    }

    public function testFindBySkuId(): void
    {
        $sku = $this->createSku(['skuId' => '12345']);

        $result = $this->repository->findBySkuId('12345');
        $this->assertNotNull($result);
        $this->assertSame($sku->getId(), $result->getId());
        $this->assertSame('12345', $result->getBaseInfo()->getSkuId());
    }

    public function testFindBySkuIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findBySkuId('non-existent');
        $this->assertNull($result);
    }

    public function testFindByCategoryId(): void
    {
        $sku1 = $this->createSku(['categoryId' => 'cat123', 'createdAt' => new \DateTimeImmutable('-1 day')]);
        $sku2 = $this->createSku(['categoryId' => 'cat123', 'createdAt' => new \DateTimeImmutable('now')]);
        $this->createSku(['categoryId' => 'cat456']);

        $result = $this->repository->findByCategoryId('cat123');

        $this->assertCount(2, $result);
        $this->assertSame($sku2->getId(), $result[0]->getId());
        $this->assertSame($sku1->getId(), $result[1]->getId());
    }

    public function testFindByCategoryIdWithLimit(): void
    {
        for ($i = 0; $i < 25; ++$i) {
            $this->createSku(['categoryId' => 'cat123']);
        }

        $result = $this->repository->findByCategoryId('cat123', ['createTime' => 'DESC'], 10);
        $this->assertCount(10, $result);
    }

    public function testFindByBrandId(): void
    {
        $sku1 = $this->createSku(['brandId' => 'brand123']);
        $sku2 = $this->createSku(['brandId' => 'brand123']);
        $this->createSku(['brandId' => 'brand456']);

        $result = $this->repository->findByBrandId('brand123');

        $this->assertCount(2, $result);
        $skuIds = array_map(fn ($sku) => $sku->getId(), $result);
        $this->assertContains($sku1->getId(), $skuIds);
        $this->assertContains($sku2->getId(), $skuIds);
    }

    public function testFindByPriceRange(): void
    {
        $sku1 = $this->createSku(['price' => '50.00']);
        $sku2 = $this->createSku(['price' => '150.00']);
        $sku3 = $this->createSku(['price' => '350.00']);
        $sku4 = $this->createSku(['price' => '600.00']);

        $result = $this->repository->findByPriceRange('100', '500');

        $this->assertCount(2, $result);
        $this->assertSame($sku2->getId(), $result[0]->getId());
        $this->assertSame($sku3->getId(), $result[1]->getId());
    }

    public function testFindByPriceRangeOrderedByPrice(): void
    {
        $sku1 = $this->createSku(['price' => '350.00']);
        $sku2 = $this->createSku(['price' => '150.00']);
        $sku3 = $this->createSku(['price' => '250.00']);

        $result = $this->repository->findByPriceRange('100', '500');

        $this->assertCount(3, $result);
        $this->assertEquals('150.00', $result[0]->getBaseInfo()->getPrice());
        $this->assertEquals('250.00', $result[1]->getBaseInfo()->getPrice());
        $this->assertEquals('350.00', $result[2]->getBaseInfo()->getPrice());
    }

    public function testFindGlobalBuySkus(): void
    {
        $sku1 = $this->createSku(['isGlobalBuy' => true]);
        $sku2 = $this->createSku(['isGlobalBuy' => true]);
        $this->createSku(['isGlobalBuy' => false]);

        $result = $this->repository->findGlobalBuySkus();

        $this->assertCount(2, $result);
        foreach ($result as $sku) {
            $this->assertTrue($sku->getBaseInfo()->isGlobalBuy());
        }
    }

    public function testFindPromotionSkus(): void
    {
        $sku1 = $this->createSku(['hasPromotion' => true]);
        $sku2 = $this->createSku(['hasPromotion' => true]);
        $this->createSku(['hasPromotion' => false]);

        $result = $this->repository->findPromotionSkus();

        $this->assertCount(2, $result);
        foreach ($result as $sku) {
            $this->assertNotNull($sku->getBaseInfo()->getCommission());
        }
    }

    public function testSearchByKeyword(): void
    {
        $sku1 = $this->createSku(['skuName' => 'iPhone 手机']);
        $sku2 = $this->createSku(['categoryName' => '手机配件']);
        $sku3 = $this->createSku(['brandName' => '手机品牌']);
        $this->createSku(['skuName' => '电脑', 'categoryName' => '电子产品', 'brandName' => '其他品牌']);

        $result = $this->repository->searchByKeyword('手机');

        $this->assertCount(3, $result);
        $skuIds = array_map(fn ($sku) => $sku->getId(), $result);
        $this->assertContains($sku1->getId(), $skuIds);
        $this->assertContains($sku2->getId(), $skuIds);
        $this->assertContains($sku3->getId(), $skuIds);
    }

    public function testSearchByKeywordWithNoResults(): void
    {
        $this->createSku(['skuName' => '电脑', 'categoryName' => '电子产品', 'brandName' => '其他品牌']);

        $result = $this->repository->searchByKeyword('手机');
        $this->assertCount(0, $result);
    }

    public function testSaveShouldPersistSku(): void
    {
        $sku = new Sku();
        $sku->setAccount($this->testAccount);

        $baseInfo = $sku->getBaseInfo();
        $baseInfo->setSkuId('SAVE_SKU_123');
        $baseInfo->setSkuName('Save Test SKU');
        $baseInfo->setPrice('299.99');
        $baseInfo->setCategoryId('save_cat');
        $baseInfo->setCategoryName('Save Category');
        $baseInfo->setBrandId('save_brand');
        $baseInfo->setBrandName('Save Brand');
        $baseInfo->setIsGlobalBuy(true);
        $baseInfo->setCommission('29.99');

        $this->repository->save($sku);

        $this->assertNotNull($sku->getId());
        $this->assertSame('SAVE_SKU_123', $sku->getBaseInfo()->getSkuId());
        $this->assertSame('Save Test SKU', $sku->getBaseInfo()->getSkuName());
        $this->assertSame('299.99', $sku->getBaseInfo()->getPrice());
        $this->assertTrue($sku->getBaseInfo()->isGlobalBuy());
        $this->assertSame('29.99', $sku->getBaseInfo()->getCommission());
    }

    public function testRemoveShouldDeleteSku(): void
    {
        $sku = $this->createSku(['skuId' => 'TO_BE_DELETED']);
        $skuId = $sku->getId();

        $this->repository->remove($sku);

        $deletedSku = $this->repository->find($skuId);
        $this->assertNull($deletedSku);
    }

    public function testFindShouldReturnSkuById(): void
    {
        $sku = $this->createSku(['skuId' => 'FIND_TEST_SKU']);

        $foundSku = $this->repository->find($sku->getId());

        $this->assertNotNull($foundSku);
        $this->assertSame($sku->getId(), $foundSku->getId());
        $this->assertSame('FIND_TEST_SKU', $foundSku->getBaseInfo()->getSkuId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllSkus(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createSku(['skuId' => 'SKU001']);
        $this->createSku(['skuId' => 'SKU002']);
        $this->createSku(['skuId' => 'SKU003']);

        $allSkus = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allSkus);
        foreach ($allSkus as $sku) {
            $this->assertInstanceOf(Sku::class, $sku);
        }
    }

    public function testFindByShouldReturnMatchingSkus(): void
    {
        $this->createSku(['isGlobalBuy' => true]);
        $this->createSku(['isGlobalBuy' => true]);
        $this->createSku(['isGlobalBuy' => false]);

        $globalBuySkus = $this->repository->findBy(['baseInfo.isGlobalBuy' => true]);

        $this->assertCount(2, $globalBuySkus);
        foreach ($globalBuySkus as $sku) {
            $this->assertTrue($sku->getBaseInfo()->isGlobalBuy());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createSku(['skuId' => "SKU{$i}"]);
        }

        $limitedSkus = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedSkus);
    }

    public function testFindOneByShouldReturnSingleSku(): void
    {
        $this->createSku(['categoryId' => 'regular_cat']);
        $specificSku = $this->createSku(['categoryId' => 'specific_cat']);

        $foundSku = $this->repository->findOneBy(['baseInfo.categoryId' => 'specific_cat']);

        $this->assertNotNull($foundSku);
        $this->assertSame($specificSku->getId(), $foundSku->getId());
        $this->assertSame('specific_cat', $foundSku->getBaseInfo()->getCategoryId());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createSku(['categoryId' => 'some_cat']);

        $result = $this->repository->findOneBy(['baseInfo.categoryId' => 'non_existent_cat']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createSku();
        $this->createSku();
        $this->createSku();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $this->createSku(['isGlobalBuy' => true]);
        $this->createSku(['isGlobalBuy' => true]);
        $this->createSku(['isGlobalBuy' => false]);

        $globalBuyCount = $this->repository->count(['baseInfo.isGlobalBuy' => true]);

        $this->assertSame(2, $globalBuyCount);
    }

    public function testSaveShouldHandleComplexData(): void
    {
        $sku = new Sku();
        $sku->setAccount($this->testAccount);

        $baseInfo = $sku->getBaseInfo();
        $baseInfo->setSkuId('COMPLEX_SKU');
        $baseInfo->setSkuName('复杂商品测试');
        $baseInfo->setPrice('1299.99');
        $baseInfo->setCategoryId('electronics');
        $baseInfo->setCategoryName('电子产品');
        $baseInfo->setBrandId('apple');
        $baseInfo->setBrandName('Apple');
        $baseInfo->setIsGlobalBuy(false);

        $this->repository->save($sku);

        $this->assertNotNull($sku->getId());
        $this->assertSame('复杂商品测试', $sku->getBaseInfo()->getSkuName());
        $this->assertSame('electronics', $sku->getBaseInfo()->getCategoryId());
        $this->assertSame('Apple', $sku->getBaseInfo()->getBrandName());
        $this->assertFalse($sku->getBaseInfo()->isGlobalBuy());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $sku = new Sku();
        $sku->setAccount($this->testAccount);

        $baseInfo = $sku->getBaseInfo();
        $baseInfo->setSkuId('NOT_PERSISTED');
        $baseInfo->setSkuName('Not Persisted SKU');
        $baseInfo->setPrice('100.00');
        $baseInfo->setCategoryId('test_cat');
        $baseInfo->setCategoryName('Test Category');
        $baseInfo->setBrandId('test_brand');
        $baseInfo->setBrandName('Test Brand');
        $baseInfo->setIsGlobalBuy(false);

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($sku);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $sku1 = $this->createSku(['categoryId' => 'test_cat', 'createdAt' => new \DateTimeImmutable('-1 hour')]);
        $sku2 = $this->createSku(['categoryId' => 'test_cat', 'createdAt' => new \DateTimeImmutable('now')]);

        $latestSku = $this->repository->findOneBy(['baseInfo.categoryId' => 'test_cat'], ['createTime' => 'DESC']);
        $this->assertNotNull($latestSku);
        $this->assertSame($sku2->getId(), $latestSku->getId());

        $earliestSku = $this->repository->findOneBy(['baseInfo.categoryId' => 'test_cat'], ['createTime' => 'ASC']);
        $this->assertNotNull($earliestSku);
        $this->assertSame($sku1->getId(), $earliestSku->getId());
    }

    public function testCountWithAssociationCriteria(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-key');
        $otherAccount->setAppSecret('other-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createSku(['account' => $this->testAccount]);
        $this->createSku(['account' => $this->testAccount]);
        $this->createSku(['account' => $otherAccount]);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame(2, $count);

        $otherCount = $this->repository->count(['account' => $otherAccount]);
        $this->assertSame(1, $otherCount);
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-key-2');
        $otherAccount->setAppSecret('other-secret-2');
        $otherAccount->setName('Other Account 2');
        $this->persistAndFlush($otherAccount);

        $sku1 = $this->createSku(['account' => $this->testAccount, 'skuId' => 'ASSOC_SKU_1']);
        $sku2 = $this->createSku(['account' => $this->testAccount, 'skuId' => 'ASSOC_SKU_2']);
        $this->createSku(['account' => $otherAccount, 'skuId' => 'ASSOC_SKU_3']);

        $skus = $this->repository->findBy(['account' => $this->testAccount]);
        $this->assertCount(2, $skus);
        $skuIds = array_map(fn ($sku) => $sku->getId(), $skus);
        $this->assertContains($sku1->getId(), $skuIds);
        $this->assertContains($sku2->getId(), $skuIds);
    }

    public function testFindByWithNullableCommissionFieldShouldReturnMatchingEntities(): void
    {
        $this->createSku(['skuId' => 'NULL_COMMISSION_1']); // commission 为 null
        $this->createSku(['skuId' => 'NULL_COMMISSION_2', 'hasPromotion' => true]); // commission 有值
        $this->createSku(['skuId' => 'NULL_COMMISSION_3']); // commission 为 null

        $skusWithNullCommission = $this->repository->findBy(['baseInfo.commission' => null]);
        $this->assertIsArray($skusWithNullCommission);
        $this->assertGreaterThanOrEqual(2, count($skusWithNullCommission));

        foreach ($skusWithNullCommission as $sku) {
            $this->assertInstanceOf(Sku::class, $sku);
            $this->assertNull($sku->getBaseInfo()->getCommission());
        }
    }

    public function testCountWithNullableCommissionFieldShouldReturnCorrectNumber(): void
    {
        $initialCount = $this->repository->count(['baseInfo.commission' => null]);

        $this->createSku(['skuId' => 'COUNT_NULL_COMMISSION_1']); // commission 为 null
        $this->createSku(['skuId' => 'COUNT_NULL_COMMISSION_2']);
        $this->createSku(['skuId' => 'COUNT_NULL_COMMISSION_3', 'hasPromotion' => true]); // commission 有值

        $count = $this->repository->count(['baseInfo.commission' => null]);
        $this->assertSame($initialCount + 2, $count);
    }

    public function testFindOneByNullableCommissionFieldShouldReturnMatchingEntity(): void
    {
        $this->createSku(['skuId' => 'COMMISSION_WITH_VALUE', 'hasPromotion' => true]);
        $nullCommissionSku = $this->createSku(['skuId' => 'NULL_COMMISSION_SKU']);

        $result = $this->repository->findOneBy(['baseInfo.commission' => null]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Sku::class, $result);
        $this->assertNull($result->getBaseInfo()->getCommission());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('count-other-account-key');
        $otherAccount->setAppSecret('count-other-account-secret');
        $otherAccount->setName('Count Other Account');
        $this->persistAndFlush($otherAccount);

        $initialCount = $this->repository->count(['account' => $this->testAccount]);

        $this->createSku(['account' => $this->testAccount, 'skuId' => 'COUNT_ACCOUNT_SKU_1']);
        $this->createSku(['account' => $this->testAccount, 'skuId' => 'COUNT_ACCOUNT_SKU_2']);
        $this->createSku(['account' => $otherAccount, 'skuId' => 'COUNT_OTHER_ACCOUNT_SKU']);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame($initialCount + 2, $count);

        $otherCount = $this->repository->count(['account' => $otherAccount]);
        $this->assertSame(1, $otherCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('find-one-other-account-key');
        $otherAccount->setAppSecret('find-one-other-account-secret');
        $otherAccount->setName('Find One Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createSku(['account' => $this->testAccount, 'skuId' => 'TEST_ACCOUNT_SKU']);
        $this->createSku(['account' => $otherAccount, 'skuId' => 'OTHER_ACCOUNT_SKU']);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Sku::class, $result);
        $this->assertSame($this->testAccount->getId(), $result->getAccount()->getId());
    }

    protected function createNewEntity(): object
    {
        $entity = new Sku();

        // 设置基本字段
        $entity->setAccount($this->testAccount);
        $baseInfo = $entity->getBaseInfo();
        $baseInfo->setSkuId('TEST_' . uniqid());
        $baseInfo->setSkuName('Test SKU ' . uniqid());
        $baseInfo->setPrice('100.00');
        $baseInfo->setState(SkuStateEnum::ON_SALE);

        return $entity;
    }

    protected function getRepository(): SkuRepository
    {
        return self::getService(SkuRepository::class);
    }
}
