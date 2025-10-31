<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Logistics;
use JingdongCloudTradeBundle\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class LogisticsFixtures extends Fixture implements DependentFixtureInterface
{
    public const LOGISTICS_REFERENCE = 'logistics';

    public function load(ObjectManager $manager): void
    {
        $logistics = new Logistics();
        $logistics->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $logistics->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $logistics->setLogisticsCode('SF');
        $logistics->setLogisticsName('顺丰速运');
        $logistics->setWaybillCode('SF1234567890');

        $manager->persist($logistics);
        $manager->flush();

        $this->addReference(self::LOGISTICS_REFERENCE, $logistics);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            OrderFixtures::class,
        ];
    }
}
