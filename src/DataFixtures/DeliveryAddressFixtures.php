<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class DeliveryAddressFixtures extends Fixture implements DependentFixtureInterface
{
    public const DELIVERY_ADDRESS_REFERENCE = 'delivery-address';

    public function load(ObjectManager $manager): void
    {
        $deliveryAddress = new DeliveryAddress();
        $deliveryAddress->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $deliveryAddress->setReceiverName('测试收货人');
        $deliveryAddress->setReceiverMobile('13800138000');
        $deliveryAddress->setProvince('北京');
        $deliveryAddress->setCity('北京市');
        $deliveryAddress->setCounty('朝阳区');
        $deliveryAddress->setDetailAddress('测试详细地址');

        $manager->persist($deliveryAddress);
        $manager->flush();

        $this->addReference(self::DELIVERY_ADDRESS_REFERENCE, $deliveryAddress);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
