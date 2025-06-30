<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Payment::class));
        
        $entity = new Payment();
        $this->assertInstanceOf(Payment::class, $entity);
    }
}