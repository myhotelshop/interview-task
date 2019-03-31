<?php

namespace App\tracking\api\v1\DataFixtures;

use App\tracking\api\v1\Entity\Customer;
use App\tracking\api\v1\Entity\Platform;
use App\tracking\api\v1\Repository\CustomerRepository;
use App\tracking\api\v1\Repository\PlatformRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AppFixtures
 * @package App\tracking\api\v1\DataFixtures
 */
class AppFixtures extends Fixture
{

    /**
     * @var CustomerRepository
     */
    private $customersRepository;
    /**
     * @var PlatformRepository
     */
    private $platformsRepository;

    /**
     * AppFixtures constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->customersRepository = $em->getRepository(Customer::class);
        $this->platformsRepository = $em->getRepository(Platform::class);
    }

    /**
     * Loads the data into the database
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->seedCustomer($manager);
        $this->seedPlatforms($manager);
        $this->seedPlacements($manager);
        $manager->flush();
    }

    /**
     * A customer seeder
     * @param ObjectManager $manager
     */
    private function seedCustomer(ObjectManager $manager)
    {
        $customers = ['Advertiser1', 'Advertiser2'];
        foreach ($customers as $customerName) {
            $customer = new Customer();
            $customer->setName($customerName);
            $manager->persist($customer);
        }
        $manager->flush();
    }

    /**
     * A platforms seeder
     * @param ObjectManager $manager
     */
    private function seedPlatforms(ObjectManager $manager)
    {
        $platforms = ['trivago', 'TripAdvisor', 'Kayak'];
        foreach ($platforms as $platformName) {
            $platform = new Platform();
            $platform->setName($platformName);
            $manager->persist($platform);
        }
        $manager->flush();
    }

    /**
     * A placements seeder
     * @param ObjectManager $manager
     */
    private function seedPlacements(ObjectManager $manager)
    {
        $platforms = $this->platformsRepository->findAll();
        $customers = $this->customersRepository->findAll();

        foreach ($platforms as $platform) {
            foreach ($customers as $customer) {
                $platform->addCustomer($customer);
                $manager->persist($platform);
            }
        }
        $manager->flush();
    }
}
