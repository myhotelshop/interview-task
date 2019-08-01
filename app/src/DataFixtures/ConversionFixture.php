<?php

namespace App\DataFixtures;

use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ConversionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $conversion = new Conversion();
        $conversion->setBookingNumber("123_asdft");
        $conversion->setConversationDate(new \DateTime('now'));
        $conversion->setCustomerId(123);
        $conversion->setPlatform("trivago");
        $conversion->setRevenue(250);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('trivago');
        $distribution->setAmount(100);

        $manager->persist($distribution);
        $conversion->addRevenueDistribution($distribution);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('tripadvisor');
        $distribution->setAmount(150);

        $manager->persist($distribution);
        $conversion->addRevenueDistribution($distribution);

        $manager->persist($conversion);

        $conversion = new Conversion();
        $conversion->setBookingNumber("12_asaqsaddft");
        $conversion->setConversationDate(new \DateTime('now'));
        $conversion->setCustomerId(12);
        $conversion->setPlatform("tripadvisor");
        $conversion->setRevenue(150);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('tripadvisor');
        $distribution->setAmount(150);

        $conversion->addRevenueDistribution($distribution);

        $manager->persist($distribution);
        $manager->persist($conversion);

        $conversion = new Conversion();
        $conversion->setBookingNumber("12_asdftdd");
        $conversion->setConversationDate((new \DateTime('now'))->modify('-3 day'));
        $conversion->setCustomerId(12);
        $conversion->setPlatform("trivago");
        $conversion->setRevenue(250);

        $manager->persist($conversion);

        $manager->flush();
    }
}
