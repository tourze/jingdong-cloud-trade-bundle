<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\DeliveryAddressRepository;
use PHPUnit\Framework\TestCase;

class DeliveryAddressRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(DeliveryAddressRepository::class));
    }
}
