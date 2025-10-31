<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Logistics;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Repository\LogisticsRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsRepository::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsRepositoryTest extends AbstractRepositoryTestCase
{
    private LogisticsRepository $repository;

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
        $this->testOrder->setOrderState('WAIT_GOODS_RECEIVE_CONFIRM');
        $this->testOrder->setPaymentState('PAID');
        $this->testOrder->setLogisticsState('SHIPPED');
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
    private function createLogistics(array $data = []): Logistics
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->testAccount);

        $order = $data['order'] ?? $this->testOrder;
        $logistics->setOrder($order instanceof Order ? $order : $this->testOrder);

        $logisticsCode = $data['logisticsCode'] ?? 'SF';
        $logistics->setLogisticsCode(\is_string($logisticsCode) ? $logisticsCode : 'SF');

        $logisticsName = $data['logisticsName'] ?? '顺丰速运';
        $logistics->setLogisticsName(\is_string($logisticsName) ? $logisticsName : '顺丰速运');

        $waybillCode = $data['waybillCode'] ?? 'WAYBILL' . uniqid();
        $logistics->setWaybillCode(\is_string($waybillCode) ? $waybillCode : 'WAYBILL' . uniqid());

        $persistedLogistics = $this->persistAndFlush($logistics);
        $this->assertInstanceOf(Logistics::class, $persistedLogistics);

        return $persistedLogistics;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(LogisticsRepository::class, $this->repository);
    }

    public function testFindByOrderId(): void
    {
        $logistics1 = $this->createLogistics();
        $logistics2 = $this->createLogistics();

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

        $this->createLogistics(['order' => $otherOrder]);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $logisticsIds = array_map(fn ($log) => $log->getId(), $result);
        $this->assertContains($logistics1->getId(), $logisticsIds);
        $this->assertContains($logistics2->getId(), $logisticsIds);
    }

    public function testFindByWaybillCode(): void
    {
        $logistics = $this->createLogistics(['waybillCode' => 'WAYBILL123456']);

        $result = $this->repository->findByWaybillCode('WAYBILL123456');
        $this->assertNotNull($result);
        $this->assertSame($logistics->getId(), $result->getId());
        $this->assertSame('WAYBILL123456', $result->getWaybillCode());
    }

    public function testFindByWaybillCodeReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByWaybillCode('NONEXISTENT');
        $this->assertNull($result);
    }

    public function testSaveShouldPersistLogistics(): void
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->testAccount);
        $logistics->setOrder($this->testOrder);
        $logistics->setLogisticsCode('YTO');
        $logistics->setLogisticsName('圆通速递');
        $logistics->setWaybillCode('YTO123456789');

        $this->repository->save($logistics);

        $this->assertNotNull($logistics->getId());
        $this->assertSame('YTO', $logistics->getLogisticsCode());
        $this->assertSame('圆通速递', $logistics->getLogisticsName());
        $this->assertSame('YTO123456789', $logistics->getWaybillCode());
    }

    public function testRemoveShouldDeleteLogistics(): void
    {
        $logistics = $this->createLogistics(['waybillCode' => 'TO_BE_DELETED']);
        $logisticsId = $logistics->getId();

        $this->repository->remove($logistics);

        $deletedLogistics = $this->repository->find($logisticsId);
        $this->assertNull($deletedLogistics);
    }

    public function testFindShouldReturnLogisticsById(): void
    {
        $logistics = $this->createLogistics(['waybillCode' => 'FIND_TEST']);

        $foundLogistics = $this->repository->find($logistics->getId());

        $this->assertNotNull($foundLogistics);
        $this->assertSame($logistics->getId(), $foundLogistics->getId());
        $this->assertSame('FIND_TEST', $foundLogistics->getWaybillCode());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllLogistics(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createLogistics(['waybillCode' => 'LOGISTICS1']);
        $this->createLogistics(['waybillCode' => 'LOGISTICS2']);
        $this->createLogistics(['waybillCode' => 'LOGISTICS3']);

        $allLogistics = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allLogistics);
        foreach ($allLogistics as $logistics) {
            $this->assertInstanceOf(Logistics::class, $logistics);
        }
    }

    public function testFindByShouldReturnMatchingLogistics(): void
    {
        $initialSfCount = $this->repository->count(['logisticsCode' => 'SF']);

        $this->createLogistics(['logisticsCode' => 'SF']);
        $this->createLogistics(['logisticsCode' => 'SF']);
        $this->createLogistics(['logisticsCode' => 'YTO']);

        $sfLogistics = $this->repository->findBy(['logisticsCode' => 'SF']);

        $this->assertCount($initialSfCount + 2, $sfLogistics);
        foreach ($sfLogistics as $logistics) {
            $this->assertSame('SF', $logistics->getLogisticsCode());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createLogistics(['waybillCode' => "WAYBILL{$i}"]);
        }

        $limitedLogistics = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedLogistics);
    }

    public function testFindOneByShouldReturnSingleLogistics(): void
    {
        $this->createLogistics(['waybillCode' => 'REGULAR']);
        $specificLogistics = $this->createLogistics(['waybillCode' => 'SPECIFIC']);

        $foundLogistics = $this->repository->findOneBy(['waybillCode' => 'SPECIFIC']);

        $this->assertNotNull($foundLogistics);
        $this->assertSame($specificLogistics->getId(), $foundLogistics->getId());
        $this->assertSame('SPECIFIC', $foundLogistics->getWaybillCode());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createLogistics(['waybillCode' => 'SOME_WAYBILL']);

        $result = $this->repository->findOneBy(['waybillCode' => 'NON_EXISTENT']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createLogistics();
        $this->createLogistics();
        $this->createLogistics();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $initialSfCount = $this->repository->count(['logisticsCode' => 'SF']);

        $this->createLogistics(['logisticsCode' => 'SF']);
        $this->createLogistics(['logisticsCode' => 'SF']);
        $this->createLogistics(['logisticsCode' => 'YTO']);

        $sfCount = $this->repository->count(['logisticsCode' => 'SF']);

        $this->assertSame($initialSfCount + 2, $sfCount);
    }

    public function testSaveShouldHandleOptionalFields(): void
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->testAccount);
        $logistics->setOrder($this->testOrder);
        $logistics->setLogisticsCode('EMS');
        $logistics->setLogisticsName('中国邮政');
        $logistics->setWaybillCode('EMS987654321');

        $this->repository->save($logistics);

        $this->assertNotNull($logistics->getId());
        $this->assertSame('EMS', $logistics->getLogisticsCode());
        $this->assertSame('中国邮政', $logistics->getLogisticsName());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->testAccount);
        $logistics->setOrder($this->testOrder);
        $logistics->setLogisticsCode('TEST');
        $logistics->setLogisticsName('Test Express');
        $logistics->setWaybillCode('NOT_PERSISTED');

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($logistics);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $oldLogistics = $this->createLogistics(['logisticsCode' => 'SF', 'waybillCode' => 'AAA_OLD']);
        $newLogistics = $this->createLogistics(['logisticsCode' => 'SF', 'waybillCode' => 'ZZZ_NEW']);

        // 找到顺丰物流中按运单号排序最后的那个
        $result = $this->repository->findOneBy(['logisticsCode' => 'SF'], ['waybillCode' => 'DESC']);

        $this->assertInstanceOf(Logistics::class, $result);
        $this->assertSame($newLogistics->getId(), $result->getId());
    }

    public function testFindByWithStringFieldCriteriaShouldReturnCorrectResults(): void
    {
        $sfLogistics1 = $this->createLogistics(['logisticsCode' => 'SF', 'logisticsName' => '顺丰速运']);
        $sfLogistics2 = $this->createLogistics(['logisticsCode' => 'SF', 'logisticsName' => '顺丰速运']);
        $ytoLogistics = $this->createLogistics(['logisticsCode' => 'YTO', 'logisticsName' => '圆通速递']);

        $sfResults = $this->repository->findBy(['logisticsName' => '顺丰速运']);

        $this->assertIsArray($sfResults);
        $this->assertGreaterThanOrEqual(2, count($sfResults));

        $sfIds = array_map(fn ($log) => $log->getId(), $sfResults);
        $this->assertContains($sfLogistics1->getId(), $sfIds);
        $this->assertContains($sfLogistics2->getId(), $sfIds);
        $this->assertNotContains($ytoLogistics->getId(), $sfIds);
    }

    public function testFindByOrderIdShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createLogistics();

        $result = $this->repository->findByOrderId(99999);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByWaybillCodeShouldHandleNonExistentCodes(): void
    {
        $this->createLogistics(['waybillCode' => 'EXISTING_CODE']);

        $result = $this->repository->findByWaybillCode('NONEXISTENT_CODE');
        $this->assertNull($result);
    }

    public function testFindByLogisticsCodeAndName(): void
    {
        $uniqueCode = 'TEST_' . uniqid();
        $uniqueName = 'Test Logistics ' . uniqid();

        $sfLogistics = $this->createLogistics(['logisticsCode' => $uniqueCode, 'logisticsName' => $uniqueName]);
        $ytoLogistics = $this->createLogistics(['logisticsCode' => 'YTO', 'logisticsName' => '圆通速递']);

        $sfResults = $this->repository->findBy(['logisticsCode' => $uniqueCode, 'logisticsName' => $uniqueName]);

        $this->assertCount(1, $sfResults);
        $this->assertSame($sfLogistics->getId(), $sfResults[0]->getId());
    }

    public function testSaveAndRemoveWithFlushParameter(): void
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->testAccount);
        $logistics->setOrder($this->testOrder);
        $logistics->setLogisticsCode('ZTO');
        $logistics->setLogisticsName('中通快递');
        $logistics->setWaybillCode('ZTO123456');

        // 测试保存时不刷新
        $this->repository->save($logistics, false);
        self::getEntityManager()->flush();

        $this->assertNotNull($logistics->getId());

        // 测试删除时刷新
        $logisticsId = $logistics->getId();
        $this->repository->remove($logistics, true);

        $deleted = $this->repository->find($logisticsId);
        $this->assertNull($deleted);
    }

    public function testFindByWithNullValue(): void
    {
        $this->createLogistics(['waybillCode' => 'null-test']);
        $this->createLogistics(['waybillCode' => 'with-value']);

        // 注意：Logistics 实体没有可为空的字段，所以这里测试基本查询
        $result = $this->repository->findBy(['logisticsCode' => 'SF']);

        $this->assertIsArray($result);
        foreach ($result as $logistics) {
            $this->assertSame('SF', $logistics->getLogisticsCode());
        }
    }

    public function testCountWithNullValue(): void
    {
        $initialSfCount = $this->repository->count(['logisticsCode' => 'SF']);

        $this->createLogistics(['logisticsCode' => 'SF', 'waybillCode' => 'SF001']);
        $this->createLogistics(['logisticsCode' => 'SF', 'waybillCode' => 'SF002']);
        $this->createLogistics(['logisticsCode' => 'YTO', 'waybillCode' => 'YTO001']);

        $sfCount = $this->repository->count(['logisticsCode' => 'SF']);
        $this->assertSame($initialSfCount + 2, $sfCount);
    }

    public function testFindByWithAssociation(): void
    {
        $logistics1 = $this->createLogistics(['waybillCode' => 'assoc-test-1']);
        $logistics2 = $this->createLogistics(['waybillCode' => 'assoc-test-2']);

        $result = $this->repository->findBy(['account' => $this->testAccount]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundIds = array_map(fn ($log) => $log->getId(), $result);
        $this->assertContains($logistics1->getId(), $foundIds);
        $this->assertContains($logistics2->getId(), $foundIds);

        foreach ($result as $logistics) {
            $this->assertSame($this->testAccount->getId(), $logistics->getAccount()->getId());
        }
    }

    public function testCountWithAssociation(): void
    {
        $this->createLogistics(['waybillCode' => 'count-assoc-1']);
        $this->createLogistics(['waybillCode' => 'count-assoc-2']);

        $accountLogisticsCount = $this->repository->count(['account' => $this->testAccount]);
        $this->assertGreaterThanOrEqual(2, $accountLogisticsCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createLogistics(['waybillCode' => 'test-account-logistics']);

        $logisticsWithOtherAccount = new Logistics();
        $logisticsWithOtherAccount->setAccount($otherAccount);
        $logisticsWithOtherAccount->setOrder($this->testOrder);
        $logisticsWithOtherAccount->setLogisticsCode('YTO');
        $logisticsWithOtherAccount->setLogisticsName('圆通速递');
        $logisticsWithOtherAccount->setWaybillCode('other-account-logistics');
        $this->persistAndFlush($logisticsWithOtherAccount);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Logistics::class, $result);
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

        $this->createLogistics(['waybillCode' => 'test-account-1']);
        $this->createLogistics(['waybillCode' => 'test-account-2']);

        $logisticsWithOtherAccount = new Logistics();
        $logisticsWithOtherAccount->setAccount($otherAccount);
        $logisticsWithOtherAccount->setOrder($this->testOrder);
        $logisticsWithOtherAccount->setLogisticsCode('EMS');
        $logisticsWithOtherAccount->setLogisticsName('中国邮政');
        $logisticsWithOtherAccount->setWaybillCode('other-account-logistics-2');
        $this->persistAndFlush($logisticsWithOtherAccount);

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

        $this->createLogistics(['waybillCode' => 'test-order-logistics']);

        $logisticsWithOtherOrder = new Logistics();
        $logisticsWithOtherOrder->setAccount($this->testAccount);
        $logisticsWithOtherOrder->setOrder($otherOrder);
        $logisticsWithOtherOrder->setLogisticsCode('ZTO');
        $logisticsWithOtherOrder->setLogisticsName('中通快递');
        $logisticsWithOtherOrder->setWaybillCode('other-order-logistics');
        $this->persistAndFlush($logisticsWithOtherOrder);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Logistics::class, $result);
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

        $this->createLogistics(['waybillCode' => 'test-order-1']);
        $this->createLogistics(['waybillCode' => 'test-order-2']);

        $logisticsWithOtherOrder = new Logistics();
        $logisticsWithOtherOrder->setAccount($this->testAccount);
        $logisticsWithOtherOrder->setOrder($otherOrder);
        $logisticsWithOtherOrder->setLogisticsCode('STO');
        $logisticsWithOtherOrder->setLogisticsName('申通快递');
        $logisticsWithOtherOrder->setWaybillCode('other-order-logistics-2');
        $this->persistAndFlush($logisticsWithOtherOrder);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function getRepository(): LogisticsRepository
    {
        return self::getService(LogisticsRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setAppKey('test-app-key-' . uniqid());
        $account->setAppSecret('test-app-secret-' . uniqid());
        $account->setName('Test Account ' . uniqid());

        $order = new Order();
        $order->setAccount($account);
        $order->setOrderId('JD' . uniqid());
        $order->setOrderState('待支付');
        $order->setPaymentState('未支付');
        $order->setLogisticsState('未发货');
        $order->setReceiverName('张三');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京市');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('朝阳区三里屯太古里');
        $order->setOrderTotalPrice('199.00');
        $order->setOrderPaymentPrice('199.00');
        $order->setFreightPrice('0.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true);

        $logistics = new Logistics();
        $logistics->setAccount($account);
        $logistics->setOrder($order);
        $logistics->setLogisticsCode('SF');
        $logistics->setLogisticsName('顺丰速运');
        $logistics->setWaybillCode('SF' . uniqid());

        return $logistics;
    }
}
