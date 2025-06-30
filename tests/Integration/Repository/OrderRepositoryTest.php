<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\OrderRepository;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(OrderRepository::class));
    }
}
