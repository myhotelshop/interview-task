<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\PlatformRevenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use PDO;
use stdClass;

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
        $this->getEntityManager()->persist($platformRevenue);
        $this->getEntityManager()->flush();
    }

    /**
     * Query the database for the first platform that attracts users
     * @return array|null
     * @throws DBALException
     */
    public function getMostAttractedPlatform(): ?stdClass
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT p.name FROM platform p 
                INNER JOIN (SELECT platform_id FROM platform_revenue
                GROUP BY booking_reference
                ORDER BY created) t1
                ON t1.platform_id = p.id 
                GROUP BY p.name 
                ORDER BY COUNT(p.name) DESC 
                LIMIT 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param int $platform
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRevenueForPlatform(int $platform)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('SUM(u.revenue) amount')
            ->from(PlatformRevenue::class, 'u')
            ->where('u.platform = :platform')
            ->setParameter('platform', $platform);
        return $qb->getQuery()->getSingleResult();
    }
}
