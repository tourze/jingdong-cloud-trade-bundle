<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\AfsService;
use JingdongCloudTradeBundle\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AfsServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public const AFS_SERVICE_REFERENCE = 'afs-service';

    public function load(ObjectManager $manager): void
    {
        $afsService = new AfsService();
        $afsService->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $afsService->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $afsService->setAfsServiceId('AFS-TEST-001');
        $afsService->setAfsType('10');
        $afsService->setAfsServiceState('申请中');

        $manager->persist($afsService);
        $manager->flush();

        $this->addReference(self::AFS_SERVICE_REFERENCE, $afsService);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            OrderFixtures::class,
        ];
    }
}
