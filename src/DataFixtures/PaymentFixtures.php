<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public const PAYMENT_REFERENCE = 'payment';

    public function load(ObjectManager $manager): void
    {
        $payment = new Payment();
        $payment->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $payment->setPaymentId('PAY-TEST-001');
        $payment->setPaymentAmount('100.00');

        $manager->persist($payment);
        $manager->flush();

        $this->addReference(self::PAYMENT_REFERENCE, $payment);
    }

    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
        ];
    }
}
