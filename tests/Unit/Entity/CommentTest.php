<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Entity;

use JingdongCloudTradeBundle\Entity\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testEntityClass(): void
    {
        $this->assertTrue(class_exists(Comment::class));
        
        $entity = new Comment();
        $this->assertInstanceOf(Comment::class, $entity);
    }
}