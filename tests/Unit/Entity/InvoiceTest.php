<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Invoice;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Invoice::class));
        
        $entity = new Invoice();
        $this->assertInstanceOf(Invoice::class, $entity);
    }
}