<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\AfsServiceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AfsServiceStateEnum::class)]
final class AfsServiceStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([AfsServiceStateEnum::APPLYING, 'APPLYING', '申请中'])]
    #[TestWith([AfsServiceStateEnum::APPROVED, 'APPROVED', '审核通过'])]
    #[TestWith([AfsServiceStateEnum::REJECTED, 'REJECTED', '审核拒绝'])]
    #[TestWith([AfsServiceStateEnum::PROCESSING, 'PROCESSING', '处理中'])]
    #[TestWith([AfsServiceStateEnum::COMPLETED, 'COMPLETED', '已完成'])]
    #[TestWith([AfsServiceStateEnum::CANCELLED, 'CANCELLED', '已取消'])]
    public function testValueAndLabel(AfsServiceStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'APPLYING',
            'label' => '申请中',
        ];
        $this->assertSame($expected, AfsServiceStateEnum::APPLYING->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(AfsServiceStateEnum::APPLYING, AfsServiceStateEnum::from('APPLYING'));
        $this->assertSame(AfsServiceStateEnum::APPROVED, AfsServiceStateEnum::from('APPROVED'));
        $this->assertSame(AfsServiceStateEnum::REJECTED, AfsServiceStateEnum::from('REJECTED'));
        $this->assertSame(AfsServiceStateEnum::PROCESSING, AfsServiceStateEnum::from('PROCESSING'));
        $this->assertSame(AfsServiceStateEnum::COMPLETED, AfsServiceStateEnum::from('COMPLETED'));
        $this->assertSame(AfsServiceStateEnum::CANCELLED, AfsServiceStateEnum::from('CANCELLED'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(AfsServiceStateEnum::APPLYING, AfsServiceStateEnum::tryFrom('APPLYING'));
        $this->assertSame(AfsServiceStateEnum::APPROVED, AfsServiceStateEnum::tryFrom('APPROVED'));
        $this->assertSame(AfsServiceStateEnum::REJECTED, AfsServiceStateEnum::tryFrom('REJECTED'));
        $this->assertSame(AfsServiceStateEnum::PROCESSING, AfsServiceStateEnum::tryFrom('PROCESSING'));
        $this->assertSame(AfsServiceStateEnum::COMPLETED, AfsServiceStateEnum::tryFrom('COMPLETED'));
        $this->assertSame(AfsServiceStateEnum::CANCELLED, AfsServiceStateEnum::tryFrom('CANCELLED'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, AfsServiceStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), AfsServiceStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
