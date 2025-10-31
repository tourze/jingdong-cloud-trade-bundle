<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const ORDER_REFERENCE = 'order';

    public function load(ObjectManager $manager): void
    {
        $order = new Order();
        $order->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $order->setOrderId('JD-ORDER-001');
        $order->setOrderState('已完成');
        $order->setPaymentState('已支付');
        $order->setLogisticsState('已发货');
        $order->setReceiverName('测试收货人');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('测试详细地址');
        $order->setOrderTotalPrice('100.00');
        $order->setOrderPaymentPrice('100.00');
        $order->setFreightPrice('10.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true); // 防止在测试环境触发同步事件

        $manager->persist($order);
        $manager->flush();

        $this->addReference(self::ORDER_REFERENCE, $order);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
