<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\AfsTypeEnum;
use PHPUnit\Framework\TestCase;

class AfsTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(AfsTypeEnum::class));
        
        $cases = AfsTypeEnum::cases();
        $this->assertCount(count($cases), $cases);
    }
}
