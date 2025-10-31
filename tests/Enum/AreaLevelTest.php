<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\AreaLevel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AreaLevel::class)]
final class AreaLevelTest extends AbstractEnumTestCase
{
    #[TestWith([AreaLevel::PROVINCE, 1, '省级'])]
    #[TestWith([AreaLevel::CITY, 2, '市级'])]
    #[TestWith([AreaLevel::AREA, 3, '县级'])]
    #[TestWith([AreaLevel::TOWN, 4, '镇级'])]
    public function testValueAndLabel(AreaLevel $enum, int $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 1,
            'label' => '省级',
        ];
        $this->assertSame($expected, AreaLevel::PROVINCE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(AreaLevel::PROVINCE, AreaLevel::from(1));
        $this->assertSame(AreaLevel::CITY, AreaLevel::from(2));
        $this->assertSame(AreaLevel::AREA, AreaLevel::from(3));
        $this->assertSame(AreaLevel::TOWN, AreaLevel::from(4));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(AreaLevel::PROVINCE, AreaLevel::tryFrom(1));
        $this->assertSame(AreaLevel::CITY, AreaLevel::tryFrom(2));
        $this->assertSame(AreaLevel::AREA, AreaLevel::tryFrom(3));
        $this->assertSame(AreaLevel::TOWN, AreaLevel::tryFrom(4));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, AreaLevel::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), AreaLevel::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
