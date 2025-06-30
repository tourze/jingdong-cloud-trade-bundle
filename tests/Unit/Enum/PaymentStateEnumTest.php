<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use PHPUnit\Framework\TestCase;

class PaymentStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(PaymentStateEnum::class));
        
        $cases = PaymentStateEnum::cases();
        $this->assertCount(5, $cases);
    }
}
