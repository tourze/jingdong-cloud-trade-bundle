<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\DeliveryTypeEnum;
use PHPUnit\Framework\TestCase;

class DeliveryTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(DeliveryTypeEnum::class));
        
        $cases = DeliveryTypeEnum::cases();
        $this->assertCount(2, $cases);
    }
}
