<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use Doctrine\ORM\ORMInvalidArgumentException;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Invoice;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;
use JingdongCloudTradeBundle\Repository\InvoiceRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceRepository::class)]
#[RunTestsInSeparateProcesses]
final class InvoiceRepositoryTest extends AbstractRepositoryTestCase
{
    private InvoiceRepository $repository;

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
    private function createInvoice(array $data = []): Invoice
    {
        $invoice = new Invoice();

        $invoice->setAccount($this->getEntityValue($data, 'account', Account::class, $this->testAccount));
        $invoice->setOrder($this->getEntityValue($data, 'order', Order::class, $this->testOrder));
        $invoice->setInvoiceType($this->getEnumValue($data, 'invoiceType', InvoiceTypeEnum::class, InvoiceTypeEnum::NORMAL));
        $invoice->setTitle($this->getStringValue($data, 'title', 'Test Company'));
        $invoice->setInvoiceState($this->getEnumValue($data, 'invoiceState', InvoiceStateEnum::class, InvoiceStateEnum::PENDING));

        $this->setOptionalStringField($data, 'taxpayerIdentity', $invoice->setTaxpayerIdentity(...));
        $this->setOptionalStringField($data, 'registeredAddress', $invoice->setRegisteredAddress(...));
        $this->setOptionalStringField($data, 'registeredPhone', $invoice->setRegisteredPhone(...));
        $this->setOptionalStringField($data, 'bankName', $invoice->setBankName(...));
        $this->setOptionalStringField($data, 'bankAccount', $invoice->setBankAccount(...));
        $this->setOptionalStringField($data, 'invoiceCode', $invoice->setInvoiceCode(...));
        $this->setOptionalStringField($data, 'invoiceNumber', $invoice->setInvoiceNumber(...));
        $this->setOptionalStringField($data, 'invoiceAmount', $invoice->setInvoiceAmount(...));
        $this->setOptionalEnumField($data, 'invoiceContent', InvoiceContentEnum::class, $invoice->setInvoiceContent(...));

        $persistedInvoice = $this->persistAndFlush($invoice);
        $this->assertInstanceOf(Invoice::class, $persistedInvoice);

        return $persistedInvoice;
    }

    /**
     * @template T of object
     * @param array<string, mixed> $data
     * @param class-string<T> $expectedClass
     * @param T $default
     * @return T
     */
    private function getEntityValue(array $data, string $key, string $expectedClass, object $default): object
    {
        $value = $data[$key] ?? $default;

        return $value instanceof $expectedClass ? $value : $default;
    }

    /**
     * @template T of \BackedEnum
     * @param array<string, mixed> $data
     * @param class-string<T> $enumClass
     * @param T $default
     * @return T
     */
    private function getEnumValue(array $data, string $key, string $enumClass, \BackedEnum $default): \BackedEnum
    {
        $value = $data[$key] ?? $default;

        return $value instanceof $enumClass ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getStringValue(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;

        return \is_string($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(string|null): void $setter
     */
    private function setOptionalStringField(array $data, string $key, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            $setter(\is_string($value) ? $value : null);
        }
    }

    /**
     * @template T of \BackedEnum
     * @param array<string, mixed> $data
     * @param class-string<T> $enumClass
     * @param callable(T|null): void $setter
     */
    private function setOptionalEnumField(array $data, string $key, string $enumClass, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            $setter($value instanceof $enumClass ? $value : null);
        }
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(InvoiceRepository::class, $this->repository);
    }

    public function testFindByOrderId(): void
    {
        $invoice1 = $this->createInvoice();
        $invoice2 = $this->createInvoice();

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

        $this->createInvoice(['order' => $otherOrder]);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $invoiceIds = array_map(fn ($inv) => $inv->getId(), $result);
        $this->assertContains($invoice1->getId(), $invoiceIds);
        $this->assertContains($invoice2->getId(), $invoiceIds);
    }

    public function testFindByInvoiceCodeAndNumber(): void
    {
        $invoice = $this->createInvoice([
            'invoiceCode' => 'CODE123',
            'invoiceNumber' => 'INV2024001',
        ]);

        $result = $this->repository->findByInvoiceCodeAndNumber('CODE123', 'INV2024001');
        $this->assertNotNull($result);
        $this->assertSame($invoice->getId(), $result->getId());
    }

    public function testFindByInvoiceCodeAndNumberReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByInvoiceCodeAndNumber('NONEXISTENT', 'NONE');
        $this->assertNull($result);
    }

    public function testSaveShouldPersistInvoice(): void
    {
        $invoice = new Invoice();
        $invoice->setAccount($this->testAccount);
        $invoice->setOrder($this->testOrder);
        $invoice->setInvoiceType(InvoiceTypeEnum::NORMAL);
        $invoice->setTitle('Test Invoice');
        $invoice->setInvoiceState(InvoiceStateEnum::PENDING);

        $this->repository->save($invoice);

        $this->assertNotNull($invoice->getId());
        $this->assertSame('Test Invoice', $invoice->getTitle());
        $this->assertSame(InvoiceTypeEnum::NORMAL, $invoice->getInvoiceType());
        $this->assertSame(InvoiceStateEnum::PENDING, $invoice->getInvoiceState());
    }

    public function testRemoveShouldDeleteInvoice(): void
    {
        $invoice = $this->createInvoice(['title' => 'To Be Deleted']);
        $invoiceId = $invoice->getId();

        $this->repository->remove($invoice);

        $deletedInvoice = $this->repository->find($invoiceId);
        $this->assertNull($deletedInvoice);
    }

    public function testFindShouldReturnInvoiceById(): void
    {
        $invoice = $this->createInvoice(['title' => 'Find Test Invoice']);

        $foundInvoice = $this->repository->find($invoice->getId());

        $this->assertNotNull($foundInvoice);
        $this->assertSame($invoice->getId(), $foundInvoice->getId());
        $this->assertSame('Find Test Invoice', $foundInvoice->getTitle());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $result = $this->repository->find(999999);

        $this->assertNull($result);
    }

    public function testFindAllShouldReturnAllInvoices(): void
    {
        $initialCount = $this->repository->count([]);

        $this->createInvoice(['title' => 'Invoice 1']);
        $this->createInvoice(['title' => 'Invoice 2']);
        $this->createInvoice(['title' => 'Invoice 3']);

        $allInvoices = $this->repository->findAll();

        $this->assertCount($initialCount + 3, $allInvoices);
        foreach ($allInvoices as $invoice) {
            $this->assertInstanceOf(Invoice::class, $invoice);
        }
    }

    public function testFindByShouldReturnMatchingInvoices(): void
    {
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING]);
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING]);
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::ISSUED]);

        $pendingInvoices = $this->repository->findBy(['invoiceState' => InvoiceStateEnum::PENDING]);

        $this->assertCount(2, $pendingInvoices);
        foreach ($pendingInvoices as $invoice) {
            $this->assertSame(InvoiceStateEnum::PENDING, $invoice->getInvoiceState());
        }
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->createInvoice(['title' => "Invoice {$i}"]);
        }

        $limitedInvoices = $this->repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $limitedInvoices);
    }

    public function testFindOneByShouldReturnSingleInvoice(): void
    {
        $this->createInvoice(['title' => 'Regular Invoice']);
        $specificInvoice = $this->createInvoice(['title' => 'Specific Invoice']);

        $foundInvoice = $this->repository->findOneBy(['title' => 'Specific Invoice']);

        $this->assertNotNull($foundInvoice);
        $this->assertSame($specificInvoice->getId(), $foundInvoice->getId());
        $this->assertSame('Specific Invoice', $foundInvoice->getTitle());
    }

    public function testFindOneByShouldReturnNullWhenNoMatch(): void
    {
        $this->createInvoice(['title' => 'Some Invoice']);

        $result = $this->repository->findOneBy(['title' => 'Non-existent Invoice']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnTotalNumber(): void
    {
        $initialCount = $this->repository->count([]);

        $this->createInvoice();
        $this->createInvoice();
        $this->createInvoice();

        $count = $this->repository->count([]);

        $this->assertSame($initialCount + 3, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredNumber(): void
    {
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING]);
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING]);
        $this->createInvoice(['invoiceState' => InvoiceStateEnum::ISSUED]);

        $pendingCount = $this->repository->count(['invoiceState' => InvoiceStateEnum::PENDING]);

        $this->assertSame(2, $pendingCount);
    }

    public function testSaveShouldHandleNullValues(): void
    {
        $invoice = new Invoice();
        $invoice->setAccount($this->testAccount);
        $invoice->setOrder($this->testOrder);
        $invoice->setInvoiceType(InvoiceTypeEnum::NORMAL);
        $invoice->setTitle('Invoice with Nulls');
        $invoice->setInvoiceState(InvoiceStateEnum::PENDING);

        $this->repository->save($invoice);

        $this->assertNotNull($invoice->getId());
        $this->assertNull($invoice->getInvoiceCode());
        $this->assertNull($invoice->getInvoiceNumber());
        $this->assertNull($invoice->getInvoiceAmount());
    }

    public function testRemoveNonPersistedEntityShouldThrowException(): void
    {
        $invoice = new Invoice();
        $invoice->setAccount($this->testAccount);
        $invoice->setOrder($this->testOrder);
        $invoice->setInvoiceType(InvoiceTypeEnum::NORMAL);
        $invoice->setTitle('Not Persisted');
        $invoice->setInvoiceState(InvoiceStateEnum::PENDING);

        $this->expectException(ORMInvalidArgumentException::class);
        $this->repository->remove($invoice);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $oldInvoice = $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING, 'title' => 'AAA Old']);
        $newInvoice = $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING, 'title' => 'ZZZ New']);

        // 找到待开票状态中按标题排序最后的那个
        $result = $this->repository->findOneBy(['invoiceState' => InvoiceStateEnum::PENDING], ['title' => 'DESC']);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertSame($newInvoice->getId(), $result->getId());
    }

    public function testFindByWithNullCriteriaShouldFindInvoicesWithNullValues(): void
    {
        $invoiceWithCode = $this->createInvoice(['invoiceCode' => 'CODE123', 'title' => 'With Code']);
        $invoiceWithoutCode = $this->createInvoice(['title' => 'Without Code']); // 没有设置 invoiceCode

        $result = $this->repository->findBy(['invoiceCode' => null]);

        $this->assertIsArray($result);
        // 验证结果中包含没有 invoiceCode 的发票
        $resultIds = array_map(fn ($invoice) => $invoice->getId(), $result);
        $this->assertContains($invoiceWithoutCode->getId(), $resultIds);
        $this->assertNotContains($invoiceWithCode->getId(), $resultIds);
    }

    public function testFindByWithEnumCriteriaShouldReturnCorrectResults(): void
    {
        $pendingInvoice = $this->createInvoice(['invoiceState' => InvoiceStateEnum::PENDING, 'title' => 'Pending']);
        $issuedInvoice = $this->createInvoice(['invoiceState' => InvoiceStateEnum::ISSUED, 'title' => 'Issued']);

        $pendingResults = $this->repository->findBy(['invoiceState' => InvoiceStateEnum::PENDING]);
        $issuedResults = $this->repository->findBy(['invoiceState' => InvoiceStateEnum::ISSUED]);

        // 验证待开票结果
        $pendingIds = array_map(fn ($invoice) => $invoice->getId(), $pendingResults);
        $this->assertContains($pendingInvoice->getId(), $pendingIds);

        // 验证已开票结果
        $issuedIds = array_map(fn ($invoice) => $invoice->getId(), $issuedResults);
        $this->assertContains($issuedInvoice->getId(), $issuedIds);
    }

    public function testFindByWithStringFieldCriteriaShouldReturnCorrectResults(): void
    {
        $testInvoice1 = $this->createInvoice(['title' => 'Test Company', 'invoiceAmount' => '1000.00']);
        $testInvoice2 = $this->createInvoice(['title' => 'Test Company', 'invoiceAmount' => '2000.00']);
        $otherInvoice = $this->createInvoice(['title' => 'Other Company', 'invoiceAmount' => '1500.00']);

        $testResults = $this->repository->findBy(['title' => 'Test Company']);

        $this->assertIsArray($testResults);
        $this->assertGreaterThanOrEqual(2, count($testResults));

        $testIds = array_map(fn ($invoice) => $invoice->getId(), $testResults);
        $this->assertContains($testInvoice1->getId(), $testIds);
        $this->assertContains($testInvoice2->getId(), $testIds);
        $this->assertNotContains($otherInvoice->getId(), $testIds);
    }

    public function testFindByOrderIdShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createInvoice();

        $result = $this->repository->findByOrderId(99999);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByInvoiceCodeAndNumberShouldHandleNonExistentValues(): void
    {
        $this->createInvoice(['invoiceCode' => 'CODE123', 'invoiceNumber' => 'NUM123']);

        $result = $this->repository->findByInvoiceCodeAndNumber('NONEXISTENT', 'NUM123');
        $this->assertNull($result);

        $result = $this->repository->findByInvoiceCodeAndNumber('CODE123', 'NONEXISTENT');
        $this->assertNull($result);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createInvoice(['title' => 'Test Invoice']);

        $invoiceWithOtherAccount = $this->createInvoice(['title' => 'Other Invoice', 'account' => $otherAccount]);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Invoice::class, $result);
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

        $this->createInvoice(['title' => 'Test Invoice 1']);
        $this->createInvoice(['title' => 'Test Invoice 2']);

        $this->createInvoice(['title' => 'Other Invoice', 'account' => $otherAccount]);

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

        $this->createInvoice(['title' => 'Test Invoice']);

        $this->createInvoice(['title' => 'Other Order Invoice', 'order' => $otherOrder]);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Invoice::class, $result);
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

        $this->createInvoice(['title' => 'Test Invoice 1']);
        $this->createInvoice(['title' => 'Test Invoice 2']);

        $this->createInvoice(['title' => 'Other Order Invoice', 'order' => $otherOrder]);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
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

        $invoice = new Invoice();
        $invoice->setAccount($account);
        $invoice->setOrder($order);
        $invoice->setTitle('测试发票抬头');

        return $invoice;
    }

    protected function getRepository(): InvoiceRepository
    {
        return self::getService(InvoiceRepository::class);
    }
}
