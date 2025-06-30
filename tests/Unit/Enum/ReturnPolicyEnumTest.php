<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\ReturnPolicyEnum;
use PHPUnit\Framework\TestCase;

class ReturnPolicyEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(ReturnPolicyEnum::class));
        
        $cases = ReturnPolicyEnum::cases();
        $this->assertCount(2, $cases);
    }
}
