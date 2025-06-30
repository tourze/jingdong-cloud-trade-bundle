<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\PayTypeEnum;
use PHPUnit\Framework\TestCase;

class PayTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(PayTypeEnum::class));
        
        $cases = PayTypeEnum::cases();
        $this->assertCount(2, $cases);
    }
}
