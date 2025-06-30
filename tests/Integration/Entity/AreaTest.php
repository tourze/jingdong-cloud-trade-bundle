<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Entity;

use JingdongCloudTradeBundle\Entity\Area;
use PHPUnit\Framework\TestCase;

class AreaTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Area::class));
        
        $entity = new Area();
        $this->assertInstanceOf(Area::class, $entity);
    }
}
