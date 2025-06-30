<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Logistics;
use PHPUnit\Framework\TestCase;

class LogisticsTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Logistics::class));
        
        $entity = new Logistics();
        $this->assertInstanceOf(Logistics::class, $entity);
    }
}