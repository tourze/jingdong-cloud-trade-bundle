<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use PHPUnit\Framework\TestCase;

class InvoiceStateEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(InvoiceStateEnum::class));
        
        $cases = InvoiceStateEnum::cases();
        $this->assertCount(5, $cases);
    }
}
