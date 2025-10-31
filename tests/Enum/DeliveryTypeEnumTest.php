<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\DeliveryTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryTypeEnum::class)]
final class DeliveryTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([DeliveryTypeEnum::JD_DELIVERY, '1', '京东配送'])]
    #[TestWith([DeliveryTypeEnum::NON_JD_DELIVERY, '0', '非京东配送'])]
    public function testValueAndLabel(DeliveryTypeEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '京东配送',
        ];
        $this->assertSame($expected, DeliveryTypeEnum::JD_DELIVERY->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(DeliveryTypeEnum::JD_DELIVERY, DeliveryTypeEnum::from('1'));
        $this->assertSame(DeliveryTypeEnum::NON_JD_DELIVERY, DeliveryTypeEnum::from('0'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(DeliveryTypeEnum::JD_DELIVERY, DeliveryTypeEnum::tryFrom('1'));
        $this->assertSame(DeliveryTypeEnum::NON_JD_DELIVERY, DeliveryTypeEnum::tryFrom('0'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, DeliveryTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), DeliveryTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
