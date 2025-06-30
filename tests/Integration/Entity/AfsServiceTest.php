<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Entity;

use JingdongCloudTradeBundle\Entity\AfsService;
use PHPUnit\Framework\TestCase;

class AfsServiceTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(AfsService::class));
        
        $entity = new AfsService();
        $this->assertInstanceOf(AfsService::class, $entity);
    }
}
