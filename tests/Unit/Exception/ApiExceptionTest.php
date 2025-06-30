<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Exception;

use JingdongCloudTradeBundle\Exception\ApiException;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    public function testExceptionClass(): void
    {
        $this->assertTrue(class_exists(ApiException::class));
        
        $exception = new ApiException('Test message');
        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}