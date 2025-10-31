<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\ReturnPolicyEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ReturnPolicyEnum::class)]
final class ReturnPolicyEnumTest extends AbstractEnumTestCase
{
    #[TestWith([ReturnPolicyEnum::NOT_SUPPORTED, 0, '不支持'])]
    #[TestWith([ReturnPolicyEnum::SUPPORTED, 1, '支持'])]
    public function testValueAndLabel(ReturnPolicyEnum $enum, int $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 0,
            'label' => '不支持',
        ];
        $this->assertSame($expected, ReturnPolicyEnum::NOT_SUPPORTED->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(ReturnPolicyEnum::NOT_SUPPORTED, ReturnPolicyEnum::from(0));
        $this->assertSame(ReturnPolicyEnum::SUPPORTED, ReturnPolicyEnum::from(1));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(ReturnPolicyEnum::NOT_SUPPORTED, ReturnPolicyEnum::tryFrom(0));
        $this->assertSame(ReturnPolicyEnum::SUPPORTED, ReturnPolicyEnum::tryFrom(1));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, ReturnPolicyEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), ReturnPolicyEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
