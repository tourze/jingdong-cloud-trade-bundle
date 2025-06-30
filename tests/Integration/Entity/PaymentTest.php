<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Entity;

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
