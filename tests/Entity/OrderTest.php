<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Order::class)]
final class OrderTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setAccount($account);
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setReceiverName('张三');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京市');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('某某街道123号');
        $order->setOrderTotalPrice('299.99');
        $order->setOrderPaymentPrice('289.99');
        $order->setFreightPrice('10.00');
        $order->setOrderTime(new \DateTimeImmutable());

        return $order;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'orderId' => ['orderId', 'JD987654321'];
        yield 'orderState' => ['orderState', 'COMPLETED'];
        yield 'paymentState' => ['paymentState', 'UNPAID'];
        yield 'logisticsState' => ['logisticsState', 'UNSHIPPED'];
        yield 'receiverName' => ['receiverName', '李四'];
        yield 'receiverMobile' => ['receiverMobile', '13900139000'];
        yield 'receiverProvince' => ['receiverProvince', '上海市'];
        yield 'receiverCity' => ['receiverCity', '上海市'];
        yield 'receiverCounty' => ['receiverCounty', '浦东新区'];
        yield 'receiverAddress' => ['receiverAddress', '某某小区456号'];
        yield 'orderTotalPrice' => ['orderTotalPrice', '599.99'];
        yield 'orderPaymentPrice' => ['orderPaymentPrice', '579.99'];
        yield 'freightPrice' => ['freightPrice', '20.00'];
        yield 'paymentTime' => ['paymentTime', new \DateTimeImmutable()];
        yield 'deliveryTime' => ['deliveryTime', new \DateTimeImmutable()];
        yield 'completionTime' => ['completionTime', new \DateTimeImmutable()];
        yield 'synced' => ['synced', true];
        yield 'waybillCode' => ['waybillCode', 'SF123456789'];
    }
}
