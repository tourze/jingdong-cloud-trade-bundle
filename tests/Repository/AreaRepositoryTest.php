<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use JingdongCloudTradeBundle\Entity\Area;
use JingdongCloudTradeBundle\Repository\AreaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AreaRepository::class)]
#[RunTestsInSeparateProcesses]
final class AreaRepositoryTest extends AbstractRepositoryTestCase
{
    private AreaRepository $repository;

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
    }

    private function createArea(): Area
    {
        $area = new Area();
        $persistedArea = $this->persistAndFlush($area);
        $this->assertInstanceOf(Area::class, $persistedArea);

        return $persistedArea;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(AreaRepository::class, $this->repository);
    }

    public function testSaveShouldPersistAreaWithFlush(): void
    {
        $area = new Area();

        $this->repository->save($area, true);
        $this->assertGreaterThan(0, $area->getId());

        $persisted = $this->repository->find($area->getId());
        $this->assertNotNull($persisted);
        $this->assertSame($area->getId(), $persisted->getId());
    }

    public function testSaveShouldPersistAreaWithoutFlush(): void
    {
        $area = new Area();

        $this->repository->save($area, false);
        self::getEntityManager()->flush();
        $this->assertGreaterThan(0, $area->getId());

        $persisted = $this->repository->find($area->getId());
        $this->assertNotNull($persisted);
        $this->assertSame($area->getId(), $persisted->getId());
    }

    public function testRemoveShouldDeleteAreaWithFlush(): void
    {
        $area = $this->createArea();
        $areaId = $area->getId();

        $this->repository->remove($area, true);

        $deleted = $this->repository->find($areaId);
        $this->assertNull($deleted);
    }

    public function testRemoveShouldDeleteAreaWithoutFlush(): void
    {
        $area = $this->createArea();
        $areaId = $area->getId();

        $this->repository->remove($area, false);
        self::getEntityManager()->flush();

        $deleted = $this->repository->find($areaId);
        $this->assertNull($deleted);
    }

    public function testFindShouldReturnAreaById(): void
    {
        $area = $this->createArea();

        $found = $this->repository->find($area->getId());
        $this->assertNotNull($found);
        $this->assertSame($area->getId(), $found->getId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $found = $this->repository->find(99999);
        $this->assertNull($found);
    }

    public function testFindAllShouldReturnAllAreas(): void
    {
        $initialCount = count($this->repository->findAll());

        $area1 = $this->createArea();
        $area2 = $this->createArea();

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);

        $ids = array_map(fn ($area) => $area->getId(), $all);
        $this->assertContains($area1->getId(), $ids);
        $this->assertContains($area2->getId(), $ids);
    }

    public function testFindAllShouldIncludeExistingAreas(): void
    {
        $all = $this->repository->findAll();
        $this->assertIsArray($all);
    }

    public function testFindByShouldReturnAreasMatchingCriteria(): void
    {
        $area1 = $this->createArea();
        $area2 = $this->createArea();

        $found = $this->repository->findBy(['id' => $area1->getId()]);
        $this->assertCount(1, $found);
        $this->assertSame($area1->getId(), $found[0]->getId());
    }

    public function testFindByShouldReturnEmptyArrayWhenNoCriteriaMatch(): void
    {
        $this->createArea();

        $found = $this->repository->findBy(['id' => 99999]);
        $this->assertCount(0, $found);
        $this->assertIsArray($found);
    }

    public function testFindOneByShouldReturnSingleAreaMatchingCriteria(): void
    {
        $area = $this->createArea();

        $found = $this->repository->findOneBy(['id' => $area->getId()]);
        $this->assertNotNull($found);
        $this->assertSame($area->getId(), $found->getId());
    }

    public function testFindOneByShouldReturnNullWhenNoCriteriaMatch(): void
    {
        $this->createArea();

        $found = $this->repository->findOneBy(['id' => 99999]);
        $this->assertNull($found);
    }

    public function testAreaEntityShouldHaveValidStringRepresentation(): void
    {
        $area = $this->createArea();
        $expectedString = sprintf('Area #%d', $area->getId());

        $this->assertSame($expectedString, (string) $area);
        $this->assertSame($expectedString, $area->__toString());
    }

    public function testMultipleSaveOperationsShouldWorkCorrectly(): void
    {
        $initialCount = count($this->repository->findAll());

        $area1 = new Area();
        $area2 = new Area();

        $this->repository->save($area1, false);
        $this->repository->save($area2, false);
        self::getEntityManager()->flush();

        $this->assertGreaterThan(0, $area1->getId());
        $this->assertGreaterThan(0, $area2->getId());
        $this->assertNotSame($area1->getId(), $area2->getId());

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);
    }

    protected function getRepository(): AreaRepository
    {
        return self::getService(AreaRepository::class);
    }

    protected function createNewEntity(): object
    {
        return new Area();
    }
}
