<?php

namespace JingdongCloudTradeBundle\Tests\Controller;

use JingdongCloudTradeBundle\Controller\OAuthCallbackController;
use JingdongCloudTradeBundle\Exception\OAuthException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(OAuthCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class OAuthCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetCallbackWithoutSessionReturnsError(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('No account ID in session');
        $client->request('GET', '/oauth/callback', ['code' => 'test_code', 'state' => 'test_state']);
    }

    public function testPostCallbackNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/oauth/callback');
    }

    public function testPutCallbackNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/oauth/callback');
    }

    public function testDeleteCallbackNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/oauth/callback');
    }

    public function testPatchCallbackNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/oauth/callback');
    }

    public function testHeadCallbackWithoutSessionReturnsError(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('No account ID in session');
        $client->request('HEAD', '/oauth/callback');
    }

    public function testOptionsCallbackNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/oauth/callback');
    }

    public function testUnauthorizedAccessWithoutSession(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('No account ID in session');
        $client->request('GET', '/oauth/callback');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            self::markTestSkipped('No disallowed methods found');
        }

        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);

        // PHPStan 规则要求 HTTP method 必须是字符串字面量
        match ($method) {
            'POST' => $client->request('POST', '/oauth/callback'),
            'PUT' => $client->request('PUT', '/oauth/callback'),
            'DELETE' => $client->request('DELETE', '/oauth/callback'),
            'PATCH' => $client->request('PATCH', '/oauth/callback'),
            'OPTIONS' => $client->request('OPTIONS', '/oauth/callback'),
            'TRACE' => $client->request('TRACE', '/oauth/callback'),
            'PURGE' => $client->request('PURGE', '/oauth/callback'),
            default => self::markTestSkipped("Unsupported method: {$method}"),
        };
    }
}
