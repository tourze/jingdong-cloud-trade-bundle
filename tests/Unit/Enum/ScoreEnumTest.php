<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\ScoreEnum;
use PHPUnit\Framework\TestCase;

class ScoreEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(ScoreEnum::class));
        
        $cases = ScoreEnum::cases();
        $this->assertCount(5, $cases);
    }
}
