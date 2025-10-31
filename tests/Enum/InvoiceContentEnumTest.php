<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceContentEnum::class)]
final class InvoiceContentEnumTest extends AbstractEnumTestCase
{
    #[TestWith([InvoiceContentEnum::GOODS, '1', '商品明细'])]
    #[TestWith([InvoiceContentEnum::CATEGORY, '2', '商品类别'])]
    #[TestWith([InvoiceContentEnum::CUSTOM, '3', '自定义'])]
    public function testValueAndLabel(InvoiceContentEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '商品明细',
        ];
        $this->assertSame($expected, InvoiceContentEnum::GOODS->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(InvoiceContentEnum::GOODS, InvoiceContentEnum::from('1'));
        $this->assertSame(InvoiceContentEnum::CATEGORY, InvoiceContentEnum::from('2'));
        $this->assertSame(InvoiceContentEnum::CUSTOM, InvoiceContentEnum::from('3'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(InvoiceContentEnum::GOODS, InvoiceContentEnum::tryFrom('1'));
        $this->assertSame(InvoiceContentEnum::CATEGORY, InvoiceContentEnum::tryFrom('2'));
        $this->assertSame(InvoiceContentEnum::CUSTOM, InvoiceContentEnum::tryFrom('3'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, InvoiceContentEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), InvoiceContentEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
