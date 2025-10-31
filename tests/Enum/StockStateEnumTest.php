<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\StockStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(StockStateEnum::class)]
final class StockStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([StockStateEnum::IN_STOCK, '1', '有货'])]
    #[TestWith([StockStateEnum::OUT_OF_STOCK, '0', '无货'])]
    public function testValueAndLabel(StockStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '有货',
        ];
        $this->assertSame($expected, StockStateEnum::IN_STOCK->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(StockStateEnum::IN_STOCK, StockStateEnum::from('1'));
        $this->assertSame(StockStateEnum::OUT_OF_STOCK, StockStateEnum::from('0'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(StockStateEnum::IN_STOCK, StockStateEnum::tryFrom('1'));
        $this->assertSame(StockStateEnum::OUT_OF_STOCK, StockStateEnum::tryFrom('0'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, StockStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), StockStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
