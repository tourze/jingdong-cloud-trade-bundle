<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use PHPUnit\Framework\TestCase;

class PaymentChannelEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(PaymentChannelEnum::class));
        
        $cases = PaymentChannelEnum::cases();
        $this->assertCount(4, $cases);
    }
}
