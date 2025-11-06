<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\ApiException;
use JingdongCloudTradeBundle\Service\AuthService;
use JingdongCloudTradeBundle\Service\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(Client::class)]
#[RunTestsInSeparateProcesses]
final class ClientTest extends AbstractIntegrationTestCase
{
    private HttpClientInterface $httpClient;

    private Client $client;

    private Account $account;

    /** @var \ReflectionClass<Client> */
    private \ReflectionClass $reflectionClass;

    protected function onSetUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $authService = $this->createMock(AuthService::class);

        $authService->method('getAccessToken');

        $this->client = new Client($this->httpClient, $authService, $logger);

        $this->account = new Account();
        $this->account->setAppKey('test_app_key');
        $this->account->setAppSecret('test_app_secret');
        $this->account->setAccessToken('test_access_token');

        // 使用反射访问私有方法
        $this->reflectionClass = new \ReflectionClass(Client::class);
    }

    public function testGenerateSign(): void
    {
        $params = [
            'app_key' => 'test_app_key',
            'method' => 'jingdong.test.api',
            'v' => '2.0',
            'timestamp' => '2023-01-01 12:00:00',
            'format' => 'json',
            'sign_method' => 'md5',
            '360buy_param_json' => '{"key":"value"}',
        ];

        $method = $this->reflectionClass->getMethod('generateSign');
        $method->setAccessible(true);

        $sign = $method->invoke($this->client, $params, 'test_app_secret');
        $this->assertIsString($sign);
        $this->assertEquals(32, strlen($sign)); // MD5 签名长度是32位
        $this->assertEquals(strtoupper($sign), $sign); // 签名应该是大写
    }

    public function testExecuteSuccessResponse(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        /** @var InvocationMocker $responseMethod */
        $responseMethod = $mockResponse->method('toArray');
        $responseMethod->willReturn([
            'response' => [
                'result' => 'success',
                'data' => ['key' => 'value'],
            ],
        ]);

        /** @var InvocationMocker $httpMethod */
        $httpMethod = $this->httpClient->method('request');
        /** @var InvocationMocker $httpWithParams */
        $httpWithParams = $httpMethod->with(
            'POST',
            'https://api.jd.com/routerjson',
            self::callback(function (array $options) use ($params): bool {
                self::assertArrayHasKey('body', $options);
                self::assertIsArray($options['body']);
                $body = $options['body'];

                // 验证关键参数是否正确
                self::assertArrayHasKey('method', $body);
                self::assertArrayHasKey('app_key', $body);
                self::assertArrayHasKey('access_token', $body);
                self::assertArrayHasKey('sign', $body);
                self::assertArrayHasKey('360buy_param_json', $body);
                self::assertIsString($body['360buy_param_json']);

                self::assertEquals('test_app_key', $body['app_key']);
                self::assertEquals('test_access_token', $body['access_token']);
                self::assertEquals(json_encode($params), $body['360buy_param_json']);

                return true;
            })
        );
        $httpWithParams->willReturn($mockResponse);

        $result = $this->client->execute($this->account, $method, $params);
        $this->assertArrayHasKey('response', $result);
    }

    public function testExecuteErrorResponse(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        /** @var InvocationMocker $responseMethod */
        $responseMethod = $mockResponse->method('toArray');
        $responseMethod->willReturn([
            'error_response' => [
                'code' => '400',
                'zh_desc' => '请求参数错误',
            ],
        ]);

        /** @var InvocationMocker $httpMethod */
        $httpMethod = $this->httpClient->method('request');
        $httpMethod->willReturn($mockResponse);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('请求参数错误');

        $this->client->execute($this->account, $method, $params);
    }

    public function testExecuteErrorResponseWithoutDescription(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        /** @var InvocationMocker $responseMethod */
        $responseMethod = $mockResponse->method('toArray');
        $responseMethod->willReturn([
            'error_response' => [
                'code' => '400',
                // 没有 zh_desc 字段
            ],
        ]);

        /** @var InvocationMocker $httpMethod */
        $httpMethod = $this->httpClient->method('request');
        $httpMethod->willReturn($mockResponse);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('未知错误');

        $this->client->execute($this->account, $method, $params);
    }

    public function testExecuteComplexParameters(): void
    {
        $method = 'jingdong.order.query';
        $params = [
            'orderId' => 123456789,
            'queryOptions' => [
                'includeItems' => true,
                'includePayment' => true,
            ],
            'dateRange' => [
                'startTime' => '2023-01-01',
                'endTime' => '2023-01-02',
            ],
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        /** @var InvocationMocker $responseMethod */
        $responseMethod = $mockResponse->method('toArray');
        $responseMethod->willReturn([
            'response' => [
                'result' => 'success',
            ],
        ]);

        /** @var InvocationMocker $httpMethod */
        $httpMethod = $this->httpClient->method('request');
        /** @var InvocationMocker $httpWithParams */
        $httpWithParams = $httpMethod->with(
            'POST',
            'https://api.jd.com/routerjson',
            self::callback(function (array $options) use ($params): bool {
                self::assertArrayHasKey('body', $options);
                self::assertIsArray($options['body']);
                $body = $options['body'];

                // 验证复杂参数的 JSON 序列化是否正确
                self::assertArrayHasKey('360buy_param_json', $body);
                self::assertIsString($body['360buy_param_json']);
                $jsonParams = json_decode($body['360buy_param_json'], true);
                self::assertIsArray($jsonParams);
                self::assertEquals($params['orderId'], $jsonParams['orderId']);
                self::assertEquals($params['queryOptions'], $jsonParams['queryOptions']);
                self::assertEquals($params['dateRange'], $jsonParams['dateRange']);

                return true;
            })
        );
        $httpWithParams->willReturn($mockResponse);

        $this->client->execute($this->account, $method, $params);
    }
}
