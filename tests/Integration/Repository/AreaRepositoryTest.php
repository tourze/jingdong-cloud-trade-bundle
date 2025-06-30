<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\AreaRepository;
use PHPUnit\Framework\TestCase;

class AreaRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(AreaRepository::class));
    }
}
