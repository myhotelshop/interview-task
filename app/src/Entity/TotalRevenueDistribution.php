<?php


namespace App\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("TotalRevenueDistribution")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "app_revenue_getrevenuesdistributionstotalsumbyplatform",
 *         parameters = { "platform" = "expr(object.getPlatform())" }
 *     )
 * )
 */
class TotalRevenueDistribution
{
    /**
     * @var string|null
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @var int|null
     * @Serializer\Expose
     */
    private $amount;

    /**
     * @return string|null
     */
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    /**
     * @param string|null $platform
     *
     * @return TotalRevenueDistribution
     */
    public function setPlatform(?string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     *
     * @return TotalRevenueDistribution
     */
    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }


}