<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\PlatformRevenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PlatformRevenueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlatformRevenue::class);
    }

    /**
     * @param PlatformRevenue $platformRevenue
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(PlatformRevenue $platformRevenue)
    {
        $this->_em->persist($platformRevenue);
        $this->_em->flush();
    }
}
