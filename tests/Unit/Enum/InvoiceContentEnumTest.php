<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use PHPUnit\Framework\TestCase;

class InvoiceContentEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(InvoiceContentEnum::class));
        
        $cases = InvoiceContentEnum::cases();
        $this->assertCount(3, $cases);
    }
}
