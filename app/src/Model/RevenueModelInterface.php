<?php


namespace App\Model;


use App\Entity\RevenueDistribution;
use App\Entity\VisitCollection;

interface RevenueModelInterface
{
    /**
     * @param array $params
     * @return RevenueDistribution[]
     */
    public function getDistributionsBy(array $params): array;

    /**
     * @param int $amount
     * @param VisitCollection $platformVisits
     * @return RevenueDistribution[]
     */
    public function distribute(int $amount, VisitCollection $platformVisits): array;
}