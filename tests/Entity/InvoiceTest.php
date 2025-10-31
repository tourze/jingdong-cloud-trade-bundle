<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Invoice;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTitleTypeEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Invoice::class)]
final class InvoiceTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setAccount($account);

        $invoice = new Invoice();
        $invoice->setAccount($account);
        $invoice->setOrder($order);
        $invoice->setTitle('测试发票抬头');

        return $invoice;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'invoiceType' => ['invoiceType', InvoiceTypeEnum::VAT];
        yield 'titleType' => ['titleType', InvoiceTitleTypeEnum::COMPANY];
        yield 'title' => ['title', '企业发票抬头'];
        yield 'taxpayerIdentity' => ['taxpayerIdentity', '123456789012345'];
        yield 'registeredAddress' => ['registeredAddress', '北京市朝阳区某某街道123号'];
        yield 'registeredPhone' => ['registeredPhone', '010-12345678'];
        yield 'bankName' => ['bankName', '中国银行'];
        yield 'bankAccount' => ['bankAccount', '1234567890123456789'];
        yield 'invoiceCode' => ['invoiceCode', 'INV001'];
        yield 'invoiceNumber' => ['invoiceNumber', 'INV2024001'];
        yield 'invoiceAmount' => ['invoiceAmount', '99.99'];
        yield 'invoiceState' => ['invoiceState', InvoiceStateEnum::ISSUED];
        yield 'invoiceContent' => ['invoiceContent', InvoiceContentEnum::GOODS];
        yield 'downloadUrl' => ['downloadUrl', 'https://example.com/invoice.pdf'];
    }
}
