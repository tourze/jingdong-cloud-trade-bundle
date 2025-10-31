<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\ItemStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ItemStateEnum::class)]
final class ItemStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([ItemStateEnum::NORMAL, 'NORMAL', '正常'])]
    #[TestWith([ItemStateEnum::RETURNING, 'RETURNING', '退货中'])]
    #[TestWith([ItemStateEnum::RETURNED, 'RETURNED', '已退货'])]
    #[TestWith([ItemStateEnum::EXCHANGING, 'EXCHANGING', '换货中'])]
    #[TestWith([ItemStateEnum::EXCHANGED, 'EXCHANGED', '已换货'])]
    public function testValueAndLabel(ItemStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'NORMAL',
            'label' => '正常',
        ];
        $this->assertSame($expected, ItemStateEnum::NORMAL->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(ItemStateEnum::NORMAL, ItemStateEnum::from('NORMAL'));
        $this->assertSame(ItemStateEnum::RETURNING, ItemStateEnum::from('RETURNING'));
        $this->assertSame(ItemStateEnum::RETURNED, ItemStateEnum::from('RETURNED'));
        $this->assertSame(ItemStateEnum::EXCHANGING, ItemStateEnum::from('EXCHANGING'));
        $this->assertSame(ItemStateEnum::EXCHANGED, ItemStateEnum::from('EXCHANGED'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(ItemStateEnum::NORMAL, ItemStateEnum::tryFrom('NORMAL'));
        $this->assertSame(ItemStateEnum::RETURNING, ItemStateEnum::tryFrom('RETURNING'));
        $this->assertSame(ItemStateEnum::RETURNED, ItemStateEnum::tryFrom('RETURNED'));
        $this->assertSame(ItemStateEnum::EXCHANGING, ItemStateEnum::tryFrom('EXCHANGING'));
        $this->assertSame(ItemStateEnum::EXCHANGED, ItemStateEnum::tryFrom('EXCHANGED'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, ItemStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), ItemStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
