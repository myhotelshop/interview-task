<?php


namespace App\Model;


use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use App\Entity\TotalRevenueDistribution;
use App\Entity\Visit;
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
     * Dummy and "dirty" implementation to distribute the revenue
     *
     * @inheritDoc
     */
    public function distribute(Conversion $conversion, VisitCollection $platformVisits): array
    {
        /** @var RevenueDistribution[] $result */
        $result = [];
        $amount = $conversion->getRevenue() ?? 0;
        $visitedPlatformCount = $platformVisits->count();

        if ($visitedPlatformCount === 1) {
            /**
             * One visited platform means getting it all
             */
            $result[] = (new RevenueDistribution())
                ->setConversion($conversion)
                ->setPlatform($platformVisits->first()->getPlatform())
                ->setAmount($amount);
        } else {

            if ($visitedPlatformCount > 2) {
                /**
                 * when amount == 1000 -> (1000 - 450) - 350 = 200
                 *
                 * 0.45 -> reserved for first visited platform
                 * 0.35 -> reserved for last visited platform
                 */
                $amountToDistribute = round(($amount - $amount * 0.45) - $amount * 0.35);
                $distributionCalculation = round($amountToDistribute / ($visitedPlatformCount - 2));

                /**
                 * distribute amount equally by all visited platforms
                 * @var Visit $platformVisit
                 */
                foreach ($platformVisits->toArray() as $platformVisit) {
                    $result[] = (new RevenueDistribution())
                        ->setConversion($conversion)
                        ->setPlatform($platformVisit->getPlatform())
                        ->setAmount($distributionCalculation);
                }

                /**
                 * distribute for first and last visited platforms correctly
                 */

                $firstVisited = $platformVisits->getEarliest();
                $lastVisited = $platformVisits->getLatest();

                foreach ($result as $distribution) {
                    if ($distribution->getPlatform() === $firstVisited->getPlatform()) {
                        $distribution->setAmount(round($amount * 0.45));
                    } else if ($distribution->getPlatform() === $lastVisited->getPlatform()) {
                        $distribution->setAmount(round($amount * 0.35));
                    } else {
                        continue;
                    }
                }
            } else {

                /**
                 * In this case there are only 2 visits. distribute 0.55 and 0.45 to visited platforms respectively
                 */
                $result[] = (new RevenueDistribution())
                    ->setConversion($conversion)
                    ->setPlatform($platformVisits->getEarliest()->getPlatform())
                    ->setAmount(round($amount * 0.55));

                $result[] = (new RevenueDistribution())
                    ->setConversion($conversion)
                    ->setPlatform($platformVisits->getLatest()->getPlatform())
                    ->setAmount(round($amount * 0.45));
            }


        }

        return $result;
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

    /**
     * @inheritDoc
     */
    public function countBy(array $params): int
    {
        /** @var RevenueDistributionRepository $objectRepository */
        $objectRepository = $this->registry->getRepository(RevenueDistribution::class);
        return $objectRepository->count($params);
    }
}