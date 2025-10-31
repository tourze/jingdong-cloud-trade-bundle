<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\ScoreEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ScoreEnum::class)]
final class ScoreEnumTest extends AbstractEnumTestCase
{
    #[TestWith([ScoreEnum::ONE, '1', '非常不满意'])]
    #[TestWith([ScoreEnum::TWO, '2', '不满意'])]
    #[TestWith([ScoreEnum::THREE, '3', '一般'])]
    #[TestWith([ScoreEnum::FOUR, '4', '满意'])]
    #[TestWith([ScoreEnum::FIVE, '5', '非常满意'])]
    public function testValueAndLabel(ScoreEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => '1',
            'label' => '非常不满意',
        ];
        $this->assertSame($expected, ScoreEnum::ONE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(ScoreEnum::ONE, ScoreEnum::from('1'));
        $this->assertSame(ScoreEnum::TWO, ScoreEnum::from('2'));
        $this->assertSame(ScoreEnum::THREE, ScoreEnum::from('3'));
        $this->assertSame(ScoreEnum::FOUR, ScoreEnum::from('4'));
        $this->assertSame(ScoreEnum::FIVE, ScoreEnum::from('5'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(ScoreEnum::ONE, ScoreEnum::tryFrom('1'));
        $this->assertSame(ScoreEnum::TWO, ScoreEnum::tryFrom('2'));
        $this->assertSame(ScoreEnum::THREE, ScoreEnum::tryFrom('3'));
        $this->assertSame(ScoreEnum::FOUR, ScoreEnum::tryFrom('4'));
        $this->assertSame(ScoreEnum::FIVE, ScoreEnum::tryFrom('5'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, ScoreEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), ScoreEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
