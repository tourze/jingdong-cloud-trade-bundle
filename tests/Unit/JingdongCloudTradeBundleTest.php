<?php

namespace JingdongCloudTradeBundle\Tests\Unit;

use JingdongCloudTradeBundle\JingdongCloudTradeBundle;
use PHPUnit\Framework\TestCase;

class JingdongCloudTradeBundleTest extends TestCase
{
    public function testBundleClass(): void
    {
        $this->assertTrue(class_exists(JingdongCloudTradeBundle::class));
        
        $bundle = new JingdongCloudTradeBundle();
        $this->assertInstanceOf(JingdongCloudTradeBundle::class, $bundle);
    }
}