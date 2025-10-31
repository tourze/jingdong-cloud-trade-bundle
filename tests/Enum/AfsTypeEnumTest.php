<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\AfsTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AfsTypeEnum::class)]
final class AfsTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([AfsTypeEnum::RETURN, 'RETURN', '退货'])]
    #[TestWith([AfsTypeEnum::EXCHANGE, 'EXCHANGE', '换货'])]
    #[TestWith([AfsTypeEnum::REPAIR, 'REPAIR', '维修'])]
    #[TestWith([AfsTypeEnum::REFUND_ONLY, 'REFUND_ONLY', '仅退款'])]
    public function testValueAndLabel(AfsTypeEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'RETURN',
            'label' => '退货',
        ];
        $this->assertSame($expected, AfsTypeEnum::RETURN->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(AfsTypeEnum::RETURN, AfsTypeEnum::from('RETURN'));
        $this->assertSame(AfsTypeEnum::EXCHANGE, AfsTypeEnum::from('EXCHANGE'));
        $this->assertSame(AfsTypeEnum::REPAIR, AfsTypeEnum::from('REPAIR'));
        $this->assertSame(AfsTypeEnum::REFUND_ONLY, AfsTypeEnum::from('REFUND_ONLY'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(AfsTypeEnum::RETURN, AfsTypeEnum::tryFrom('RETURN'));
        $this->assertSame(AfsTypeEnum::EXCHANGE, AfsTypeEnum::tryFrom('EXCHANGE'));
        $this->assertSame(AfsTypeEnum::REPAIR, AfsTypeEnum::tryFrom('REPAIR'));
        $this->assertSame(AfsTypeEnum::REFUND_ONLY, AfsTypeEnum::tryFrom('REFUND_ONLY'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, AfsTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), AfsTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
