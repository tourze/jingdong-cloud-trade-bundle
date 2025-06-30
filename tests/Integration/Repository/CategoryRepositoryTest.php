<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;

class CategoryRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(CategoryRepository::class));
    }
}
