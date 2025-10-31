<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(PaymentChannelEnum::class)]
final class PaymentChannelEnumTest extends AbstractEnumTestCase
{
    #[TestWith([PaymentChannelEnum::WECHAT, 'WECHAT', '微信支付'])]
    #[TestWith([PaymentChannelEnum::ALIPAY, 'ALIPAY', '支付宝'])]
    #[TestWith([PaymentChannelEnum::UNIONPAY, 'UNIONPAY', '银联支付'])]
    #[TestWith([PaymentChannelEnum::OTHER, 'OTHER', '其他支付'])]
    public function testValueAndLabel(PaymentChannelEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'WECHAT',
            'label' => '微信支付',
        ];
        $this->assertSame($expected, PaymentChannelEnum::WECHAT->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(PaymentChannelEnum::WECHAT, PaymentChannelEnum::from('WECHAT'));
        $this->assertSame(PaymentChannelEnum::ALIPAY, PaymentChannelEnum::from('ALIPAY'));
        $this->assertSame(PaymentChannelEnum::UNIONPAY, PaymentChannelEnum::from('UNIONPAY'));
        $this->assertSame(PaymentChannelEnum::OTHER, PaymentChannelEnum::from('OTHER'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(PaymentChannelEnum::WECHAT, PaymentChannelEnum::tryFrom('WECHAT'));
        $this->assertSame(PaymentChannelEnum::ALIPAY, PaymentChannelEnum::tryFrom('ALIPAY'));
        $this->assertSame(PaymentChannelEnum::UNIONPAY, PaymentChannelEnum::tryFrom('UNIONPAY'));
        $this->assertSame(PaymentChannelEnum::OTHER, PaymentChannelEnum::tryFrom('OTHER'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, PaymentChannelEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), PaymentChannelEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
