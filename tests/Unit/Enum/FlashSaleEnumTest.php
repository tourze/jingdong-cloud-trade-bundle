<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\FlashSaleEnum;
use PHPUnit\Framework\TestCase;

class FlashSaleEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(FlashSaleEnum::class));
        
        $cases = FlashSaleEnum::cases();
        $this->assertCount(2, $cases);
    }
}
