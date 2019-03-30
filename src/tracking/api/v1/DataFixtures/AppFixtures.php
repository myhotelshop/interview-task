<?php

namespace App\tracking\api\v1\DataFixtures;

use App\tracking\api\v1\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->seedCustomer($manager);
        $manager->flush();
    }

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
}
