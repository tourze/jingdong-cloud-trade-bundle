<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceTitleTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceTitleTypeEnum::class)]
final class InvoiceTitleTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([InvoiceTitleTypeEnum::PERSONAL, '1', '个人'])]
    #[TestWith([InvoiceTitleTypeEnum::COMPANY, '2', '企业'])]
    public function testValueAndLabel(InvoiceTitleTypeEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '个人',
        ];
        $this->assertSame($expected, InvoiceTitleTypeEnum::PERSONAL->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(InvoiceTitleTypeEnum::PERSONAL, InvoiceTitleTypeEnum::from('1'));
        $this->assertSame(InvoiceTitleTypeEnum::COMPANY, InvoiceTitleTypeEnum::from('2'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(InvoiceTitleTypeEnum::PERSONAL, InvoiceTitleTypeEnum::tryFrom('1'));
        $this->assertSame(InvoiceTitleTypeEnum::COMPANY, InvoiceTitleTypeEnum::tryFrom('2'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, InvoiceTitleTypeEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), InvoiceTitleTypeEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
