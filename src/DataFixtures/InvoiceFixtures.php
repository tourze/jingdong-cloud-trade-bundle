<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Invoice;
use JingdongCloudTradeBundle\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public const INVOICE_REFERENCE = 'invoice';

    public function load(ObjectManager $manager): void
    {
        $invoice = new Invoice();
        $invoice->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $invoice->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $invoice->setTitle('测试发票抬头');

        $manager->persist($invoice);
        $manager->flush();

        $this->addReference(self::INVOICE_REFERENCE, $invoice);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            OrderFixtures::class,
        ];
    }
}
