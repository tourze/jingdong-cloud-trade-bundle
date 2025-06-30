<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Enum;

use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;
use PHPUnit\Framework\TestCase;

class InvoiceTypeEnumTest extends TestCase
{
    public function testEnumClass(): void
    {
        $this->assertTrue(enum_exists(InvoiceTypeEnum::class));
        
        $cases = InvoiceTypeEnum::cases();
        $this->assertCount(3, $cases);
    }
}
