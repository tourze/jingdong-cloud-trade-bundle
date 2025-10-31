<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(PaymentStateEnum::class)]
final class PaymentStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([PaymentStateEnum::PENDING, '1', '待支付'])]
    #[TestWith([PaymentStateEnum::PAID, '2', '已支付'])]
    #[TestWith([PaymentStateEnum::REFUNDING, '3', '退款中'])]
    #[TestWith([PaymentStateEnum::REFUNDED, '4', '已退款'])]
    #[TestWith([PaymentStateEnum::FAILED, '5', '支付失败'])]
    public function testValueAndLabel(PaymentStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '待支付',
        ];
        $this->assertSame($expected, PaymentStateEnum::PENDING->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(PaymentStateEnum::PENDING, PaymentStateEnum::from('1'));
        $this->assertSame(PaymentStateEnum::PAID, PaymentStateEnum::from('2'));
        $this->assertSame(PaymentStateEnum::REFUNDING, PaymentStateEnum::from('3'));
        $this->assertSame(PaymentStateEnum::REFUNDED, PaymentStateEnum::from('4'));
        $this->assertSame(PaymentStateEnum::FAILED, PaymentStateEnum::from('5'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(PaymentStateEnum::PENDING, PaymentStateEnum::tryFrom('1'));
        $this->assertSame(PaymentStateEnum::PAID, PaymentStateEnum::tryFrom('2'));
        $this->assertSame(PaymentStateEnum::REFUNDING, PaymentStateEnum::tryFrom('3'));
        $this->assertSame(PaymentStateEnum::REFUNDED, PaymentStateEnum::tryFrom('4'));
        $this->assertSame(PaymentStateEnum::FAILED, PaymentStateEnum::tryFrom('5'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, PaymentStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), PaymentStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
