<?php

namespace App\Repository;

use App\Entity\RevenueDistribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RevenueDistribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevenueDistribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevenueDistribution[]    findAll()
 * @method RevenueDistribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevenueDistributionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RevenueDistribution::class);
    }



    // /**
    //  * @return RevenueDistribution[] Returns an array of RevenueDistribution objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RevenueDistribution
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
