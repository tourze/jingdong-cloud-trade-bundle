<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class OrderItemFixtures extends Fixture implements DependentFixtureInterface
{
    public const ORDER_ITEM_REFERENCE = 'order-item';

    public function load(ObjectManager $manager): void
    {
        $orderItem = new OrderItem();
        $orderItem->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $orderItem->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $orderItem->setSkuId('SKU-TEST-001');
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(1);
        $orderItem->setPrice('90.00');
        $orderItem->setTotalPrice('90.00');

        $manager->persist($orderItem);
        $manager->flush();

        $this->addReference(self::ORDER_ITEM_REFERENCE, $orderItem);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            OrderFixtures::class,
        ];
    }
}
