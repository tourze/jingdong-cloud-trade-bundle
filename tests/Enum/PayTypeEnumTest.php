<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\PayTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(PayTypeEnum::class)]
final class PayTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([PayTypeEnum::ONLINE, 'ONLINE', '在线付款'])]
    #[TestWith([PayTypeEnum::COD, 'COD', '货到付款'])]
    public function testValueAndLabel(PayTypeEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'ONLINE',
            'label' => '在线付款',
        ];
        $this->assertSame($expected, PayTypeEnum::ONLINE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(PayTypeEnum::ONLINE, PayTypeEnum::from('ONLINE'));
        $this->assertSame(PayTypeEnum::COD, PayTypeEnum::from('COD'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(PayTypeEnum::ONLINE, PayTypeEnum::tryFrom('ONLINE'));
        $this->assertSame(PayTypeEnum::COD, PayTypeEnum::tryFrom('COD'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, PayTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), PayTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
