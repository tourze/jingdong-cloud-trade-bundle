<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\FlashSaleEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(FlashSaleEnum::class)]
final class FlashSaleEnumTest extends AbstractEnumTestCase
{
    #[TestWith([FlashSaleEnum::NOT_FLASH_SALE, 0, '非秒杀商品'])]
    #[TestWith([FlashSaleEnum::FLASH_SALE, 1, '秒杀商品'])]
    public function testValueAndLabel(FlashSaleEnum $enum, int $value, string $label): void
    {
        $this->assertSame($value, $enum->value);
        $this->assertSame($label, $enum->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 0,
            'label' => '非秒杀商品',
        ];
        $this->assertSame($expected, FlashSaleEnum::NOT_FLASH_SALE->toArray());
    }

    public function testFromWithValidValue(): void
    {
        $this->assertSame(FlashSaleEnum::NOT_FLASH_SALE, FlashSaleEnum::from(0));
        $this->assertSame(FlashSaleEnum::FLASH_SALE, FlashSaleEnum::from(1));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(FlashSaleEnum::NOT_FLASH_SALE, FlashSaleEnum::tryFrom(0));
        $this->assertSame(FlashSaleEnum::FLASH_SALE, FlashSaleEnum::tryFrom(1));
    }

    public function testUniqueValues(): void
    {
        $values = array_map(fn ($case) => $case->value, FlashSaleEnum::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testUniqueLabels(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), FlashSaleEnum::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}
