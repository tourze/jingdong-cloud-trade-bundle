<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\AfsService;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Repository\AfsServiceRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AfsServiceRepository::class)]
#[RunTestsInSeparateProcesses]
final class AfsServiceRepositoryTest extends AbstractRepositoryTestCase
{
    private AfsServiceRepository $repository;

    private Account $testAccount;

    private Order $testOrder;

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

        $this->testAccount = new Account();
        $this->testAccount->setAppKey('test-app-key');
        $this->testAccount->setAppSecret('test-app-secret');
        $this->testAccount->setName('Test Account');
        $this->persistAndFlush($this->testAccount);

        $this->testOrder = new Order();
        $this->testOrder->setAccount($this->testAccount);
        $this->testOrder->setOrderId('123456');
        $this->testOrder->setOrderState('WAIT_SELLER_STOCK_OUT');
        $this->testOrder->setPaymentState('WAIT_PAY');
        $this->testOrder->setLogisticsState('WAIT_SHIP');
        $this->testOrder->setReceiverName('Test Receiver');
        $this->testOrder->setReceiverMobile('13800138000');
        $this->testOrder->setReceiverProvince('北京市');
        $this->testOrder->setReceiverCity('北京市');
        $this->testOrder->setReceiverCounty('朝阳区');
        $this->testOrder->setReceiverAddress('测试地址');
        $this->testOrder->setOrderTotalPrice('1000.00');
        $this->testOrder->setOrderPaymentPrice('1000.00');
        $this->testOrder->setFreightPrice('0.00');
        $this->testOrder->setOrderTime(new \DateTimeImmutable());
        $this->testOrder->setSynced(true);
        $this->persistAndFlush($this->testOrder);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createAfsService(array $data = []): AfsService
    {
        $afsService = new AfsService();
        $afsService->setAccount($this->testAccount);

        $this->setAfsServiceBasicFields($afsService, $data);
        $this->setAfsServiceOptionalFields($afsService, $data);

        $persistedAfsService = $this->persistAndFlush($afsService);
        $this->assertInstanceOf(AfsService::class, $persistedAfsService);

        return $persistedAfsService;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setAfsServiceBasicFields(AfsService $afsService, array $data): void
    {
        $order = $data['order'] ?? $this->testOrder;
        $afsService->setOrder($order instanceof Order ? $order : $this->testOrder);

        $afsServiceId = $data['afsServiceId'] ?? 'afs-' . uniqid();
        $afsService->setAfsServiceId(\is_string($afsServiceId) ? $afsServiceId : 'afs-' . uniqid());

        $afsType = $data['afsType'] ?? '10';
        $afsService->setAfsType(\is_string($afsType) ? $afsType : '10');

        $afsServiceState = $data['afsServiceState'] ?? 'PENDING';
        $afsService->setAfsServiceState(\is_string($afsServiceState) ? $afsServiceState : 'PENDING');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setAfsServiceOptionalFields(AfsService $afsService, array $data): void
    {
        $this->setAfsServiceStringFields($afsService, $data);
        $this->setAfsServiceDateFields($afsService, $data);
        $this->setAfsServiceLogisticsFields($afsService, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setAfsServiceStringFields(AfsService $afsService, array $data): void
    {
        if (isset($data['applyReason']) && \is_string($data['applyReason'])) {
            $afsService->setApplyReason($data['applyReason']);
        }

        if (isset($data['applyDescription']) && \is_string($data['applyDescription'])) {
            $afsService->setApplyDescription($data['applyDescription']);
        }

        if (isset($data['refundAmount']) && \is_string($data['refundAmount'])) {
            $afsService->setRefundAmount($data['refundAmount']);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setAfsServiceDateFields(AfsService $afsService, array $data): void
    {
        if (isset($data['applyTime']) && $data['applyTime'] instanceof \DateTimeImmutable) {
            $afsService->setApplyTime($data['applyTime']);
        }

        if (isset($data['auditTime']) && $data['auditTime'] instanceof \DateTimeImmutable) {
            $afsService->setAuditTime($data['auditTime']);
        }

        if (isset($data['completeTime']) && $data['completeTime'] instanceof \DateTimeImmutable) {
            $afsService->setCompleteTime($data['completeTime']);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setAfsServiceLogisticsFields(AfsService $afsService, array $data): void
    {
        if (isset($data['logisticsCompany']) && \is_string($data['logisticsCompany'])) {
            $afsService->setLogisticsCompany($data['logisticsCompany']);
        }

        if (isset($data['logisticsNo']) && \is_string($data['logisticsNo'])) {
            $afsService->setLogisticsNo($data['logisticsNo']);
        }
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(AfsServiceRepository::class, $this->repository);
    }

    public function testFindByAfsServiceId(): void
    {
        $afsService = $this->createAfsService(['afsServiceId' => 'AFS123456']);

        $result = $this->repository->findByAfsServiceId('AFS123456');
        $this->assertNotNull($result);
        $this->assertSame($afsService->getId(), $result->getId());
        $this->assertSame('AFS123456', $result->getAfsServiceId());
    }

    public function testFindByAfsServiceIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByAfsServiceId('non-existent');
        $this->assertNull($result);
    }

    public function testFindByOrderId(): void
    {
        $afsService1 = $this->createAfsService(['afsServiceId' => 'AFS001']);
        $afsService2 = $this->createAfsService(['afsServiceId' => 'AFS002']);

        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('789012');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('SHIPPED');
        $otherOrder->setReceiverName('Test Receiver');
        $otherOrder->setReceiverMobile('13800138000');
        $otherOrder->setReceiverProvince('北京市');
        $otherOrder->setReceiverCity('北京市');
        $otherOrder->setReceiverCounty('朝阳区');
        $otherOrder->setReceiverAddress('测试地址');
        $otherOrder->setOrderTotalPrice('500.00');
        $otherOrder->setOrderPaymentPrice('500.00');
        $otherOrder->setFreightPrice('0.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $this->createAfsService(['order' => $otherOrder, 'afsServiceId' => 'AFS003']);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $afsServiceIds = array_map(fn ($afs) => $afs->getId(), $result);
        $this->assertContains($afsService1->getId(), $afsServiceIds);
        $this->assertContains($afsService2->getId(), $afsServiceIds);
    }

    public function testFindByOrderIdReturnsEmptyArrayWhenNotFound(): void
    {
        $result = $this->repository->findByOrderId(999999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSaveShouldPersistAfsService(): void
    {
        $afsService = new AfsService();
        $afsService->setAccount($this->testAccount);
        $afsService->setOrder($this->testOrder);
        $afsService->setAfsServiceId('SAVE_AFS_123');
        $afsService->setAfsType('20');
        $afsService->setAfsServiceState('APPROVED');
        $afsService->setApplyReason('商品质量问题');
        $afsService->setApplyDescription('保存测试售后服务');
        $afsService->setApplyTime(new \DateTimeImmutable());

        $this->repository->save($afsService);

        $this->assertNotNull($afsService->getId());
        $this->assertSame('SAVE_AFS_123', $afsService->getAfsServiceId());
        $this->assertSame('20', $afsService->getAfsType());
        $this->assertSame('APPROVED', $afsService->getAfsServiceState());
        $this->assertSame('商品质量问题', $afsService->getApplyReason());
        $this->assertSame('保存测试售后服务', $afsService->getApplyDescription());
    }

    public function testRemoveShouldDeleteAfsService(): void
    {
        $afsService = $this->createAfsService(['afsServiceId' => 'TO_BE_DELETED']);
        $afsServiceId = $afsService->getId();

        $this->repository->remove($afsService);

        $deletedAfsService = $this->repository->find($afsServiceId);
        $this->assertNull($deletedAfsService);
    }

    public function testFindShouldReturnAfsServiceById(): void
    {
        $afsService = $this->createAfsService(['afsServiceId' => 'FIND_TEST_AFS']);

        $foundAfsService = $this->repository->find($afsService->getId());

        $this->assertNotNull($foundAfsService);
        $this->assertSame($afsService->getId(), $foundAfsService->getId());
        $this->assertSame('FIND_TEST_AFS', $foundAfsService->getAfsServiceId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllAfsServices(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createAfsService(['afsServiceId' => 'AFS001']);
        $this->createAfsService(['afsServiceId' => 'AFS002']);
        $this->createAfsService(['afsServiceId' => 'AFS003']);

        $allAfsServices = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allAfsServices);
        foreach ($allAfsServices as $afsService) {
            $this->assertInstanceOf(AfsService::class, $afsService);
        }
    }

    public function testFindByShouldReturnMatchingAfsServices(): void
    {
        $initialType10Count = count($this->repository->findBy(['afsType' => '10']));

        $this->createAfsService(['afsType' => '10']);
        $this->createAfsService(['afsType' => '10']);
        $this->createAfsService(['afsType' => '20']);

        $type10Services = $this->repository->findBy(['afsType' => '10']);

        $this->assertCount($initialType10Count + 2, $type10Services);
        foreach ($type10Services as $afsService) {
            $this->assertSame('10', $afsService->getAfsType());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createAfsService(['afsServiceId' => "AFS{$i}"]);
        }

        $limitedAfsServices = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedAfsServices);
    }

    public function testFindOneByShouldReturnSingleAfsService(): void
    {
        $this->createAfsService(['afsServiceState' => 'PENDING']);
        $specificAfsService = $this->createAfsService(['afsServiceState' => 'APPROVED']);

        $foundAfsService = $this->repository->findOneBy(['afsServiceState' => 'APPROVED']);

        $this->assertNotNull($foundAfsService);
        $this->assertSame($specificAfsService->getId(), $foundAfsService->getId());
        $this->assertSame('APPROVED', $foundAfsService->getAfsServiceState());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createAfsService(['afsServiceState' => 'PENDING']);

        $result = $this->repository->findOneBy(['afsServiceState' => 'REJECTED']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createAfsService();
        $this->createAfsService();
        $this->createAfsService();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $this->createAfsService(['afsServiceState' => 'PENDING']);
        $this->createAfsService(['afsServiceState' => 'PENDING']);
        $this->createAfsService(['afsServiceState' => 'APPROVED']);

        $pendingCount = $this->repository->count(['afsServiceState' => 'PENDING']);

        $this->assertSame(2, $pendingCount);
    }

    public function testSaveShouldHandleOptionalFields(): void
    {
        $afsService = new AfsService();
        $afsService->setAccount($this->testAccount);
        $afsService->setOrder($this->testOrder);
        $afsService->setAfsServiceId('OPTIONAL_FIELDS');
        $afsService->setAfsType('30');
        $afsService->setAfsServiceState('PROCESSING');

        $this->repository->save($afsService);

        $this->assertNotNull($afsService->getId());
        $this->assertSame('30', $afsService->getAfsType());
        $this->assertSame('PROCESSING', $afsService->getAfsServiceState());
        $this->assertNull($afsService->getApplyReason());
        $this->assertNull($afsService->getApplyDescription());
        $this->assertNull($afsService->getApplyTime());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $afsService = new AfsService();
        $afsService->setAccount($this->testAccount);
        $afsService->setOrder($this->testOrder);
        $afsService->setAfsServiceId('NOT_PERSISTED');
        $afsService->setAfsType('10');
        $afsService->setAfsServiceState('PENDING');

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($afsService);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $this->createAfsService(['afsServiceId' => 'Z-Last', 'afsType' => 'ORDER_BY_TEST']);
        $this->createAfsService(['afsServiceId' => 'A-First', 'afsType' => 'ORDER_BY_TEST']);
        $this->createAfsService(['afsServiceId' => 'M-Middle', 'afsType' => 'ORDER_BY_TEST']);

        $result = $this->repository->findOneBy(['afsType' => 'ORDER_BY_TEST'], ['afsServiceId' => 'ASC']);

        $this->assertNotNull($result);
        $this->assertSame('A-First', $result->getAfsServiceId());
    }

    public function testFindByWithNullValue(): void
    {
        $this->createAfsService(['afsServiceId' => 'null-reason', 'applyReason' => null]);
        $this->createAfsService(['afsServiceId' => 'with-reason', 'applyReason' => 'Some reason']);

        $result = $this->repository->findBy(['applyReason' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $afsService) {
            $this->assertNull($afsService->getApplyReason());
        }
    }

    public function testCountWithNullValue(): void
    {
        $initialNullCount = $this->repository->count(['applyDescription' => null]);

        $this->createAfsService(['afsServiceId' => 'null-desc-1', 'applyDescription' => null]);
        $this->createAfsService(['afsServiceId' => 'null-desc-2', 'applyDescription' => null]);
        $this->createAfsService(['afsServiceId' => 'with-desc', 'applyDescription' => 'Description']);

        $nullDescCount = $this->repository->count(['applyDescription' => null]);
        $this->assertSame($initialNullCount + 2, $nullDescCount);
    }

    public function testFindByWithAssociation(): void
    {
        $afsService1 = $this->createAfsService(['afsServiceId' => 'assoc-test-1']);
        $afsService2 = $this->createAfsService(['afsServiceId' => 'assoc-test-2']);

        $result = $this->repository->findBy(['account' => $this->testAccount]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundIds = array_map(fn ($afs) => $afs->getId(), $result);
        $this->assertContains($afsService1->getId(), $foundIds);
        $this->assertContains($afsService2->getId(), $foundIds);

        foreach ($result as $afsService) {
            $this->assertSame($this->testAccount->getId(), $afsService->getAccount()->getId());
        }
    }

    public function testCountWithAssociation(): void
    {
        $this->createAfsService(['afsServiceId' => 'count-assoc-1']);
        $this->createAfsService(['afsServiceId' => 'count-assoc-2']);

        $accountAfsCount = $this->repository->count(['account' => $this->testAccount]);
        $this->assertGreaterThanOrEqual(2, $accountAfsCount);
    }

    public function testFindByWithApplyTimeNullValue(): void
    {
        $this->createAfsService(['afsServiceId' => 'null-apply-time', 'applyTime' => null]);
        $this->createAfsService(['afsServiceId' => 'with-apply-time', 'applyTime' => new \DateTimeImmutable()]);

        $result = $this->repository->findBy(['applyTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $afsService) {
            $this->assertNull($afsService->getApplyTime());
        }
    }

    public function testCountWithApplyTimeNullValue(): void
    {
        $initialNullCount = $this->repository->count(['applyTime' => null]);

        $this->createAfsService(['afsServiceId' => 'null-apply-1', 'applyTime' => null]);
        $this->createAfsService(['afsServiceId' => 'null-apply-2', 'applyTime' => null]);
        $this->createAfsService(['afsServiceId' => 'with-apply', 'applyTime' => new \DateTimeImmutable()]);

        $nullApplyTimeCount = $this->repository->count(['applyTime' => null]);
        $this->assertSame($initialNullCount + 2, $nullApplyTimeCount);
    }

    public function testFindByWithCreateTimeNullValue(): void
    {
        $afsService1 = $this->createAfsService(['afsServiceId' => 'null-create']);
        $afsService1->setCreateTime(null);
        self::getEntityManager()->persist($afsService1);

        $afsService2 = $this->createAfsService(['afsServiceId' => 'with-create']);
        $afsService2->setCreateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($afsService2);

        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['createTime' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $afsService) {
            $this->assertNull($afsService->getCreateTime());
        }
    }

    public function testCountWithUpdateTimeNullValue(): void
    {
        $initialNullCount = $this->repository->count(['updateTime' => null]);

        $afsService1 = $this->createAfsService(['afsServiceId' => 'null-update-1']);
        $afsService1->setUpdateTime(null);
        self::getEntityManager()->persist($afsService1);

        $afsService2 = $this->createAfsService(['afsServiceId' => 'null-update-2']);
        $afsService2->setUpdateTime(null);
        self::getEntityManager()->persist($afsService2);

        $afsService3 = $this->createAfsService(['afsServiceId' => 'with-update']);
        $afsService3->setUpdateTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($afsService3);

        self::getEntityManager()->flush();

        $nullUpdateCount = $this->repository->count(['updateTime' => null]);
        $this->assertSame($initialNullCount + 2, $nullUpdateCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createAfsService(['afsServiceId' => 'test-account-service']);

        $afsServiceWithOtherAccount = new AfsService();
        $afsServiceWithOtherAccount->setAccount($otherAccount);
        $afsServiceWithOtherAccount->setOrder($this->testOrder);
        $afsServiceWithOtherAccount->setAfsServiceId('other-account-service');
        $afsServiceWithOtherAccount->setAfsType('10');
        $afsServiceWithOtherAccount->setAfsServiceState('PENDING');
        $this->persistAndFlush($afsServiceWithOtherAccount);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(AfsService::class, $result);
        $this->assertSame($this->testAccount->getId(), $result->getAccount()->getId());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key-2');
        $otherAccount->setAppSecret('other-app-secret-2');
        $otherAccount->setName('Other Account 2');
        $this->persistAndFlush($otherAccount);

        $initialCount = $this->repository->count(['account' => $this->testAccount]);

        $this->createAfsService(['afsServiceId' => 'test-account-1']);
        $this->createAfsService(['afsServiceId' => 'test-account-2']);

        $afsServiceWithOtherAccount = new AfsService();
        $afsServiceWithOtherAccount->setAccount($otherAccount);
        $afsServiceWithOtherAccount->setOrder($this->testOrder);
        $afsServiceWithOtherAccount->setAfsServiceId('other-account-service');
        $afsServiceWithOtherAccount->setAfsType('10');
        $afsServiceWithOtherAccount->setAfsServiceState('PENDING');
        $this->persistAndFlush($afsServiceWithOtherAccount);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame($initialCount + 2, $count);
    }

    public function testFindOneByAssociationOrderShouldReturnMatchingEntity(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-123');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('DELIVERED');
        $otherOrder->setReceiverName('Other Receiver');
        $otherOrder->setReceiverMobile('13900139000');
        $otherOrder->setReceiverProvince('上海市');
        $otherOrder->setReceiverCity('上海市');
        $otherOrder->setReceiverCounty('浦东新区');
        $otherOrder->setReceiverAddress('其他测试地址');
        $otherOrder->setOrderTotalPrice('2000.00');
        $otherOrder->setOrderPaymentPrice('2000.00');
        $otherOrder->setFreightPrice('10.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $this->createAfsService(['afsServiceId' => 'test-order-service']);

        $afsServiceWithOtherOrder = new AfsService();
        $afsServiceWithOtherOrder->setAccount($this->testAccount);
        $afsServiceWithOtherOrder->setOrder($otherOrder);
        $afsServiceWithOtherOrder->setAfsServiceId('other-order-service');
        $afsServiceWithOtherOrder->setAfsType('10');
        $afsServiceWithOtherOrder->setAfsServiceState('PENDING');
        $this->persistAndFlush($afsServiceWithOtherOrder);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(AfsService::class, $result);
        $this->assertSame($this->testOrder->getId(), $result->getOrder()->getId());
    }

    public function testCountByAssociationOrderShouldReturnCorrectNumber(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-456');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('DELIVERED');
        $otherOrder->setReceiverName('Other Receiver 2');
        $otherOrder->setReceiverMobile('13800138001');
        $otherOrder->setReceiverProvince('广州市');
        $otherOrder->setReceiverCity('广州市');
        $otherOrder->setReceiverCounty('天河区');
        $otherOrder->setReceiverAddress('其他测试地址2');
        $otherOrder->setOrderTotalPrice('3000.00');
        $otherOrder->setOrderPaymentPrice('3000.00');
        $otherOrder->setFreightPrice('15.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $initialCount = $this->repository->count(['order' => $this->testOrder]);

        $this->createAfsService(['afsServiceId' => 'test-order-1']);
        $this->createAfsService(['afsServiceId' => 'test-order-2']);

        $afsServiceWithOtherOrder = new AfsService();
        $afsServiceWithOtherOrder->setAccount($this->testAccount);
        $afsServiceWithOtherOrder->setOrder($otherOrder);
        $afsServiceWithOtherOrder->setAfsServiceId('other-order-service-2');
        $afsServiceWithOtherOrder->setAfsType('10');
        $afsServiceWithOtherOrder->setAfsServiceState('PENDING');
        $this->persistAndFlush($afsServiceWithOtherOrder);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setAppKey('test_app_key_' . uniqid());
        $account->setAppSecret('test_app_secret_' . uniqid());
        $account->setName('Test Account ' . uniqid());

        $order = new Order();
        $order->setAccount($account);
        $order->setOrderId('order_' . uniqid());
        $order->setOrderState('WAIT_SELLER_STOCK_OUT');
        $order->setPaymentState('WAIT_PAY');
        $order->setLogisticsState('WAIT_SHIP');
        $order->setReceiverName('Test Receiver');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京市');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('测试地址');
        $order->setOrderTotalPrice('1000.00');
        $order->setOrderPaymentPrice('1000.00');
        $order->setFreightPrice('0.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true);

        $afsService = new AfsService();
        $afsService->setAccount($account);
        $afsService->setOrder($order);
        $afsService->setAfsServiceId('afs_' . uniqid());
        $afsService->setAfsType('10');
        $afsService->setAfsServiceState('PENDING');

        return $afsService;
    }

    protected function getRepository(): AfsServiceRepository
    {
        return self::getService(AfsServiceRepository::class);
    }
}
