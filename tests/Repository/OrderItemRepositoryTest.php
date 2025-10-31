<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use JingdongCloudTradeBundle\Repository\OrderItemRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(OrderItemRepository::class)]
#[RunTestsInSeparateProcesses]
final class OrderItemRepositoryTest extends AbstractRepositoryTestCase
{
    private OrderItemRepository $repository;

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
        $this->testOrder->setOrderState('FINISHED');
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
    private function createOrderItem(array $data = []): OrderItem
    {
        $orderItem = new OrderItem();
        $orderItem->setAccount($this->testAccount);

        if (\array_key_exists('order', $data)) {
            $order = $data['order'];
            if ($order instanceof Order || null === $order) {
                $orderItem->setOrder($order);
            } else {
                $orderItem->setOrder($this->testOrder);
            }
        } else {
            $orderItem->setOrder($this->testOrder);
        }

        $skuId = $data['skuId'] ?? 'SKU' . uniqid();
        $orderItem->setSkuId(\is_string($skuId) ? $skuId : 'SKU' . uniqid());

        $skuName = $data['skuName'] ?? 'Test Product';
        $orderItem->setSkuName(\is_string($skuName) ? $skuName : 'Test Product');

        $quantity = $data['quantity'] ?? 1;
        $orderItem->setQuantity(\is_int($quantity) ? $quantity : 1);

        $price = $data['price'] ?? '100.00';
        $orderItem->setPrice(\is_string($price) ? $price : '100.00');

        $totalPrice = $data['totalPrice'] ?? '100.00';
        $orderItem->setTotalPrice(\is_string($totalPrice) ? $totalPrice : '100.00');

        $persistedOrderItem = $this->persistAndFlush($orderItem);
        $this->assertInstanceOf(OrderItem::class, $persistedOrderItem);

        return $persistedOrderItem;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(OrderItemRepository::class, $this->repository);
    }

    public function testFindByOrderId(): void
    {
        $orderItem1 = $this->createOrderItem(['skuId' => 'SKU001']);
        $orderItem2 = $this->createOrderItem(['skuId' => 'SKU002']);

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

        $this->createOrderItem(['order' => $otherOrder, 'skuId' => 'SKU003']);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $orderItemIds = array_map(fn ($item) => $item->getId(), $result);
        $this->assertContains($orderItem1->getId(), $orderItemIds);
        $this->assertContains($orderItem2->getId(), $orderItemIds);
    }

    public function testFindBySkuId(): void
    {
        $targetSkuId = 'SKU456';
        $orderItem1 = $this->createOrderItem(['skuId' => $targetSkuId]);

        $order2 = new Order();
        $order2->setAccount($this->testAccount);
        $order2->setOrderId('789012');
        $order2->setOrderState('FINISHED');
        $order2->setPaymentState('PAID');
        $order2->setLogisticsState('SHIPPED');
        $order2->setReceiverName('Test Receiver');
        $order2->setReceiverMobile('13800138000');
        $order2->setReceiverProvince('北京市');
        $order2->setReceiverCity('北京市');
        $order2->setReceiverCounty('朝阳区');
        $order2->setReceiverAddress('测试地址');
        $order2->setOrderTotalPrice('200.00');
        $order2->setOrderPaymentPrice('200.00');
        $order2->setFreightPrice('0.00');
        $order2->setOrderTime(new \DateTimeImmutable());
        $order2->setSynced(true);
        $this->persistAndFlush($order2);

        $orderItem2 = $this->createOrderItem(['order' => $order2, 'skuId' => $targetSkuId]);
        $this->createOrderItem(['skuId' => 'SKU789']);

        $result = $this->repository->findBySkuId($targetSkuId);

        $this->assertCount(2, $result);
        $orderItemIds = array_map(fn ($item) => $item->getId(), $result);
        $this->assertContains($orderItem1->getId(), $orderItemIds);
        $this->assertContains($orderItem2->getId(), $orderItemIds);
    }

    public function testSaveShouldPersistOrderItem(): void
    {
        $orderItem = new OrderItem();
        $orderItem->setAccount($this->testAccount);
        $orderItem->setOrder($this->testOrder);
        $orderItem->setSkuId('SKU789');
        $orderItem->setSkuName('Test Product Save');
        $orderItem->setQuantity(2);
        $orderItem->setPrice('150.00');
        $orderItem->setTotalPrice('300.00');

        $this->repository->save($orderItem);

        $this->assertNotNull($orderItem->getId());
        $this->assertSame('SKU789', $orderItem->getSkuId());
        $this->assertSame('Test Product Save', $orderItem->getSkuName());
        $this->assertSame(2, $orderItem->getQuantity());
        $this->assertSame('150.00', $orderItem->getPrice());
        $this->assertSame('300.00', $orderItem->getTotalPrice());
    }

    public function testRemoveShouldDeleteOrderItem(): void
    {
        $orderItem = $this->createOrderItem(['skuName' => 'To Be Deleted']);
        $orderItemId = $orderItem->getId();

        $this->repository->remove($orderItem);

        $deletedOrderItem = $this->repository->find($orderItemId);
        $this->assertNull($deletedOrderItem);
    }

    public function testFindShouldReturnOrderItemById(): void
    {
        $orderItem = $this->createOrderItem(['skuName' => 'Find Test Product']);

        $foundOrderItem = $this->repository->find($orderItem->getId());

        $this->assertNotNull($foundOrderItem);
        $this->assertSame($orderItem->getId(), $foundOrderItem->getId());
        $this->assertSame('Find Test Product', $foundOrderItem->getSkuName());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllOrderItems(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createOrderItem(['skuName' => 'Item 1']);
        $this->createOrderItem(['skuName' => 'Item 2']);
        $this->createOrderItem(['skuName' => 'Item 3']);

        $allOrderItems = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allOrderItems);
        foreach ($allOrderItems as $orderItem) {
            $this->assertInstanceOf(OrderItem::class, $orderItem);
        }
    }

    public function testFindByShouldReturnMatchingOrderItems(): void
    {
        $uniqueSkuPrefix = 'FIND_BY_TEST_' . uniqid();
        $this->createOrderItem(['quantity' => 1, 'skuId' => $uniqueSkuPrefix . '_1']);
        $this->createOrderItem(['quantity' => 1, 'skuId' => $uniqueSkuPrefix . '_2']);
        $this->createOrderItem(['quantity' => 2, 'skuId' => $uniqueSkuPrefix . '_3']);

        $singleQuantityItems = $this->repository->findBy([
            'quantity' => 1,
            'order' => $this->testOrder,
        ]);

        $this->assertCount(2, $singleQuantityItems);
        foreach ($singleQuantityItems as $orderItem) {
            $this->assertSame(1, $orderItem->getQuantity());
            $this->assertSame($this->testOrder, $orderItem->getOrder());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createOrderItem(['skuName' => "Product {$i}"]);
        }

        $limitedOrderItems = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedOrderItems);
    }

    public function testFindOneByShouldReturnSingleOrderItem(): void
    {
        $this->createOrderItem(['skuId' => 'REGULAR001']);
        $specificOrderItem = $this->createOrderItem(['skuId' => 'SPECIFIC001']);

        $foundOrderItem = $this->repository->findOneBy(['skuId' => 'SPECIFIC001']);

        $this->assertNotNull($foundOrderItem);
        $this->assertSame($specificOrderItem->getId(), $foundOrderItem->getId());
        $this->assertSame('SPECIFIC001', $foundOrderItem->getSkuId());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createOrderItem(['skuId' => 'SOME_SKU']);

        $result = $this->repository->findOneBy(['skuId' => 'NON_EXISTENT_SKU']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createOrderItem();
        $this->createOrderItem();
        $this->createOrderItem();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $initialCount = $this->repository->count(['quantity' => 1]);

        $this->createOrderItem(['quantity' => 1]);
        $this->createOrderItem(['quantity' => 1]);
        $this->createOrderItem(['quantity' => 2]);

        $singleQuantityCount = $this->repository->count(['quantity' => 1]);

        $this->assertSame($initialCount + 2, $singleQuantityCount);
    }

    public function testSaveShouldHandleNumericFields(): void
    {
        $orderItem = new OrderItem();
        $orderItem->setAccount($this->testAccount);
        $orderItem->setOrder($this->testOrder);
        $orderItem->setSkuId('SKU_NUMERIC');
        $orderItem->setSkuName('Numeric Test Product');
        $orderItem->setQuantity(5);
        $orderItem->setPrice('99.99');
        $orderItem->setTotalPrice('499.95');

        $this->repository->save($orderItem);

        $this->assertNotNull($orderItem->getId());
        $this->assertSame(5, $orderItem->getQuantity());
        $this->assertSame('99.99', $orderItem->getPrice());
        $this->assertSame('499.95', $orderItem->getTotalPrice());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $orderItem = new OrderItem();
        $orderItem->setAccount($this->testAccount);
        $orderItem->setOrder($this->testOrder);
        $orderItem->setSkuId('NOT_PERSISTED');
        $orderItem->setSkuName('Not Persisted Product');
        $orderItem->setQuantity(1);
        $orderItem->setPrice('100.00');
        $orderItem->setTotalPrice('100.00');

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($orderItem);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $orderItem1 = $this->createOrderItem(['quantity' => 5, 'skuId' => 'ORDER_BY_TEST_1']);
        $orderItem2 = $this->createOrderItem(['quantity' => 5, 'skuId' => 'ORDER_BY_TEST_2']);

        $result = $this->repository->findOneBy(['quantity' => 5], ['id' => 'DESC']);

        $this->assertInstanceOf(OrderItem::class, $result);
        $this->assertSame($orderItem2->getId(), $result->getId());
    }

    public function testCountWithAssociationCriteria(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-123');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('SHIPPED');
        $otherOrder->setReceiverName('Other Receiver');
        $otherOrder->setReceiverMobile('13900139000');
        $otherOrder->setReceiverProvince('上海市');
        $otherOrder->setReceiverCity('上海市');
        $otherOrder->setReceiverCounty('浦东新区');
        $otherOrder->setReceiverAddress('其他地址');
        $otherOrder->setOrderTotalPrice('500.00');
        $otherOrder->setOrderPaymentPrice('500.00');
        $otherOrder->setFreightPrice('0.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $this->createOrderItem(['order' => $this->testOrder]);
        $this->createOrderItem(['order' => $this->testOrder]);
        $this->createOrderItem(['order' => $otherOrder]);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame(2, $count);

        $otherCount = $this->repository->count(['order' => $otherOrder]);
        $this->assertSame(1, $otherCount);
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('assoc-order-456');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('SHIPPED');
        $otherOrder->setReceiverName('Assoc Receiver');
        $otherOrder->setReceiverMobile('13800138001');
        $otherOrder->setReceiverProvince('广东省');
        $otherOrder->setReceiverCity('深圳市');
        $otherOrder->setReceiverCounty('南山区');
        $otherOrder->setReceiverAddress('关联地址');
        $otherOrder->setOrderTotalPrice('300.00');
        $otherOrder->setOrderPaymentPrice('300.00');
        $otherOrder->setFreightPrice('0.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $orderItem1 = $this->createOrderItem(['order' => $this->testOrder, 'skuId' => 'ASSOC_ITEM_1']);
        $orderItem2 = $this->createOrderItem(['order' => $this->testOrder, 'skuId' => 'ASSOC_ITEM_2']);
        $this->createOrderItem(['order' => $otherOrder, 'skuId' => 'ASSOC_ITEM_3']);

        $orderItems = $this->repository->findBy(['order' => $this->testOrder]);
        $this->assertCount(2, $orderItems);
        $orderItemIds = array_map(fn ($item) => $item->getId(), $orderItems);
        $this->assertContains($orderItem1->getId(), $orderItemIds);
        $this->assertContains($orderItem2->getId(), $orderItemIds);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createOrderItem(['skuId' => 'test-account-item']);

        $orderItemWithOtherAccount = new OrderItem();
        $orderItemWithOtherAccount->setAccount($otherAccount);
        $orderItemWithOtherAccount->setOrder($this->testOrder);
        $orderItemWithOtherAccount->setSkuId('other-account-item');
        $orderItemWithOtherAccount->setSkuName('Other Account Product');
        $orderItemWithOtherAccount->setQuantity(1);
        $orderItemWithOtherAccount->setPrice('200.00');
        $orderItemWithOtherAccount->setTotalPrice('200.00');
        $this->persistAndFlush($orderItemWithOtherAccount);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(OrderItem::class, $result);
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

        $this->createOrderItem(['skuId' => 'test-account-1']);
        $this->createOrderItem(['skuId' => 'test-account-2']);

        $orderItemWithOtherAccount = new OrderItem();
        $orderItemWithOtherAccount->setAccount($otherAccount);
        $orderItemWithOtherAccount->setOrder($this->testOrder);
        $orderItemWithOtherAccount->setSkuId('other-account-item-2');
        $orderItemWithOtherAccount->setSkuName('Other Account Product 2');
        $orderItemWithOtherAccount->setQuantity(1);
        $orderItemWithOtherAccount->setPrice('300.00');
        $orderItemWithOtherAccount->setTotalPrice('300.00');
        $this->persistAndFlush($orderItemWithOtherAccount);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame($initialCount + 2, $count);
    }

    public function testFindOneByAssociationOrderShouldReturnMatchingEntity(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-789');
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

        $this->createOrderItem(['skuId' => 'test-order-item']);

        $orderItemWithOtherOrder = new OrderItem();
        $orderItemWithOtherOrder->setAccount($this->testAccount);
        $orderItemWithOtherOrder->setOrder($otherOrder);
        $orderItemWithOtherOrder->setSkuId('other-order-item');
        $orderItemWithOtherOrder->setSkuName('Other Order Product');
        $orderItemWithOtherOrder->setQuantity(1);
        $orderItemWithOtherOrder->setPrice('2000.00');
        $orderItemWithOtherOrder->setTotalPrice('2000.00');
        $this->persistAndFlush($orderItemWithOtherOrder);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(OrderItem::class, $result);
        $this->assertSame($this->testOrder->getId(), $result->getOrder()->getId());
    }

    public function testCountByAssociationOrderShouldReturnCorrectNumber(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-012');
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

        $this->createOrderItem(['skuId' => 'test-order-1']);
        $this->createOrderItem(['skuId' => 'test-order-2']);

        $orderItemWithOtherOrder = new OrderItem();
        $orderItemWithOtherOrder->setAccount($this->testAccount);
        $orderItemWithOtherOrder->setOrder($otherOrder);
        $orderItemWithOtherOrder->setSkuId('other-order-item-2');
        $orderItemWithOtherOrder->setSkuName('Other Order Product 2');
        $orderItemWithOtherOrder->setQuantity(1);
        $orderItemWithOtherOrder->setPrice('3000.00');
        $orderItemWithOtherOrder->setTotalPrice('3000.00');
        $this->persistAndFlush($orderItemWithOtherOrder);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function getRepository(): OrderItemRepository
    {
        return self::getService(OrderItemRepository::class);
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

        $orderItem = new OrderItem();
        $orderItem->setAccount($account);
        $orderItem->setOrder($order);
        $orderItem->setSkuId('sku-' . uniqid());
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(1);
        $orderItem->setPrice('199.00');
        $orderItem->setTotalPrice('199.00');

        return $orderItem;
    }
}
