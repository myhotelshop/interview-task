<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\Platform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Platform[]    findAll()
 * @method Platform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Platform::class);
    }
}
