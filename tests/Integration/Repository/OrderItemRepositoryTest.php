<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\OrderItemRepository;
use PHPUnit\Framework\TestCase;

class OrderItemRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(OrderItemRepository::class));
    }
}
