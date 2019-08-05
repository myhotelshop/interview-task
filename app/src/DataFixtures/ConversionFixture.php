<?php

namespace App\DataFixtures;

use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ConversionFixture extends Fixture
{
    /**
     * Automated tests will rely on this fixtures
     * In order to prevent collisions with data inserted manually in DB, platforms are prefixed with "test_"
     */
    public function load(ObjectManager $manager)
    {

        /**
         * Conversion 1
         */
        $conversion = new Conversion();
        $conversion->setBookingNumber("123_asdft");
        $conversion->setConversationDate(new \DateTime('now'));
        $conversion->setCustomerId(123);
        $conversion->setPlatform("test_trivago");
        $conversion->setEntryPoint("test_tripadvisor");
        $conversion->setRevenue(250);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_trivago');
        $distribution->setAmount(100);

        $manager->persist($distribution);
        $conversion->addRevenueDistribution($distribution);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_tripadvisor');
        $distribution->setAmount(150);

        $manager->persist($distribution);
        $conversion->addRevenueDistribution($distribution);

        $manager->persist($conversion);

        /**
         * Conversion 2
         */
        $conversion = new Conversion();
        $conversion->setBookingNumber("12_asaqsaddft");
        $conversion->setConversationDate(new \DateTime('now'));
        $conversion->setCustomerId(12);
        $conversion->setPlatform("test_tripadvisor");
        $conversion->setEntryPoint("test_tripadvisor");
        $conversion->setRevenue(150);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_tripadvisor');
        $distribution->setAmount(150);

        $conversion->addRevenueDistribution($distribution);

        $manager->persist($distribution);
        $manager->persist($conversion);

        /**
         * Conversion 3
         */
        $conversion = new Conversion();
        $conversion->setBookingNumber("12_asdftdd");
        $conversion->setConversationDate((new \DateTime('now'))->modify('-3 day'));
        $conversion->setCustomerId(12);
        $conversion->setPlatform("test_trivago");
        $conversion->setEntryPoint("test_kayak");
        $conversion->setRevenue(250);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_kayak');
        $distribution->setAmount(150);

        $conversion->addRevenueDistribution($distribution);

        $manager->persist($distribution);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_tripadvisor');
        $distribution->setAmount(50);

        $conversion->addRevenueDistribution($distribution);

        $manager->persist($distribution);

        $distribution = new RevenueDistribution();
        $distribution->setPlatform('test_trivago');
        $distribution->setAmount(100);

        $conversion->addRevenueDistribution($distribution);

        $manager->persist($distribution);

        $manager->persist($conversion);

        $manager->flush();
    }
}
