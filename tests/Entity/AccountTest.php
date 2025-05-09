<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    public function testBasicProperties(): void
    {
        $name = 'TestJDAccount';
        $appKey = 'test_app_key';
        $appSecret = 'test_app_secret';
        
        $this->account->setName($name);
        $this->account->setAppKey($appKey);
        $this->account->setAppSecret($appSecret);
        
        $this->assertSame($name, $this->account->getName());
        $this->assertSame($appKey, $this->account->getAppKey());
        $this->assertSame($appSecret, $this->account->getAppSecret());
    }
    
    public function testTokenProperties(): void
    {
        $accessToken = 'test_access_token';
        $refreshToken = 'test_refresh_token';
        $now = new \DateTimeImmutable();
        
        $this->account->setAccessToken($accessToken);
        $this->account->setRefreshToken($refreshToken);
        $this->account->setAccessTokenExpiresAt($now);
        $this->account->setRefreshTokenExpiresAt($now);
        
        $this->assertSame($accessToken, $this->account->getAccessToken());
        $this->assertSame($refreshToken, $this->account->getRefreshToken());
        $this->assertSame($now, $this->account->getAccessTokenExpiresAt());
        $this->assertSame($now, $this->account->getRefreshTokenExpiresAt());
    }
    
    public function testAccessTokenExpired_withNullExpirationDate(): void
    {
        $this->account->setAccessTokenExpiresAt(null);
        
        $this->assertTrue($this->account->isAccessTokenExpired());
    }
    
    public function testAccessTokenExpired_withFutureExpirationDate(): void
    {
        $futureDate = new \DateTimeImmutable('+1 hour');
        $this->account->setAccessTokenExpiresAt($futureDate);
        
        $this->assertFalse($this->account->isAccessTokenExpired());
    }
    
    public function testAccessTokenExpired_withPastExpirationDate(): void
    {
        $pastDate = new \DateTimeImmutable('-1 hour');
        $this->account->setAccessTokenExpiresAt($pastDate);
        
        $this->assertTrue($this->account->isAccessTokenExpired());
    }
    
    public function testRefreshTokenExpired_withNullExpirationDate(): void
    {
        $this->account->setRefreshTokenExpiresAt(null);
        
        $this->assertTrue($this->account->isRefreshTokenExpired());
    }
    
    public function testRefreshTokenExpired_withFutureExpirationDate(): void
    {
        $futureDate = new \DateTimeImmutable('+1 hour');
        $this->account->setRefreshTokenExpiresAt($futureDate);
        
        $this->assertFalse($this->account->isRefreshTokenExpired());
    }
    
    public function testRefreshTokenExpired_withPastExpirationDate(): void
    {
        $pastDate = new \DateTimeImmutable('-1 hour');
        $this->account->setRefreshTokenExpiresAt($pastDate);
        
        $this->assertTrue($this->account->isRefreshTokenExpired());
    }
    
    public function testCodeProperties(): void
    {
        $code = 'test_auth_code';
        $state = 'test_state';
        $expiresAt = new \DateTimeImmutable('+10 minutes');
        
        $this->account->setCode($code);
        $this->account->setState($state);
        $this->account->setCodeExpiresAt($expiresAt);
        
        $this->assertSame($code, $this->account->getCode());
        $this->assertSame($state, $this->account->getState());
        $this->assertSame($expiresAt, $this->account->getCodeExpiresAt());
    }
    
    public function testTimestampProperties(): void
    {
        $createTime = new \DateTime();
        $updateTime = new \DateTime();
        
        $this->account->setCreateTime($createTime);
        $this->account->setUpdateTime($updateTime);
        
        $this->assertSame($createTime, $this->account->getCreateTime());
        $this->assertSame($updateTime, $this->account->getUpdateTime());
    }
} 