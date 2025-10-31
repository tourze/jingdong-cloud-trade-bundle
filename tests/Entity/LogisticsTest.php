<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Logistics;
use JingdongCloudTradeBundle\Entity\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Logistics::class)]
final class LogisticsTest extends AbstractEntityTestCase
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

        $logistics = new Logistics();
        $logistics->setAccount($account);
        $logistics->setOrder($order);
        $logistics->setLogisticsCode('SF');
        $logistics->setLogisticsName('顺丰速运');
        $logistics->setWaybillCode('SF123456789');

        return $logistics;
    }

    public static function propertiesProvider(): iterable
    {
        yield 'logisticsCode' => ['logisticsCode', 'JD'];
        yield 'logisticsName' => ['logisticsName', '京东物流'];
        yield 'waybillCode' => ['waybillCode', 'JD987654321'];
        yield 'trackInfo' => ['trackInfo', '{"status": "delivered", "time": "2024-01-01 10:00:00"}'];
        yield 'lastModificationTime' => ['lastModificationTime', new \DateTimeImmutable()];
    }
}
