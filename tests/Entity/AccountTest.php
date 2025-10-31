<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'TestJDAccount'];
        yield 'appKey' => ['appKey', 'test_app_key'];
        yield 'appSecret' => ['appSecret', 'test_app_secret'];
        yield 'accessToken' => ['accessToken', 'test_access_token'];
        yield 'refreshToken' => ['refreshToken', 'test_refresh_token'];
        yield 'accessTokenExpireTime' => ['accessTokenExpireTime', new \DateTimeImmutable('+1 hour')];
        yield 'refreshTokenExpireTime' => ['refreshTokenExpireTime', new \DateTimeImmutable('+1 day')];
        yield 'code' => ['code', 'test_auth_code'];
        yield 'codeExpireTime' => ['codeExpireTime', new \DateTimeImmutable('+10 minutes')];
        yield 'state' => ['state', 'test_state'];
    }

    public function testBasicProperties(): void
    {
        $account = new Account();
        $name = 'TestJDAccount';
        $appKey = 'test_app_key';
        $appSecret = 'test_app_secret';

        $account->setName($name);
        $account->setAppKey($appKey);
        $account->setAppSecret($appSecret);

        $this->assertSame($name, $account->getName());
        $this->assertSame($appKey, $account->getAppKey());
        $this->assertSame($appSecret, $account->getAppSecret());
    }

    public function testTokenProperties(): void
    {
        $account = new Account();
        $accessToken = 'test_access_token';
        $refreshToken = 'test_refresh_token';
        $now = new \DateTimeImmutable();

        $account->setAccessToken($accessToken);
        $account->setRefreshToken($refreshToken);
        $account->setAccessTokenExpireTime($now);
        $account->setRefreshTokenExpireTime($now);

        $this->assertSame($accessToken, $account->getAccessToken());
        $this->assertSame($refreshToken, $account->getRefreshToken());
        $this->assertSame($now, $account->getAccessTokenExpireTime());
        $this->assertSame($now, $account->getRefreshTokenExpireTime());
    }

    public function testAccessTokenExpiredWithNullExpirationDate(): void
    {
        $account = new Account();
        $account->setAccessTokenExpireTime(null);

        $this->assertTrue($account->isAccessTokenExpired());
    }

    public function testAccessTokenExpiredWithFutureExpirationDate(): void
    {
        $account = new Account();
        $futureDate = new \DateTimeImmutable('+1 hour');
        $account->setAccessTokenExpireTime($futureDate);

        $this->assertFalse($account->isAccessTokenExpired());
    }

    public function testAccessTokenExpiredWithPastExpirationDate(): void
    {
        $account = new Account();
        $pastDate = new \DateTimeImmutable('-1 hour');
        $account->setAccessTokenExpireTime($pastDate);

        $this->assertTrue($account->isAccessTokenExpired());
    }

    public function testRefreshTokenExpiredWithNullExpirationDate(): void
    {
        $account = new Account();
        $account->setRefreshTokenExpireTime(null);

        $this->assertTrue($account->isRefreshTokenExpired());
    }

    public function testRefreshTokenExpiredWithFutureExpirationDate(): void
    {
        $account = new Account();
        $futureDate = new \DateTimeImmutable('+1 hour');
        $account->setRefreshTokenExpireTime($futureDate);

        $this->assertFalse($account->isRefreshTokenExpired());
    }

    public function testRefreshTokenExpiredWithPastExpirationDate(): void
    {
        $account = new Account();
        $pastDate = new \DateTimeImmutable('-1 hour');
        $account->setRefreshTokenExpireTime($pastDate);

        $this->assertTrue($account->isRefreshTokenExpired());
    }

    public function testCodeProperties(): void
    {
        $account = new Account();
        $code = 'test_auth_code';
        $state = 'test_state';
        $expiresAt = new \DateTimeImmutable('+10 minutes');

        $account->setCode($code);
        $account->setState($state);
        $account->setCodeExpireTime($expiresAt);

        $this->assertSame($code, $account->getCode());
        $this->assertSame($state, $account->getState());
        $this->assertSame($expiresAt, $account->getCodeExpireTime());
    }

    public function testTimestampProperties(): void
    {
        $account = new Account();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        $account->setCreateTime($createTime);
        $account->setUpdateTime($updateTime);

        $this->assertSame($createTime, $account->getCreateTime());
        $this->assertSame($updateTime, $account->getUpdateTime());
    }
}
