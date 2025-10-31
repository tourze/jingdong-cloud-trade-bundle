<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    public const ACCOUNT_REFERENCE = 'account';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('测试京东账户');
        $account->setAppKey('test-app-key');
        $account->setAppSecret('test-app-secret');
        $manager->persist($account);

        $manager->flush();

        $this->addReference(self::ACCOUNT_REFERENCE, $account);
    }
}
