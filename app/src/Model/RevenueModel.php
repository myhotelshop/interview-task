<?php


namespace App\Model;


use App\Entity\VisitCollection;
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
        // TODO: Implement getDistributionsBy() method.
    }

    /**
     * @inheritDoc
     */
    public function distribute(int $amount, VisitCollection $platformVisits): array
    {
        return [];
    }
}