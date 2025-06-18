<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Service\AuthService;
use JingdongCloudTradeBundle\Service\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ClientTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private AuthService $authService;
    private Client $client;
    private Account $account;
    private \ReflectionClass $reflectionClass;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->authService = $this->createMock(AuthService::class);
        
        $this->client = new Client($this->httpClient, $this->authService);
        
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
        $this->assertEquals(32, strlen($sign)); // MD5 签名长度是32位
        $this->assertEquals(strtoupper($sign), $sign); // 签名应该是大写
    }
    
    public function testExecute_successResponse(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];
        
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'response' => [
                'result' => 'success',
                'data' => ['key' => 'value'],
            ]
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.jd.com/routerjson',
                $this->callback(function($options) use ($params) {
                    $body = $options['body'];
                    
                    // 验证关键参数是否正确
                    $this->assertArrayHasKey('method', $body);
                    $this->assertArrayHasKey('app_key', $body);
                    $this->assertArrayHasKey('access_token', $body);
                    $this->assertArrayHasKey('sign', $body);
                    $this->assertArrayHasKey('360buy_param_json', $body);
                    
                    $this->assertEquals('test_app_key', $body['app_key']);
                    $this->assertEquals('test_access_token', $body['access_token']);
                    $this->assertEquals(json_encode($params), $body['360buy_param_json']);
                    
                    return true;
                })
            )
            ->willReturn($mockResponse);
        
        $result = $this->client->execute($this->account, $method, $params);
        $this->assertArrayHasKey('response', $result);
    }
    
    public function testExecute_errorResponse(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];
        
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'error_response' => [
                'code' => '400',
                'zh_desc' => '请求参数错误',
            ]
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);
            
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('请求参数错误');
        
        $this->client->execute($this->account, $method, $params);
    }
    
    public function testExecute_errorResponseWithoutDescription(): void
    {
        $method = 'jingdong.test.api';
        $params = ['key' => 'value'];
        
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'error_response' => [
                'code' => '400',
                // 没有 zh_desc 字段
            ]
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);
            
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('未知错误');
        
        $this->client->execute($this->account, $method, $params);
    }
    
    public function testExecute_complexParameters(): void
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
        $mockResponse->method('toArray')->willReturn([
            'response' => [
                'result' => 'success',
            ]
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.jd.com/routerjson',
                $this->callback(function($options) use ($params) {
                    $body = $options['body'];
                    
                    // 验证复杂参数的 JSON 序列化是否正确
                    $jsonParams = json_decode($body['360buy_param_json'], true);
                    $this->assertEquals($params['orderId'], $jsonParams['orderId']);
                    $this->assertEquals($params['queryOptions'], $jsonParams['queryOptions']);
                    $this->assertEquals($params['dateRange'], $jsonParams['dateRange']);
                    
                    return true;
                })
            )
            ->willReturn($mockResponse);
        
        $this->client->execute($this->account, $method, $params);
    }
} 