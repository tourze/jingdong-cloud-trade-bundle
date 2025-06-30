<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\OrderStateEnum;
use PHPUnit\Framework\TestCase;

class OrderStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(OrderStateEnum::class));
        
        $cases = OrderStateEnum::cases();
        $this->assertCount(7, $cases);
    }
}
