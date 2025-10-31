<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SkuStateEnum::class)]
final class SkuStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([SkuStateEnum::ON_SALE, '1', '上架'])]
    #[TestWith([SkuStateEnum::OFF_SALE, '0', '下架'])]
    public function testValueAndLabel(SkuStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '上架',
        ];
        $this->assertSame($expected, SkuStateEnum::ON_SALE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(SkuStateEnum::ON_SALE, SkuStateEnum::from('1'));
        $this->assertSame(SkuStateEnum::OFF_SALE, SkuStateEnum::from('0'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(SkuStateEnum::ON_SALE, SkuStateEnum::tryFrom('1'));
        $this->assertSame(SkuStateEnum::OFF_SALE, SkuStateEnum::tryFrom('0'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, SkuStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), SkuStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
