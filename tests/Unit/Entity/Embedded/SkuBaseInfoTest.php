<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use PHPUnit\Framework\TestCase;

class SkuBaseInfoTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(SkuBaseInfo::class));
        
        $entity = new SkuBaseInfo();
        $this->assertInstanceOf(SkuBaseInfo::class, $entity);
    }
}