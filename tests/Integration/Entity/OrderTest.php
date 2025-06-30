<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Entity;

use JingdongCloudTradeBundle\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Order::class));
        
        $entity = new Order();
        $this->assertInstanceOf(Order::class, $entity);
    }
}
