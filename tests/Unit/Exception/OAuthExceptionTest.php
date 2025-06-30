<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Exception;

use JingdongCloudTradeBundle\Exception\OAuthException;
use PHPUnit\Framework\TestCase;

class OAuthExceptionTest extends TestCase
{
    public function testExceptionClass(): void
    {
        $this->assertTrue(class_exists(OAuthException::class));
        
        $exception = new OAuthException('Test message');
        $this->assertInstanceOf(OAuthException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}