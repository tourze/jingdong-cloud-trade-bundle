<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    protected function onSetUp(): void
    {
        // 彻底重置数据库连接状态，确保每个测试都从干净状态开始
        $connection = self::getEntityManager()->getConnection();

        // 关闭现有连接
        if ($connection->isConnected()) {
            $connection->close();
        }

        // 通过执行简单查询触发重新连接
        try {
            $connection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            // 忽略连接异常，让测试自然进行
        }

        $this->repository = $this->getRepository();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createAccount(array $data = []): Account
    {
        $account = new Account();

        $name = $data['name'] ?? 'Test Account';
        $account->setName(\is_string($name) ? $name : 'Test Account');

        $appKey = $data['appKey'] ?? 'test-app-key';
        $account->setAppKey(\is_string($appKey) ? $appKey : 'test-app-key');

        $appSecret = $data['appSecret'] ?? 'test-app-secret';
        $account->setAppSecret(\is_string($appSecret) ? $appSecret : 'test-app-secret');

        if (isset($data['accessToken']) && \is_string($data['accessToken'])) {
            $account->setAccessToken($data['accessToken']);
        }

        if (isset($data['refreshToken']) && \is_string($data['refreshToken'])) {
            $account->setRefreshToken($data['refreshToken']);
        }

        if (isset($data['accessTokenExpiresAt']) && $data['accessTokenExpiresAt'] instanceof \DateTimeImmutable) {
            $account->setAccessTokenExpiresAt($data['accessTokenExpiresAt']);
        }

        $persistedAccount = $this->persistAndFlush($account);
        $this->assertInstanceOf(Account::class, $persistedAccount);

        return $persistedAccount;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(AccountRepository::class, $this->repository);
    }

    public function testFindOneValid(): void
    {
        $account = $this->createAccount();

        $result = $this->repository->findOneValid();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Account::class, $result);
    }

    public function testFindOneValidShouldReturnAccountWhenExists(): void
    {
        $this->createAccount(['name' => 'Test Account for Valid']);

        $result = $this->repository->findOneValid();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Account::class, $result);
    }

    public function testSaveShouldPersistAccountWithFlush(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test-app-key');
        $account->setAppSecret('test-app-secret');

        $this->repository->save($account, true);
        $this->assertGreaterThan(0, $account->getId());

        $persisted = $this->repository->find($account->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('Test Account', $persisted->getName());
        $this->assertSame('test-app-key', $persisted->getAppKey());
        $this->assertSame('test-app-secret', $persisted->getAppSecret());
    }

    public function testSaveShouldPersistAccountWithoutFlush(): void
    {
        $account = new Account();
        $account->setName('Test Account No Flush');
        $account->setAppKey('test-app-key-no-flush');
        $account->setAppSecret('test-app-secret-no-flush');

        $this->repository->save($account, false);
        self::getEntityManager()->flush();
        $this->assertGreaterThan(0, $account->getId());

        $persisted = $this->repository->find($account->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('Test Account No Flush', $persisted->getName());
    }

    public function testRemoveShouldDeleteAccountWithFlush(): void
    {
        $account = $this->createAccount(['name' => 'Account to Delete']);
        $accountId = $account->getId();

        $this->repository->remove($account, true);

        $deleted = $this->repository->find($accountId);
        $this->assertNull($deleted);
    }

    public function testRemoveShouldDeleteAccountWithoutFlush(): void
    {
        $account = $this->createAccount(['name' => 'Account to Delete No Flush']);
        $accountId = $account->getId();

        $this->repository->remove($account, false);
        self::getEntityManager()->flush();

        $deleted = $this->repository->find($accountId);
        $this->assertNull($deleted);
    }

    public function testFindShouldReturnAccountById(): void
    {
        $account = $this->createAccount(['name' => 'Findable Account']);

        $found = $this->repository->find($account->getId());
        $this->assertNotNull($found);
        $this->assertSame($account->getId(), $found->getId());
        $this->assertSame('Findable Account', $found->getName());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $found = $this->repository->find(99999);
        $this->assertNull($found);
    }

    public function testFindAllShouldReturnAllAccounts(): void
    {
        $initialCount = count($this->repository->findAll());

        $account1 = $this->createAccount(['name' => 'Account 1']);
        $account2 = $this->createAccount(['name' => 'Account 2']);

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);

        $names = array_map(fn ($acc) => $acc->getName(), $all);
        $this->assertContains('Account 1', $names);
        $this->assertContains('Account 2', $names);
    }

    public function testFindByShouldReturnAccountsMatchingCriteria(): void
    {
        $this->createAccount(['name' => 'Test Account 1', 'appKey' => 'key1']);
        $this->createAccount(['name' => 'Test Account 2', 'appKey' => 'key1']);
        $this->createAccount(['name' => 'Other Account', 'appKey' => 'key2']);

        $found = $this->repository->findBy(['appKey' => 'key1']);
        $this->assertCount(2, $found);

        foreach ($found as $account) {
            $this->assertSame('key1', $account->getAppKey());
        }
    }

    public function testFindOneByShouldReturnSingleAccountMatchingCriteria(): void
    {
        $this->createAccount(['name' => 'Unique Account', 'appKey' => 'unique-key']);
        $this->createAccount(['name' => 'Other Account', 'appKey' => 'other-key']);

        $found = $this->repository->findOneBy(['appKey' => 'unique-key']);
        $this->assertNotNull($found);
        $this->assertSame('Unique Account', $found->getName());
        $this->assertSame('unique-key', $found->getAppKey());
    }

    public function testFindOneByShouldReturnNullWhenNoCriteriasMatch(): void
    {
        $this->createAccount(['appKey' => 'existing-key']);

        $found = $this->repository->findOneBy(['appKey' => 'non-existent-key']);
        $this->assertNull($found);
    }

    public function testSaveWithTokensShouldPersistAllTokenData(): void
    {
        $account = new Account();
        $account->setName('Token Account');
        $account->setAppKey('token-app-key');
        $account->setAppSecret('token-app-secret');
        $account->setAccessToken('access-token-123');
        $account->setRefreshToken('refresh-token-456');

        $expiresAt = new \DateTimeImmutable('+1 hour');
        $account->setAccessTokenExpiresAt($expiresAt);

        $refreshExpiresAt = new \DateTimeImmutable('+30 days');
        $account->setRefreshTokenExpiresAt($refreshExpiresAt);

        $this->repository->save($account);

        $persisted = $this->repository->find($account->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('access-token-123', $persisted->getAccessToken());
        $this->assertSame('refresh-token-456', $persisted->getRefreshToken());
        $this->assertEquals($expiresAt, $persisted->getAccessTokenExpiresAt());
        $this->assertEquals($refreshExpiresAt, $persisted->getRefreshTokenExpiresAt());
    }

    public function testSaveWithAuthCodeShouldPersistCodeData(): void
    {
        $account = new Account();
        $account->setName('Code Account');
        $account->setAppKey('code-app-key');
        $account->setAppSecret('code-app-secret');
        $account->setCode('auth-code-789');
        $account->setState('test-state');

        $codeExpiresAt = new \DateTimeImmutable('+10 minutes');
        $account->setCodeExpiresAt($codeExpiresAt);

        $this->repository->save($account);

        $persisted = $this->repository->find($account->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('auth-code-789', $persisted->getCode());
        $this->assertSame('test-state', $persisted->getState());
        $this->assertEquals($codeExpiresAt, $persisted->getCodeExpiresAt());
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $this->createAccount(['name' => 'Z Last Account', 'appKey' => 'order-test-key']);
        $this->createAccount(['name' => 'A First Account', 'appKey' => 'order-test-key']);
        $this->createAccount(['name' => 'M Middle Account', 'appKey' => 'order-test-key']);

        $result = $this->repository->findOneBy(['appKey' => 'order-test-key'], ['name' => 'ASC']);

        $this->assertNotNull($result);
        $this->assertSame('A First Account', $result->getName());
    }

    public function testFindByWithNullValue(): void
    {
        $this->createAccount(['name' => 'No Token Account', 'accessToken' => null]);
        $this->createAccount(['name' => 'With Token Account', 'accessToken' => 'some-token']);

        $result = $this->repository->findBy(['accessToken' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $account) {
            $this->assertNull($account->getAccessToken());
        }
    }

    public function testCountWithNullValue(): void
    {
        $initialNullCount = $this->repository->count(['accessToken' => null]);

        $this->createAccount(['name' => 'Null Token 1', 'accessToken' => null]);
        $this->createAccount(['name' => 'Null Token 2', 'accessToken' => null]);
        $this->createAccount(['name' => 'With Token', 'accessToken' => 'token-value']);

        $nullTokenCount = $this->repository->count(['accessToken' => null]);
        $this->assertSame($initialNullCount + 2, $nullTokenCount);
    }

    public function testFindByWithRefreshTokenNullValue(): void
    {
        $this->createAccount(['name' => 'No Refresh Token', 'refreshToken' => null]);
        $this->createAccount(['name' => 'With Refresh Token', 'refreshToken' => 'refresh-token-value']);

        $result = $this->repository->findBy(['refreshToken' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $account) {
            $this->assertNull($account->getRefreshToken());
        }
    }

    public function testCountWithRefreshTokenNullValue(): void
    {
        $initialNullCount = $this->repository->count(['refreshToken' => null]);

        $this->createAccount(['name' => 'Null Refresh 1', 'refreshToken' => null]);
        $this->createAccount(['name' => 'Null Refresh 2', 'refreshToken' => null]);
        $this->createAccount(['name' => 'With Refresh', 'refreshToken' => 'refresh-value']);

        $nullRefreshCount = $this->repository->count(['refreshToken' => null]);
        $this->assertSame($initialNullCount + 2, $nullRefreshCount);
    }

    public function testFindByWithCreateTimeNullValue(): void
    {
        $account1 = $this->createAccount(['name' => 'Null Create Time']);
        $account1->setCreateTime(null);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccount(['name' => 'With Create Time']);
        $account2->setCreateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($account2);

        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['createTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $account) {
            $this->assertNull($account->getCreateTime());
        }
    }

    public function testCountWithUpdateTimeNullValue(): void
    {
        $initialNullCount = $this->repository->count(['updateTime' => null]);

        $account1 = $this->createAccount(['name' => 'Null Update 1']);
        $account1->setUpdateTime(null);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccount(['name' => 'Null Update 2']);
        $account2->setUpdateTime(null);
        self::getEntityManager()->persist($account2);

        $account3 = $this->createAccount(['name' => 'With Update']);
        $account3->setUpdateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($account3);

        self::getEntityManager()->flush();

        $nullUpdateCount = $this->repository->count(['updateTime' => null]);
        $this->assertSame($initialNullCount + 2, $nullUpdateCount);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAppKey('test_app_key_' . uniqid());
        $account->setAppSecret('test_app_secret_' . uniqid());

        return $account;
    }

    protected function getRepository(): AccountRepository
    {
        return self::getService(AccountRepository::class);
    }
}
