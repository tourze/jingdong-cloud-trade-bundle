<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Area;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Area::class)]
final class AreaTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Area();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'createTime' => ['createTime', new \DateTimeImmutable('2023-01-01 12:00:00')];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable('2023-01-02 12:00:00')];
    }

    /**
     * 测试 Area 实体的基本功能
     */
    public function testAreaEntity(): void
    {
        $area = new Area();
        $this->assertInstanceOf(Area::class, $area);
    }

    /**
     * 测试 Area 实体的 getId 方法
     */
    public function testGetId(): void
    {
        $area = new Area();
        $this->assertSame(0, $area->getId());
    }

    /**
     * 测试 Area 实体的 __toString 方法
     */
    public function testToString(): void
    {
        $area = new Area();
        $this->assertSame('Area #0', (string) $area);
    }
}
