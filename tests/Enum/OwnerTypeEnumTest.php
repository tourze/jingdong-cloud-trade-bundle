<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\OwnerTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OwnerTypeEnum::class)]
final class OwnerTypeEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $expected = [
            'value' => 'g',
            'label' => '自营店铺',
        ];
        $this->assertSame($expected, OwnerTypeEnum::SELF_OPERATED->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(OwnerTypeEnum::SELF_OPERATED, OwnerTypeEnum::from('g'));
        $this->assertSame(OwnerTypeEnum::POP, OwnerTypeEnum::from('p'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(OwnerTypeEnum::SELF_OPERATED, OwnerTypeEnum::tryFrom('g'));
        $this->assertSame(OwnerTypeEnum::POP, OwnerTypeEnum::tryFrom('p'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, OwnerTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), OwnerTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
