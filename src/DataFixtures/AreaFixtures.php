<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Area;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AreaFixtures extends Fixture
{
    public const AREA_REFERENCE = 'area';

    public function load(ObjectManager $manager): void
    {
        $area = new Area();

        $manager->persist($area);
        $manager->flush();

        $this->addReference(self::AREA_REFERENCE, $area);
    }
}
