<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuSpecification;
use PHPUnit\Framework\TestCase;

class SkuSpecificationTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(SkuSpecification::class));
        
        $entity = new SkuSpecification();
        $this->assertInstanceOf(SkuSpecification::class, $entity);
    }
}