<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\AfsServiceStateEnum;
use PHPUnit\Framework\TestCase;

class AfsServiceStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(AfsServiceStateEnum::class));
        
        // 验证枚举有预期的case
        $this->assertNotNull(AfsServiceStateEnum::APPLYING);
        $this->assertInstanceOf(AfsServiceStateEnum::class, AfsServiceStateEnum::APPLYING);
    }
}
