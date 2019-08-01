<?php


namespace App\Model;


use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use App\Entity\TotalRevenueDistribution;
use App\Entity\VisitCollection;
use App\Repository\RevenueDistributionRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RevenueModel implements RevenueModelInterface
{

    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(
        ManagerRegistry $registry
    )
    {
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function getDistributionsBy(array $params): array
    {
        return $this->registry->getRepository(RevenueDistribution::class)->findBy($params);
    }

    /**
     * @inheritDoc
     */
    public function distribute(Conversion $conversion, VisitCollection $platformVisits): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTotalRevenueDistributionByPlatform(string $platform): ?TotalRevenueDistribution
    {
        $totalDistribution = null;
        /** @var RevenueDistributionRepository $repository */
        $repository = $this->registry->getRepository(RevenueDistribution::class);


        $rows = $repository->createQueryBuilder('rd')
            ->select('SUM(rd.amount) AS total')
            ->andWhere('rd.platform = :platform')
            ->setParameter('platform', $platform)
            ->getQuery()
            ->getResult();

        $total = $rows[0]['total'] ?? null;

        if ($total) {
            $totalDistribution = new TotalRevenueDistribution();

            $totalDistribution->setPlatform($platform);
            $totalDistribution->setAmount($total);
        }


        return $totalDistribution;
    }
}