<?php

namespace JingdongCloudTradeBundle\Tests\Exception;

use JingdongCloudTradeBundle\Exception\AccountNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(AccountNotFoundException::class)]
final class AccountNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsRuntimeException(): void
    {
        $exception = new AccountNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(AccountNotFoundException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Account not found';
        $exception = new AccountNotFoundException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Account not found';
        $code = 404;
        $exception = new AccountNotFoundException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithMessageCodeAndPrevious(): void
    {
        $message = 'Account not found';
        $code = 404;
        $previous = new \RuntimeException('Previous exception');
        $exception = new AccountNotFoundException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
