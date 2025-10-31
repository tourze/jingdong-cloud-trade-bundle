<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(PaymentMethodEnum::class)]
final class PaymentMethodEnumTest extends AbstractEnumTestCase
{
    #[TestWith([PaymentMethodEnum::ONLINE, '1', '在线支付'])]
    #[TestWith([PaymentMethodEnum::COD, '2', '货到付款'])]
    public function testValueAndLabel(PaymentMethodEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '在线支付',
        ];
        $this->assertSame($expected, PaymentMethodEnum::ONLINE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(PaymentMethodEnum::ONLINE, PaymentMethodEnum::from('1'));
        $this->assertSame(PaymentMethodEnum::COD, PaymentMethodEnum::from('2'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(PaymentMethodEnum::ONLINE, PaymentMethodEnum::tryFrom('1'));
        $this->assertSame(PaymentMethodEnum::COD, PaymentMethodEnum::tryFrom('2'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, PaymentMethodEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), PaymentMethodEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
