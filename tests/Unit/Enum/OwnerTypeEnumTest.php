<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\OwnerTypeEnum;
use PHPUnit\Framework\TestCase;

class OwnerTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(OwnerTypeEnum::class));
        
        $cases = OwnerTypeEnum::cases();
        $this->assertCount(2, $cases);
    }
}
