<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\AreaLevel;
use PHPUnit\Framework\TestCase;

class AreaLevelTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(AreaLevel::class));
        
        $cases = AreaLevel::cases();
        $this->assertCount(4, $cases);
    }
}
