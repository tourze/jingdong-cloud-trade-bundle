<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceTypeEnum::class)]
final class InvoiceTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([InvoiceTypeEnum::NORMAL, '1', '普通发票'])]
    #[TestWith([InvoiceTypeEnum::VAT, '2', '增值税发票'])]
    #[TestWith([InvoiceTypeEnum::ELECTRONIC, '3', '电子发票'])]
    public function testValueAndLabel(InvoiceTypeEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '普通发票',
        ];
        $this->assertSame($expected, InvoiceTypeEnum::NORMAL->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(InvoiceTypeEnum::NORMAL, InvoiceTypeEnum::from('1'));
        $this->assertSame(InvoiceTypeEnum::VAT, InvoiceTypeEnum::from('2'));
        $this->assertSame(InvoiceTypeEnum::ELECTRONIC, InvoiceTypeEnum::from('3'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(InvoiceTypeEnum::NORMAL, InvoiceTypeEnum::tryFrom('1'));
        $this->assertSame(InvoiceTypeEnum::VAT, InvoiceTypeEnum::tryFrom('2'));
        $this->assertSame(InvoiceTypeEnum::ELECTRONIC, InvoiceTypeEnum::tryFrom('3'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, InvoiceTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), InvoiceTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
