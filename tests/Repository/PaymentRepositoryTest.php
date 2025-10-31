<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\Payment;
use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use JingdongCloudTradeBundle\Repository\PaymentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PaymentRepository::class)]
#[RunTestsInSeparateProcesses]
final class PaymentRepositoryTest extends AbstractRepositoryTestCase
{
    private PaymentRepository $repository;

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
        $this->testOrder->setOrderState('WAIT_PAY');
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
        $this->testOrder->setSynced(true); // 设置为已同步，避免触发同步到京东的逻辑
        $this->persistAndFlush($this->testOrder);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createPayment(array $data = []): Payment
    {
        $payment = new Payment();

        $order = $data['order'] ?? $this->testOrder;
        $payment->setOrder($order instanceof Order ? $order : $this->testOrder);

        $paymentId = $data['paymentId'] ?? 'PAY' . uniqid();
        $payment->setPaymentId(\is_string($paymentId) ? $paymentId : 'PAY' . uniqid());

        $paymentMethod = $data['paymentMethod'] ?? PaymentMethodEnum::ONLINE;
        $payment->setPaymentMethod($paymentMethod instanceof PaymentMethodEnum ? $paymentMethod : PaymentMethodEnum::ONLINE);

        $paymentState = $data['paymentState'] ?? PaymentStateEnum::PENDING;
        $payment->setPaymentState($paymentState instanceof PaymentStateEnum ? $paymentState : PaymentStateEnum::PENDING);

        $amount = $data['amount'] ?? '1000.00';
        $payment->setPaymentAmount(\is_string($amount) ? $amount : '1000.00');

        $paymentTime = $data['paymentTime'] ?? new \DateTimeImmutable();
        $payment->setPaymentTime($paymentTime instanceof \DateTimeImmutable ? $paymentTime : new \DateTimeImmutable());

        if (isset($data['paymentChannel']) && $data['paymentChannel'] instanceof PaymentChannelEnum) {
            $payment->setPaymentChannel($data['paymentChannel']);
        }

        if (isset($data['paymentNote']) && \is_string($data['paymentNote'])) {
            $payment->setPaymentNote($data['paymentNote']);
        }

        $persistedPayment = $this->persistAndFlush($payment);
        $this->assertInstanceOf(Payment::class, $persistedPayment);

        return $persistedPayment;
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(PaymentRepository::class, $this->repository);
    }

    public function testFindByOrderId(): void
    {
        $payment1 = $this->createPayment();
        $payment2 = $this->createPayment();

        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('789012');
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
        $otherOrder->setSynced(true); // 设置为已同步，避免触发同步到京东的逻辑
        $this->persistAndFlush($otherOrder);

        $this->createPayment(['order' => $otherOrder]);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $paymentIds = array_map(fn ($payment) => $payment->getId(), $result);
        $this->assertContains($payment1->getId(), $paymentIds);
        $this->assertContains($payment2->getId(), $paymentIds);
    }

    public function testFindByPaymentId(): void
    {
        $payment = $this->createPayment(['paymentId' => 'PAY123456']);

        $result = $this->repository->findByPaymentId('PAY123456');
        $this->assertNotNull($result);
        $this->assertSame($payment->getId(), $result->getId());
        $this->assertSame('PAY123456', $result->getPaymentId());
    }

    public function testFindByPaymentIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByPaymentId('NONEXISTENT');
        $this->assertNull($result);
    }

    public function testFindByDateRange(): void
    {
        $payment1 = $this->createPayment(['paymentTime' => new \DateTimeImmutable('2024-01-15')]);
        $payment2 = $this->createPayment(['paymentTime' => new \DateTimeImmutable('2024-06-15')]);
        $this->createPayment(['paymentTime' => new \DateTimeImmutable('2023-12-31')]);
        $this->createPayment(['paymentTime' => new \DateTimeImmutable('2025-01-01')]);

        $result = $this->repository->findByDateRange(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31')
        );

        $this->assertCount(2, $result);
        $paymentIds = array_map(fn ($payment) => $payment->getId(), $result);
        $this->assertContains($payment1->getId(), $paymentIds);
        $this->assertContains($payment2->getId(), $paymentIds);
    }

    public function testSaveShouldPersistPayment(): void
    {
        $payment = new Payment();
        $payment->setOrder($this->testOrder);
        $payment->setPaymentId('SAVE123456');
        $payment->setPaymentMethod(PaymentMethodEnum::ONLINE);
        $payment->setPaymentState(PaymentStateEnum::PAID);
        $payment->setPaymentAmount('500.00');
        $payment->setPaymentTime(new \DateTimeImmutable());
        $payment->setPaymentChannel(PaymentChannelEnum::ALIPAY);
        $payment->setPaymentNote('保存测试支付');

        $this->repository->save($payment);

        $this->assertNotNull($payment->getId());
        $this->assertSame('SAVE123456', $payment->getPaymentId());
        $this->assertSame(PaymentMethodEnum::ONLINE, $payment->getPaymentMethod());
        $this->assertSame(PaymentStateEnum::PAID, $payment->getPaymentState());
        $this->assertSame('500.00', $payment->getPaymentAmount());
        $this->assertSame(PaymentChannelEnum::ALIPAY, $payment->getPaymentChannel());
    }

    public function testRemoveShouldDeletePayment(): void
    {
        $payment = $this->createPayment(['paymentId' => 'TO_BE_DELETED']);
        $paymentId = $payment->getId();

        $this->repository->remove($payment);

        $deletedPayment = $this->repository->find($paymentId);
        $this->assertNull($deletedPayment);
    }

    public function testFindShouldReturnPaymentById(): void
    {
        $payment = $this->createPayment(['paymentId' => 'FIND_TEST_PAYMENT']);

        $foundPayment = $this->repository->find($payment->getId());

        $this->assertNotNull($foundPayment);
        $this->assertSame($payment->getId(), $foundPayment->getId());
        $this->assertSame('FIND_TEST_PAYMENT', $foundPayment->getPaymentId());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllPayments(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createPayment(['paymentId' => 'PAYMENT001']);
        $this->createPayment(['paymentId' => 'PAYMENT002']);
        $this->createPayment(['paymentId' => 'PAYMENT003']);

        $allPayments = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allPayments);
        foreach ($allPayments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
        }
    }

    public function testFindByShouldReturnMatchingPayments(): void
    {
        $testId = 'MATCH_TEST_' . uniqid();
        $this->createPayment(['paymentId' => $testId . '_1', 'paymentState' => PaymentStateEnum::PENDING]);
        $this->createPayment(['paymentId' => $testId . '_2', 'paymentState' => PaymentStateEnum::PENDING]);
        $this->createPayment(['paymentId' => $testId . '_3', 'paymentState' => PaymentStateEnum::PAID]);

        $pendingPayments = $this->repository->findBy(['paymentState' => PaymentStateEnum::PENDING]);

        $matchingCount = 0;
        foreach ($pendingPayments as $payment) {
            if (str_starts_with($payment->getPaymentId(), $testId)) {
                ++$matchingCount;
                $this->assertSame(PaymentStateEnum::PENDING, $payment->getPaymentState());
            }
        }
        $this->assertSame(2, $matchingCount);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createPayment(['paymentId' => "PAYMENT{$i}"]);
        }

        $limitedPayments = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedPayments);
    }

    public function testFindOneByShouldReturnSinglePayment(): void
    {
        $this->createPayment(['paymentId' => 'REGULAR_PAYMENT']);
        $specificPayment = $this->createPayment(['paymentId' => 'SPECIFIC_PAYMENT']);

        $foundPayment = $this->repository->findOneBy(['paymentId' => 'SPECIFIC_PAYMENT']);

        $this->assertNotNull($foundPayment);
        $this->assertSame($specificPayment->getId(), $foundPayment->getId());
        $this->assertSame('SPECIFIC_PAYMENT', $foundPayment->getPaymentId());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createPayment(['paymentId' => 'SOME_PAYMENT']);

        $result = $this->repository->findOneBy(['paymentId' => 'NON_EXISTENT_PAYMENT']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);
        $this->createPayment();
        $this->createPayment();
        $this->createPayment();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $this->createPayment(['paymentState' => PaymentStateEnum::PAID]);
        $this->createPayment(['paymentState' => PaymentStateEnum::PAID]);
        $this->createPayment(['paymentState' => PaymentStateEnum::FAILED]);

        $paidCount = $this->repository->count(['paymentState' => PaymentStateEnum::PAID]);

        $this->assertSame(2, $paidCount);
    }

    public function testSaveShouldHandleOptionalFields(): void
    {
        $payment = new Payment();
        $payment->setOrder($this->testOrder);
        $payment->setPaymentId('OPTIONAL_FIELDS');
        $payment->setPaymentMethod(PaymentMethodEnum::COD);
        $payment->setPaymentState(PaymentStateEnum::PAID);
        $payment->setPaymentAmount('800.00');
        $payment->setPaymentTime(new \DateTimeImmutable());

        $this->repository->save($payment);

        $this->assertNotNull($payment->getId());
        $this->assertSame(PaymentMethodEnum::COD, $payment->getPaymentMethod());
        $this->assertNull($payment->getPaymentChannel());
        $this->assertNull($payment->getPaymentNote());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $payment = new Payment();
        $payment->setOrder($this->testOrder);
        $payment->setPaymentId('NOT_PERSISTED');
        $payment->setPaymentMethod(PaymentMethodEnum::ONLINE);
        $payment->setPaymentState(PaymentStateEnum::PENDING);
        $payment->setPaymentAmount('100.00');
        $payment->setPaymentTime(new \DateTimeImmutable());

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($payment);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $payment1 = $this->createPayment(['paymentState' => PaymentStateEnum::PAID, 'paymentTime' => new \DateTimeImmutable('-1 hour')]);
        $payment2 = $this->createPayment(['paymentState' => PaymentStateEnum::PAID, 'paymentTime' => new \DateTimeImmutable('now')]);

        $latestPayment = $this->repository->findOneBy(['paymentState' => PaymentStateEnum::PAID], ['paymentTime' => 'DESC']);
        $this->assertNotNull($latestPayment);
        $this->assertSame($payment2->getId(), $latestPayment->getId());

        $earliestPayment = $this->repository->findOneBy(['paymentState' => PaymentStateEnum::PAID], ['paymentTime' => 'ASC']);
        $this->assertNotNull($earliestPayment);
        $this->assertSame($payment1->getId(), $earliestPayment->getId());
    }

    public function testCountWithAssociationCriteria(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('OTHER_ORDER_789');
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

        $this->createPayment(['order' => $this->testOrder]);
        $this->createPayment(['order' => $this->testOrder]);
        $this->createPayment(['order' => $otherOrder]);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame(2, $count);

        $otherCount = $this->repository->count(['order' => $otherOrder]);
        $this->assertSame(1, $otherCount);
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('ASSOC_ORDER_456');
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

        $payment1 = $this->createPayment(['order' => $this->testOrder, 'paymentId' => 'ASSOC_PAY_1']);
        $payment2 = $this->createPayment(['order' => $this->testOrder, 'paymentId' => 'ASSOC_PAY_2']);
        $this->createPayment(['order' => $otherOrder, 'paymentId' => 'ASSOC_PAY_3']);

        $payments = $this->repository->findBy(['order' => $this->testOrder]);
        $this->assertCount(2, $payments);
        $paymentIds = array_map(fn ($payment) => $payment->getId(), $payments);
        $this->assertContains($payment1->getId(), $paymentIds);
        $this->assertContains($payment2->getId(), $paymentIds);
    }

    public function testFindByWithNullableFieldsIsNull(): void
    {
        $initialNullChannelCount = count($this->repository->findBy(['paymentChannel' => null]));
        $initialNullNoteCount = count($this->repository->findBy(['paymentNote' => null]));

        $this->createPayment(['paymentId' => 'NULL_TEST_1']); // paymentChannel, paymentNote 为 null
        $this->createPayment(['paymentId' => 'NULL_TEST_2', 'paymentChannel' => PaymentChannelEnum::WECHAT]);
        $this->createPayment(['paymentId' => 'NULL_TEST_3', 'paymentNote' => 'Test Note']);

        $paymentsWithNullChannel = $this->repository->findBy(['paymentChannel' => null]);
        $this->assertCount($initialNullChannelCount + 2, $paymentsWithNullChannel);

        $paymentsWithNullNote = $this->repository->findBy(['paymentNote' => null]);
        $this->assertCount($initialNullNoteCount + 2, $paymentsWithNullNote);
    }

    public function testCountWithNullableFieldsIsNull(): void
    {
        $initialNullChannelCount = $this->repository->count(['paymentChannel' => null]);
        $initialNullNoteCount = $this->repository->count(['paymentNote' => null]);

        $this->createPayment(['paymentId' => 'COUNT_NULL_1']); // paymentChannel, paymentNote 为 null
        $this->createPayment(['paymentId' => 'COUNT_NULL_2', 'paymentChannel' => PaymentChannelEnum::ALIPAY]);
        $this->createPayment(['paymentId' => 'COUNT_NULL_3', 'paymentNote' => 'Another Note']);

        $countNullChannel = $this->repository->count(['paymentChannel' => null]);
        $this->assertSame($initialNullChannelCount + 2, $countNullChannel);

        $countNullNote = $this->repository->count(['paymentNote' => null]);
        $this->assertSame($initialNullNoteCount + 2, $countNullNote);
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

        $this->createPayment(['paymentId' => 'test-order-payment']);

        $paymentWithOtherOrder = new Payment();
        $paymentWithOtherOrder->setOrder($otherOrder);
        $paymentWithOtherOrder->setPaymentId('other-order-payment');
        $paymentWithOtherOrder->setPaymentMethod(PaymentMethodEnum::ONLINE);
        $paymentWithOtherOrder->setPaymentState(PaymentStateEnum::PAID);
        $paymentWithOtherOrder->setPaymentAmount('2000.00');
        $paymentWithOtherOrder->setPaymentTime(new \DateTimeImmutable());
        $this->persistAndFlush($paymentWithOtherOrder);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Payment::class, $result);
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

        $this->createPayment(['paymentId' => 'test-order-1']);
        $this->createPayment(['paymentId' => 'test-order-2']);

        $paymentWithOtherOrder = new Payment();
        $paymentWithOtherOrder->setOrder($otherOrder);
        $paymentWithOtherOrder->setPaymentId('other-order-payment-2');
        $paymentWithOtherOrder->setPaymentMethod(PaymentMethodEnum::ONLINE);
        $paymentWithOtherOrder->setPaymentState(PaymentStateEnum::PAID);
        $paymentWithOtherOrder->setPaymentAmount('3000.00');
        $paymentWithOtherOrder->setPaymentTime(new \DateTimeImmutable());
        $this->persistAndFlush($paymentWithOtherOrder);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function createNewEntity(): object
    {
        $entity = new Payment();

        // 设置基本字段
        $entity->setOrder($this->testOrder);
        $entity->setPaymentId('TEST_' . uniqid());
        $entity->setPaymentMethod(PaymentMethodEnum::ONLINE);
        $entity->setPaymentAmount('100.00');
        $entity->setPaymentTime(new \DateTimeImmutable());
        $entity->setPaymentState(PaymentStateEnum::PAID);

        return $entity;
    }

    protected function getRepository(): PaymentRepository
    {
        return self::getService(PaymentRepository::class);
    }
}
