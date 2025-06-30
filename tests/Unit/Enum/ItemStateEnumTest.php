<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\ItemStateEnum;
use PHPUnit\Framework\TestCase;

class ItemStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(ItemStateEnum::class));
        
        $cases = ItemStateEnum::cases();
        $this->assertCount(5, $cases);
    }
}
