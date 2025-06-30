<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\LogisticsRepository;
use PHPUnit\Framework\TestCase;

class LogisticsRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(LogisticsRepository::class));
    }
}
