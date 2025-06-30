<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Sku;
use PHPUnit\Framework\TestCase;

class SkuTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Sku::class));
        
        $entity = new Sku();
        $this->assertInstanceOf(Sku::class, $entity);
    }
}