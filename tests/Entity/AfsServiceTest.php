<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\AfsService;
use JingdongCloudTradeBundle\Entity\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AfsService::class)]
final class AfsServiceTest extends AbstractEntityTestCase
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

        $afsService = new AfsService();
        $afsService->setAccount($account);
        $afsService->setOrder($order);
        $afsService->setAfsServiceId('AFS123456789');
        $afsService->setAfsType('10');
        $afsService->setAfsServiceState('1');

        return $afsService;
    }

    public static function propertiesProvider(): iterable
    {
        yield 'afsServiceId' => ['afsServiceId', 'AFS987654321'];
        yield 'afsType' => ['afsType', '20'];
        yield 'afsServiceState' => ['afsServiceState', '2'];
        yield 'applyReason' => ['applyReason', '商品有质量问题'];
        yield 'applyDescription' => ['applyDescription', '商品外包装破损，内部商品也有损坏'];
        yield 'applyTime' => ['applyTime', new \DateTimeImmutable()];
        yield 'auditTime' => ['auditTime', new \DateTimeImmutable()];
        yield 'completeTime' => ['completeTime', new \DateTimeImmutable()];
        yield 'refundAmount' => ['refundAmount', '299.99'];
        yield 'logisticsCompany' => ['logisticsCompany', '顺丰速运'];
        yield 'logisticsNo' => ['logisticsNo', 'SF1234567890123'];
    }
}
