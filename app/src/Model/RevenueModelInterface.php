<?php


namespace App\Model;


use App\Entity\Conversion;
use App\Entity\RevenueDistribution;
use App\Entity\TotalRevenueDistribution;
use App\Entity\VisitCollection;

interface RevenueModelInterface
{
    /**
     * @param array $params
     * @return RevenueDistribution[]
     */
    public function getDistributionsBy(array $params): array;

    /**
     * @param Conversion $conversion
     * @param VisitCollection $platformVisits
     * @return RevenueDistribution[]
     */
    public function distribute(Conversion $conversion, VisitCollection $platformVisits): array;

    /**
     * @param string $platform
     * @return TotalRevenueDistribution|null
     */
    public function getTotalRevenueDistributionByPlatform(string $platform): ?TotalRevenueDistribution;

    /**
     * @param array $params
     * @return int
     */
    public function countDistributionsBy(array $params): int;
}