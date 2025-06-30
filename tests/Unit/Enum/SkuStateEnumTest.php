<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use PHPUnit\Framework\TestCase;

class SkuStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(SkuStateEnum::class));
        
        $cases = SkuStateEnum::cases();
        $this->assertCount(2, $cases);
    }
}
