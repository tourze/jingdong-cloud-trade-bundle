<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBookInfo;
use PHPUnit\Framework\TestCase;

class SkuBookInfoTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(SkuBookInfo::class));
        
        $entity = new SkuBookInfo();
        $this->assertInstanceOf(SkuBookInfo::class, $entity);
    }
}