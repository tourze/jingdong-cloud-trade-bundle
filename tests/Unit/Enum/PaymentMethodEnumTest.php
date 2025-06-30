<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use PHPUnit\Framework\TestCase;

class PaymentMethodEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(PaymentMethodEnum::class));
        
        $cases = PaymentMethodEnum::cases();
        $this->assertCount(2, $cases);
    }
}
