<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\Platform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class PlatformRepository
 * @package App\tracking\api\v1\Repository
 */
class PlatformRepository extends ServiceEntityRepository
{
    /**
     * PlatformRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Platform::class);
    }
}
