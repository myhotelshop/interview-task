<?php
namespace App\tracking\api\v1\Repository;

use App\tracking\api\v1\Entity\PlatformRevenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use PDO;
use stdClass;

/**
 * Class PlatformRevenueRepository
 * @package App\tracking\api\v1\Repository
 */
class PlatformRevenueRepository extends ServiceEntityRepository
{
    /**
     * PlatformRevenueRepository constructor.
     * @param RegistryInterface $registry
     */
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
     * Query the database for the revenue of a given platform
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

    /**
     * Query the database for the number of conversions for a given platform
     * @param int $platform
     * @return stdClass|bool
     * @throws DBALException
     */
    public function getConversionOfPlatform(int $platform)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT p.platform_id, COUNT(p.platform_id) conversion
                FROM platform_revenue p
                INNER JOIN (
                    SELECT booking_reference, MAX(created) conversionDate
                    FROM platform_revenue
                    GROUP BY booking_reference
                ) t1
                ON t1.booking_reference = p.booking_reference AND t1.conversionDate = p.created
                WHERE p.platform_id = :platform
                GROUP BY p.platform_id 
                ORDER BY MAX(conversion) DESC';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('platform', $platform, ParameterType::INTEGER);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
