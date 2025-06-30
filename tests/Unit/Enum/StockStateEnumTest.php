<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\StockStateEnum;
use PHPUnit\Framework\TestCase;

class StockStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(StockStateEnum::class));
        
        $cases = StockStateEnum::cases();
        $this->assertCount(2, $cases);
    }
}
