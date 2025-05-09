<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Service\AuthService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthServiceTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private UrlGeneratorInterface $urlGenerator;
    private AuthService $authService;
    private Account $account;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->authService = new AuthService($this->httpClient, $this->urlGenerator);
        
        $this->account = new Account();
        $this->account->setAppKey('test_app_key');
        $this->account->setAppSecret('test_app_secret');
    }

    public function testGetAuthorizationUrl_withBasicParams(): void
    {
        $redirectUri = 'https://example.com/callback';
        
        $authUrl = $this->authService->getAuthorizationUrl($this->account, $redirectUri);
        
        $this->assertNotEmpty($this->account->getState());
        $this->assertStringContainsString('response_type=code', $authUrl);
        $this->assertStringContainsString('client_id=test_app_key', $authUrl);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fexample.com%2Fcallback', $authUrl);
        $this->assertStringContainsString('state=' . $this->account->getState(), $authUrl);
    }
    
    public function testGetAuthorizationUrl_withScope(): void
    {
        $redirectUri = 'https://example.com/callback';
        $scope = ['read', 'write'];
        
        $authUrl = $this->authService->getAuthorizationUrl($this->account, $redirectUri, $scope);
        
        $this->assertStringContainsString('scope=read+write', $authUrl);
    }
    
    public function testHandleCallback_validState(): void
    {
        $state = 'valid_state';
        $code = 'test_auth_code';
        
        $this->account->setState($state);
        
        $request = Request::create('/?state=' . $state . '&code=' . $code);
        
        // Mock response for token request
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
            'refresh_token_expires_in' => 2592000,
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);
            
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/oauth/callback');
        
        $this->authService->handleCallback($this->account, $request);
        
        $this->assertSame($code, $this->account->getCode());
        $this->assertNotNull($this->account->getCodeExpiresAt());
        $this->assertSame('new_access_token', $this->account->getAccessToken());
        $this->assertSame('new_refresh_token', $this->account->getRefreshToken());
        $this->assertNotNull($this->account->getAccessTokenExpiresAt());
        $this->assertNotNull($this->account->getRefreshTokenExpiresAt());
    }
    
    public function testHandleCallback_invalidState(): void
    {
        $this->account->setState('valid_state');
        
        $request = Request::create('/?state=invalid_state&code=test_code');
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid state');
        
        $this->authService->handleCallback($this->account, $request);
    }
    
    public function testHandleCallback_noCode(): void
    {
        $state = 'valid_state';
        $this->account->setState($state);
        
        $request = Request::create('/?state=' . $state);
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No code received');
        
        $this->authService->handleCallback($this->account, $request);
    }
    
    public function testGetAccessToken_tokenStillValid(): void
    {
        $this->account->setAccessToken('valid_token');
        $this->account->setAccessTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
        
        // HttpClient should not be called since token is still valid
        $this->httpClient->expects($this->never())->method('request');
        
        $this->authService->getAccessToken($this->account);
    }
    
    public function testGetAccessToken_refreshToken(): void
    {
        $this->account->setAccessToken('expired_token');
        $this->account->setAccessTokenExpiresAt(new \DateTimeImmutable('-1 hour'));
        $this->account->setRefreshToken('valid_refresh_token');
        $this->account->setRefreshTokenExpiresAt(new \DateTimeImmutable('+1 day'));
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
            'refresh_token_expires_in' => 2592000,
        ]);
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://oauth.jd.com/oauth/token',
                [
                    'body' => [
                        'grant_type' => 'refresh_token',
                        'client_id' => 'test_app_key',
                        'client_secret' => 'test_app_secret',
                        'refresh_token' => 'valid_refresh_token',
                    ],
                ]
            )
            ->willReturn($response);
            
        $this->authService->getAccessToken($this->account);
        
        $this->assertSame('new_access_token', $this->account->getAccessToken());
        $this->assertSame('new_refresh_token', $this->account->getRefreshToken());
    }
    
    public function testGetAccessToken_useCode(): void
    {
        $this->account->setAccessToken('expired_token');
        $this->account->setAccessTokenExpiresAt(new \DateTimeImmutable('-1 hour'));
        $this->account->setCode('valid_code');
        $this->account->setCodeExpiresAt(new \DateTimeImmutable('+5 minutes'));
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
            'refresh_token_expires_in' => 2592000,
        ]);
        
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/oauth/callback');
            
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://oauth.jd.com/oauth/token',
                [
                    'body' => [
                        'grant_type' => 'authorization_code',
                        'client_id' => 'test_app_key',
                        'client_secret' => 'test_app_secret',
                        'code' => 'valid_code',
                        'redirect_uri' => 'https://example.com/oauth/callback',
                    ],
                ]
            )
            ->willReturn($response);
            
        $this->authService->getAccessToken($this->account);
        
        $this->assertSame('new_access_token', $this->account->getAccessToken());
        $this->assertSame('new_refresh_token', $this->account->getRefreshToken());
    }
    
    public function testGetAccessToken_noValidCredentials(): void
    {
        $this->account->setAccessToken('expired_token');
        $this->account->setAccessTokenExpiresAt(new \DateTimeImmutable('-1 hour'));
        $this->account->setRefreshToken('expired_refresh_token');
        $this->account->setRefreshTokenExpiresAt(new \DateTimeImmutable('-1 hour'));
        $this->account->setCode('expired_code');
        $this->account->setCodeExpiresAt(new \DateTimeImmutable('-1 hour'));
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No valid code available');
        
        $this->authService->getAccessToken($this->account);
    }
    
    public function testGetAccessToken_tokenResponse_missingAccessToken(): void
    {
        $this->account->setCode('valid_code');
        $this->account->setCodeExpiresAt(new \DateTimeImmutable('+5 minutes'));
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            // Missing access_token
            'expires_in' => 3600,
        ]);
        
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn('https://example.com/oauth/callback');
            
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);
            
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No access token received');
        
        $this->authService->getAccessToken($this->account);
    }
} 