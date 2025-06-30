<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Entity;

use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use PHPUnit\Framework\TestCase;

class DeliveryAddressTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(DeliveryAddress::class));
        
        $entity = new DeliveryAddress();
        $this->assertInstanceOf(DeliveryAddress::class, $entity);
    }
}
