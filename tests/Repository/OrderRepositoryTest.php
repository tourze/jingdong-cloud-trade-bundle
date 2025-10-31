<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Repository\OrderRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(OrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class OrderRepositoryTest extends AbstractRepositoryTestCase
{
    private OrderRepository $repository;

    private Account $testAccount;

    /**
     * @param array<string, mixed> $data
     */
    private function setOrderBasicFields(Order $order, array $data): void
    {
        $account = $data['account'] ?? $this->testAccount;
        $order->setAccount($account instanceof Account ? $account : $this->testAccount);

        $orderId = $data['orderId'] ?? (string) mt_rand(100000, 999999);
        $order->setOrderId(\is_string($orderId) ? $orderId : (string) mt_rand(100000, 999999));

        $orderState = $data['orderState'] ?? 'WAIT_PAY';
        $order->setOrderState(\is_string($orderState) ? $orderState : 'WAIT_PAY');

        $paymentState = $data['paymentState'] ?? 'WAIT_PAY';
        $order->setPaymentState(\is_string($paymentState) ? $paymentState : 'WAIT_PAY');

        $logisticsState = $data['logisticsState'] ?? 'WAIT_SHIP';
        $order->setLogisticsState(\is_string($logisticsState) ? $logisticsState : 'WAIT_SHIP');

        $orderTime = $data['orderTime'] ?? new \DateTimeImmutable();
        $order->setOrderTime($orderTime instanceof \DateTimeImmutable ? $orderTime : new \DateTimeImmutable());
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOrderReceiverFields(Order $order, array $data): void
    {
        $receiverName = $data['receiverName'] ?? 'Test Receiver';
        $order->setReceiverName(\is_string($receiverName) ? $receiverName : 'Test Receiver');

        $receiverMobile = $data['receiverMobile'] ?? '13800138000';
        $order->setReceiverMobile(\is_string($receiverMobile) ? $receiverMobile : '13800138000');

        $receiverProvince = $data['receiverProvince'] ?? '北京市';
        $order->setReceiverProvince(\is_string($receiverProvince) ? $receiverProvince : '北京市');

        $receiverCity = $data['receiverCity'] ?? '北京市';
        $order->setReceiverCity(\is_string($receiverCity) ? $receiverCity : '北京市');

        $receiverCounty = $data['receiverCounty'] ?? '朝阳区';
        $order->setReceiverCounty(\is_string($receiverCounty) ? $receiverCounty : '朝阳区');

        $receiverAddress = $data['receiverAddress'] ?? '测试地址';
        $order->setReceiverAddress(\is_string($receiverAddress) ? $receiverAddress : '测试地址');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOrderPriceFields(Order $order, array $data): void
    {
        $orderTotalPrice = $data['orderTotalPrice'] ?? '100.00';
        $order->setOrderTotalPrice(\is_string($orderTotalPrice) ? $orderTotalPrice : '100.00');

        $orderPaymentPrice = $data['orderPaymentPrice'] ?? '100.00';
        $order->setOrderPaymentPrice(\is_string($orderPaymentPrice) ? $orderPaymentPrice : '100.00');

        $freightPrice = $data['freightPrice'] ?? '0.00';
        $order->setFreightPrice(\is_string($freightPrice) ? $freightPrice : '0.00');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOrderOptionalFields(Order $order, array $data): void
    {
        if (isset($data['paymentTime']) && $data['paymentTime'] instanceof \DateTimeImmutable) {
            $order->setPaymentTime($data['paymentTime']);
        }

        if (isset($data['deliveryTime']) && $data['deliveryTime'] instanceof \DateTimeImmutable) {
            $order->setDeliveryTime($data['deliveryTime']);
        }

        if (isset($data['completionTime']) && $data['completionTime'] instanceof \DateTimeImmutable) {
            $order->setCompletionTime($data['completionTime']);
        }

        if (isset($data['waybillCode']) && \is_string($data['waybillCode'])) {
            $order->setWaybillCode($data['waybillCode']);
        }
    }

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
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createOrder(array $data = []): Order
    {
        $order = new Order();

        $this->setOrderBasicFields($order, $data);
        $this->setOrderReceiverFields($order, $data);
        $this->setOrderPriceFields($order, $data);
        $this->setOrderOptionalFields($order, $data);

        // 设置为已同步，避免触发同步到京东的逻辑
        $order->setSynced(true);

        $persistedOrder = $this->persistAndFlush($order);
        $this->assertInstanceOf(Order::class, $persistedOrder);

        return $persistedOrder;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(OrderRepository::class, $this->repository);
    }

    public function testFindByOrderId(): void
    {
        $order = $this->createOrder(['orderId' => '123456']);

        $result = $this->repository->findByOrderId('123456');
        $this->assertNotNull($result);
        $this->assertSame($order->getId(), $result->getId());
        $this->assertSame('123456', $result->getOrderId());
    }

    public function testFindByOrderIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByOrderId('999999');
        $this->assertNull($result);
    }

    public function testFindByAccountId(): void
    {
        // 创建一个订单
        $order = $this->createOrder(['orderTime' => new \DateTimeImmutable('now')]);
        $this->assertNotNull($order);
        $this->assertSame($this->testAccount->getId(), $order->getAccount()->getId());

        // 创建另一个账户
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        // 为另一个账户创建订单
        $otherOrder = $this->createOrder(['account' => $otherAccount]);
        $this->assertNotNull($otherOrder);
        $this->assertSame($otherAccount->getId(), $otherOrder->getAccount()->getId());

        // 查找测试账户的订单
        $result = $this->repository->findByAccountId($this->testAccount->getId());

        // 验证：应该找到1个订单
        $this->assertCount(1, $result);
        $this->assertSame($order->getId(), $result[0]->getId());

        // 查找另一个账户的订单
        $otherResult = $this->repository->findByAccountId($otherAccount->getId());
        $this->assertCount(1, $otherResult);
        $this->assertSame($otherOrder->getId(), $otherResult[0]->getId());
    }

    public function testSaveShouldPersistOrder(): void
    {
        $order = new Order();
        $order->setAccount($this->testAccount);
        $order->setOrderId('SAVE123456');
        $order->setOrderState('WAIT_PAY');
        $order->setPaymentState('WAIT_PAY');
        $order->setLogisticsState('WAIT_SHIP');
        $order->setReceiverName('Save Test Receiver');
        $order->setReceiverMobile('13900139000');
        $order->setReceiverProvince('上海市');
        $order->setReceiverCity('上海市');
        $order->setReceiverCounty('浦东新区');
        $order->setReceiverAddress('保存测试地址');
        $order->setOrderTotalPrice('500.00');
        $order->setOrderPaymentPrice('500.00');
        $order->setFreightPrice('10.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true);

        $this->repository->save($order);

        $this->assertNotNull($order->getId());
        $this->assertSame('SAVE123456', $order->getOrderId());
        $this->assertSame('WAIT_PAY', $order->getOrderState());
        $this->assertSame('Save Test Receiver', $order->getReceiverName());
        $this->assertSame('500.00', $order->getOrderTotalPrice());
    }

    public function testRemoveShouldDeleteOrder(): void
    {
        $order = $this->createOrder(['orderId' => 'TO_BE_DELETED']);
        $orderId = $order->getId();

        $this->repository->remove($order);

        $deletedOrder = $this->repository->find($orderId);
        $this->assertNull($deletedOrder);
    }

    public function testFindShouldReturnOrderById(): void
    {
        $order = $this->createOrder(['orderId' => 'FIND_TEST_ORDER']);

        $foundOrder = $this->repository->find($order->getId());

        $this->assertNotNull($foundOrder);
        $this->assertSame($order->getId(), $foundOrder->getId());
        $this->assertSame('FIND_TEST_ORDER', $foundOrder->getOrderId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllOrders(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createOrder(['orderId' => 'ORDER001']);
        $this->createOrder(['orderId' => 'ORDER002']);
        $this->createOrder(['orderId' => 'ORDER003']);

        $allOrders = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allOrders);
        foreach ($allOrders as $order) {
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function testFindByShouldReturnMatchingOrders(): void
    {
        $this->createOrder(['orderState' => 'WAIT_PAY']);
        $this->createOrder(['orderState' => 'WAIT_PAY']);
        $this->createOrder(['orderState' => 'FINISHED']);

        $waitPayOrders = $this->repository->findBy(['orderState' => 'WAIT_PAY']);

        $this->assertCount(2, $waitPayOrders);
        foreach ($waitPayOrders as $order) {
            $this->assertSame('WAIT_PAY', $order->getOrderState());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createOrder(['orderId' => "ORDER{$i}"]);
        }

        $limitedOrders = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedOrders);
    }

    public function testFindOneByShouldReturnSingleOrder(): void
    {
        $this->createOrder(['orderId' => 'REGULAR_ORDER']);
        $specificOrder = $this->createOrder(['orderId' => 'SPECIFIC_ORDER']);

        $foundOrder = $this->repository->findOneBy(['orderId' => 'SPECIFIC_ORDER']);

        $this->assertNotNull($foundOrder);
        $this->assertSame($specificOrder->getId(), $foundOrder->getId());
        $this->assertSame('SPECIFIC_ORDER', $foundOrder->getOrderId());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createOrder(['orderId' => 'SOME_ORDER']);

        $result = $this->repository->findOneBy(['orderId' => 'NON_EXISTENT_ORDER']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createOrder();
        $this->createOrder();
        $this->createOrder();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $this->createOrder(['orderState' => 'WAIT_PAY']);
        $this->createOrder(['orderState' => 'WAIT_PAY']);
        $this->createOrder(['orderState' => 'FINISHED']);

        $waitPayCount = $this->repository->count(['orderState' => 'WAIT_PAY']);

        $this->assertSame(2, $waitPayCount);
    }

    public function testSaveShouldHandleOptionalFields(): void
    {
        $order = new Order();
        $order->setAccount($this->testAccount);
        $order->setOrderId('OPTIONAL_FIELDS');
        $order->setOrderState('WAIT_GOODS_RECEIVE_CONFIRM');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setReceiverName('Optional Receiver');
        $order->setReceiverMobile('13700137000');
        $order->setReceiverProvince('广东省');
        $order->setReceiverCity('深圳市');
        $order->setReceiverCounty('南山区');
        $order->setReceiverAddress('可选字段测试地址');
        $order->setOrderTotalPrice('800.00');
        $order->setOrderPaymentPrice('800.00');
        $order->setFreightPrice('0.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setPaymentTime(new \DateTimeImmutable());
        $order->setWaybillCode('TEST_WAYBILL');
        $order->setSynced(true);

        $this->repository->save($order);

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getPaymentTime());
        $this->assertSame('TEST_WAYBILL', $order->getWaybillCode());
        $this->assertTrue($order->isSynced());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $order = new Order();
        $order->setAccount($this->testAccount);
        $order->setOrderId('NOT_PERSISTED');
        $order->setOrderState('WAIT_PAY');
        $order->setPaymentState('WAIT_PAY');
        $order->setLogisticsState('WAIT_SHIP');
        $order->setReceiverName('Not Persisted');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京市');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('未持久化地址');
        $order->setOrderTotalPrice('100.00');
        $order->setOrderPaymentPrice('100.00');
        $order->setFreightPrice('0.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true);

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($order);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $order1 = $this->createOrder(['orderState' => 'WAIT_PAY', 'orderTime' => new \DateTimeImmutable('-1 hour')]);
        $order2 = $this->createOrder(['orderState' => 'WAIT_PAY', 'orderTime' => new \DateTimeImmutable('now')]);

        $latestOrder = $this->repository->findOneBy(['orderState' => 'WAIT_PAY'], ['orderTime' => 'DESC']);
        $this->assertNotNull($latestOrder);
        $this->assertSame($order2->getId(), $latestOrder->getId());

        $earliestOrder = $this->repository->findOneBy(['orderState' => 'WAIT_PAY'], ['orderTime' => 'ASC']);
        $this->assertNotNull($earliestOrder);
        $this->assertSame($order1->getId(), $earliestOrder->getId());
    }

    public function testCountWithAssociationCriteria(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-key');
        $otherAccount->setAppSecret('other-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createOrder(['account' => $this->testAccount]);
        $this->createOrder(['account' => $this->testAccount]);
        $this->createOrder(['account' => $otherAccount]);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame(2, $count);

        $otherCount = $this->repository->count(['account' => $otherAccount]);
        $this->assertSame(1, $otherCount);
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-key-2');
        $otherAccount->setAppSecret('other-secret-2');
        $otherAccount->setName('Other Account 2');
        $this->persistAndFlush($otherAccount);

        $order1 = $this->createOrder(['account' => $this->testAccount, 'orderId' => 'ASSOC_1']);
        $order2 = $this->createOrder(['account' => $this->testAccount, 'orderId' => 'ASSOC_2']);
        $this->createOrder(['account' => $otherAccount, 'orderId' => 'ASSOC_3']);

        $orders = $this->repository->findBy(['account' => $this->testAccount]);
        $this->assertCount(2, $orders);
        $orderIds = array_map(fn ($order) => $order->getId(), $orders);
        $this->assertContains($order1->getId(), $orderIds);
        $this->assertContains($order2->getId(), $orderIds);
    }

    public function testFindByWithNullableFieldsIsNull(): void
    {
        $testState = 'NULL_FIELD_TEST_STATE';
        $order1 = $this->createOrder(['orderId' => 'NULL_TEST_1', 'orderState' => $testState]); // paymentTime, deliveryTime, completionTime 为 null
        $order2 = $this->createOrder(['orderId' => 'NULL_TEST_2', 'orderState' => $testState, 'paymentTime' => new \DateTimeImmutable()]);
        $order3 = $this->createOrder(['orderId' => 'NULL_TEST_3', 'orderState' => $testState, 'deliveryTime' => new \DateTimeImmutable()]);

        $ordersWithNullPaymentTime = $this->repository->findBy(['orderState' => $testState, 'paymentTime' => null]);
        $this->assertCount(2, $ordersWithNullPaymentTime);

        $ordersWithNullDeliveryTime = $this->repository->findBy(['orderState' => $testState, 'deliveryTime' => null]);
        $this->assertCount(2, $ordersWithNullDeliveryTime);

        $ordersWithNullWaybillCode = $this->repository->findBy(['orderState' => $testState, 'waybillCode' => null]);
        $this->assertCount(3, $ordersWithNullWaybillCode);
    }

    public function testCountWithNullableFieldsIsNull(): void
    {
        $testState = 'COUNT_NULL_TEST_STATE';
        $this->createOrder(['orderId' => 'COUNT_NULL_1', 'orderState' => $testState]); // paymentTime, deliveryTime, completionTime 为 null
        $this->createOrder(['orderId' => 'COUNT_NULL_2', 'orderState' => $testState, 'paymentTime' => new \DateTimeImmutable()]);
        $this->createOrder(['orderId' => 'COUNT_NULL_3', 'orderState' => $testState, 'waybillCode' => 'TEST_WAYBILL']);

        $countNullPaymentTime = $this->repository->count(['orderState' => $testState, 'paymentTime' => null]);
        $this->assertSame(2, $countNullPaymentTime);

        $countNullWaybillCode = $this->repository->count(['orderState' => $testState, 'waybillCode' => null]);
        $this->assertSame(2, $countNullWaybillCode);

        $countNullCompletionTime = $this->repository->count(['orderState' => $testState, 'completionTime' => null]);
        $this->assertSame(3, $countNullCompletionTime);
    }

    protected function getRepository(): OrderRepository
    {
        return self::getService(OrderRepository::class);
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
        // 设置为已同步，避免触发同步到京东的逻辑
        $order->setSynced(true);

        return $order;
    }
}
