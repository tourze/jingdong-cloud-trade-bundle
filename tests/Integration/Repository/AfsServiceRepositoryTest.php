<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\AfsServiceRepository;
use PHPUnit\Framework\TestCase;

class AfsServiceRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(AfsServiceRepository::class));
    }
}
