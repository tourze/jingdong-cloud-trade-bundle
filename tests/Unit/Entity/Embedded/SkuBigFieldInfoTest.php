<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBigFieldInfo;
use PHPUnit\Framework\TestCase;

class SkuBigFieldInfoTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(SkuBigFieldInfo::class));
        
        $entity = new SkuBigFieldInfo();
        $this->assertInstanceOf(SkuBigFieldInfo::class, $entity);
    }
}