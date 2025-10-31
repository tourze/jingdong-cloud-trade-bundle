<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SkuFixtures extends Fixture implements DependentFixtureInterface
{
    public const SKU_REFERENCE = 'sku';

    public function load(ObjectManager $manager): void
    {
        $sku = new Sku();
        $sku->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $sku->getBaseInfo()->setSkuId('JD-SKU-001');
        $sku->getBaseInfo()->setSkuName('测试商品SKU');
        $sku->getBaseInfo()->setPrice('99.99');

        $manager->persist($sku);
        $manager->flush();

        $this->addReference(self::SKU_REFERENCE, $sku);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
