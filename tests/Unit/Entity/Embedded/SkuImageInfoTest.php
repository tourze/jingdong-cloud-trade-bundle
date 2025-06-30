<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuImageInfo;
use PHPUnit\Framework\TestCase;

class SkuImageInfoTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(SkuImageInfo::class));
        
        $entity = new SkuImageInfo();
        $this->assertInstanceOf(SkuImageInfo::class, $entity);
    }
}