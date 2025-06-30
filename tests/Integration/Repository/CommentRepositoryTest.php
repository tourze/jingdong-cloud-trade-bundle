<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Repository;

use JingdongCloudTradeBundle\Repository\CommentRepository;
use PHPUnit\Framework\TestCase;

class CommentRepositoryTest extends TestCase
{
    public function testRepositoryClass(): void
    {
        $this->assertTrue(class_exists(CommentRepository::class));
    }
}
