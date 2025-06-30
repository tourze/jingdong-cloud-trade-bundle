<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\PaymentRepository;
use PHPUnit\Framework\TestCase;

class PaymentRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(PaymentRepository::class));
    }
}
