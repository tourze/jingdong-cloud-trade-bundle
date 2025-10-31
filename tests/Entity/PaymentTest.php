<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\Payment;
use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Payment::class)]
final class PaymentTest extends AbstractEntityTestCase
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

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPaymentId('PAY123456789');
        $payment->setPaymentAmount('299.99');

        return $payment;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'paymentId' => ['paymentId', 'PAY987654321'];
        yield 'paymentMethod' => ['paymentMethod', PaymentMethodEnum::ONLINE];
        yield 'paymentChannel' => ['paymentChannel', PaymentChannelEnum::ALIPAY];
        yield 'paymentAmount' => ['paymentAmount', '599.99'];
        yield 'paymentState' => ['paymentState', PaymentStateEnum::PAID];
        yield 'paymentTime' => ['paymentTime', new \DateTimeImmutable()];
        yield 'paymentNote' => ['paymentNote', '测试支付备注'];
    }
}
