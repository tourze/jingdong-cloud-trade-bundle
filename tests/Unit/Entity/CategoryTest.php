<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Category::class));
        
        $entity = new Category();
        $this->assertInstanceOf(Category::class, $entity);
    }
}