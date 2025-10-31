<?php

namespace JingdongCloudTradeBundle\Tests\Exception;

use JingdongCloudTradeBundle\Exception\ApiException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ApiException::class)]
final class ApiExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsRuntimeException(): void
    {
        $exception = new ApiException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'API request failed';
        $exception = new ApiException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'API request failed';
        $code = 500;
        $exception = new ApiException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithMessageCodeAndPrevious(): void
    {
        $message = 'API request failed';
        $code = 500;
        $previous = new \RuntimeException('Previous exception');
        $exception = new ApiException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
