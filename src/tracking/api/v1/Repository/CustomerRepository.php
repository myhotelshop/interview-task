<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CustomerRepository
 * @package App\tracking\api\v1\Repository
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * CustomerRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);
    }
}
