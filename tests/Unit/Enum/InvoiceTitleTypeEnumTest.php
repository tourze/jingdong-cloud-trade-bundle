<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceTitleTypeEnum;
use PHPUnit\Framework\TestCase;

class InvoiceTitleTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(InvoiceTitleTypeEnum::class));
        
        $cases = InvoiceTitleTypeEnum::cases();
        $this->assertCount(2, $cases);
    }
}
