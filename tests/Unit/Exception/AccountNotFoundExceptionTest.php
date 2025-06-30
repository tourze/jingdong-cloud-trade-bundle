<?php

namespace JingdongCloudTradeBundle\Tests\Unit\Exception;

use JingdongCloudTradeBundle\Exception\AccountNotFoundException;
use PHPUnit\Framework\TestCase;

class AccountNotFoundExceptionTest extends TestCase
{
    public function testExceptionClass(): void
    {
        $this->assertTrue(class_exists(AccountNotFoundException::class));
        
        $exception = new AccountNotFoundException('Test message');
        $this->assertInstanceOf(AccountNotFoundException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}