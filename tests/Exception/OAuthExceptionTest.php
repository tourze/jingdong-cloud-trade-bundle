<?php

namespace JingdongCloudTradeBundle\Tests\Exception;

use JingdongCloudTradeBundle\Exception\OAuthException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(OAuthException::class)]
final class OAuthExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsRuntimeException(): void
    {
        $exception = new OAuthException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(OAuthException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'OAuth authentication failed';
        $exception = new OAuthException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'OAuth authentication failed';
        $code = 401;
        $exception = new OAuthException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithMessageCodeAndPrevious(): void
    {
        $message = 'OAuth authentication failed';
        $code = 401;
        $previous = new \RuntimeException('Previous exception');
        $exception = new OAuthException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
