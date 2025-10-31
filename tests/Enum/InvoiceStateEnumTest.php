<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceStateEnum::class)]
final class InvoiceStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([InvoiceStateEnum::NOT_APPLIED, '0', '未申请'])]
    #[TestWith([InvoiceStateEnum::PENDING, '1', '申请中'])]
    #[TestWith([InvoiceStateEnum::ISSUED, '2', '已开票'])]
    #[TestWith([InvoiceStateEnum::FAILED, '3', '开票失败'])]
    #[TestWith([InvoiceStateEnum::CANCELLED, '4', '已取消'])]
    public function testValueAndLabel(InvoiceStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '0',
            'label' => '未申请',
        ];
        $this->assertSame($expected, InvoiceStateEnum::NOT_APPLIED->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(InvoiceStateEnum::NOT_APPLIED, InvoiceStateEnum::from('0'));
        $this->assertSame(InvoiceStateEnum::PENDING, InvoiceStateEnum::from('1'));
        $this->assertSame(InvoiceStateEnum::ISSUED, InvoiceStateEnum::from('2'));
        $this->assertSame(InvoiceStateEnum::FAILED, InvoiceStateEnum::from('3'));
        $this->assertSame(InvoiceStateEnum::CANCELLED, InvoiceStateEnum::from('4'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(InvoiceStateEnum::NOT_APPLIED, InvoiceStateEnum::tryFrom('0'));
        $this->assertSame(InvoiceStateEnum::PENDING, InvoiceStateEnum::tryFrom('1'));
        $this->assertSame(InvoiceStateEnum::ISSUED, InvoiceStateEnum::tryFrom('2'));
        $this->assertSame(InvoiceStateEnum::FAILED, InvoiceStateEnum::tryFrom('3'));
        $this->assertSame(InvoiceStateEnum::CANCELLED, InvoiceStateEnum::tryFrom('4'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, InvoiceStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), InvoiceStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
