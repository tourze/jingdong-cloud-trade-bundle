<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\InvoiceRepository;
use PHPUnit\Framework\TestCase;

class InvoiceRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(InvoiceRepository::class));
    }
}
