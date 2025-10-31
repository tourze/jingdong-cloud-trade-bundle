<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\OrderStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OrderStateEnum::class)]
final class OrderStateEnumTest extends AbstractEnumTestCase
{
    #[TestWith([OrderStateEnum::CREATED, 'CREATED', '已创建'])]
    #[TestWith([OrderStateEnum::PAID, 'PAID', '已支付'])]
    #[TestWith([OrderStateEnum::SHIPPED, 'SHIPPED', '已发货'])]
    #[TestWith([OrderStateEnum::COMPLETED, 'COMPLETED', '已完成'])]
    #[TestWith([OrderStateEnum::CANCELLED, 'CANCELLED', '已取消'])]
    #[TestWith([OrderStateEnum::CLOSED, 'CLOSED', '已关闭'])]
    #[TestWith([OrderStateEnum::AFTER_SALE, 'AFTER_SALE', '售后中'])]
    public function testValueAndLabel(OrderStateEnum $enum, string $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'CREATED',
            'label' => '已创建',
        ];
        $this->assertSame($expected, OrderStateEnum::CREATED->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(OrderStateEnum::CREATED, OrderStateEnum::from('CREATED'));
        $this->assertSame(OrderStateEnum::PAID, OrderStateEnum::from('PAID'));
        $this->assertSame(OrderStateEnum::SHIPPED, OrderStateEnum::from('SHIPPED'));
        $this->assertSame(OrderStateEnum::COMPLETED, OrderStateEnum::from('COMPLETED'));
        $this->assertSame(OrderStateEnum::CANCELLED, OrderStateEnum::from('CANCELLED'));
        $this->assertSame(OrderStateEnum::CLOSED, OrderStateEnum::from('CLOSED'));
        $this->assertSame(OrderStateEnum::AFTER_SALE, OrderStateEnum::from('AFTER_SALE'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(OrderStateEnum::CREATED, OrderStateEnum::tryFrom('CREATED'));
        $this->assertSame(OrderStateEnum::PAID, OrderStateEnum::tryFrom('PAID'));
        $this->assertSame(OrderStateEnum::SHIPPED, OrderStateEnum::tryFrom('SHIPPED'));
        $this->assertSame(OrderStateEnum::COMPLETED, OrderStateEnum::tryFrom('COMPLETED'));
        $this->assertSame(OrderStateEnum::CANCELLED, OrderStateEnum::tryFrom('CANCELLED'));
        $this->assertSame(OrderStateEnum::CLOSED, OrderStateEnum::tryFrom('CLOSED'));
        $this->assertSame(OrderStateEnum::AFTER_SALE, OrderStateEnum::tryFrom('AFTER_SALE'));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, OrderStateEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), OrderStateEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
