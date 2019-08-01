<?php

namespace App\Repository;

use App\Entity\Conversion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Conversion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversion[]    findAll()
 * @method Conversion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Conversion::class);
    }

    // /**
    //  * @return Conversion[] Returns an array of Conversion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Conversion
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
