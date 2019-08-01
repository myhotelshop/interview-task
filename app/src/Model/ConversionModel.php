<?php


namespace App\Model;


use App\Entity\Conversion;
use Doctrine\Common\Persistence\ManagerRegistry;

class ConversionModel implements ConversionModelInterface
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
    public function getBy(array $params): array
    {
        return $this->registry->getRepository(Conversion::class)->findAll();
    }

    /**
     * @inheritDoc
     */
    public function post(Conversion $conversion)
    {
        $manager = $this->registry->getManager();

        $distributions = $conversion->getRevenueDistribution();

        foreach ($distributions->toArray() as $distribution) {
            $manager->persist($distribution);
        }

        $manager->persist($conversion);
        $manager->flush();
    }
}