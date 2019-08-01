<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RevenueDistributionRepository")
 * @ORM\Table("revenue_distributions")
 *
 * @Serializer\XmlRoot("RevenueDistribution")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "app_revenue_getrevenuedistributionbyid",
 *         parameters = { "id" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "conversion",
 *     href = @Hateoas\Route(
 *         "app_conversion_getconversionbyid",
 *         parameters = { "id" = "expr(object.getConversionId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "platform-total-revenue-distribution",
 *     href = @Hateoas\Route(
 *         "app_revenue_getrevenuesdistributionstotalsumbyplatform",
 *         parameters = { "platform" = "expr(object.getPlatform())" }
 *     )
 * )
 */
class RevenueDistribution
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @ORM\Column(type="integer")
     * @var int|null
     * @Serializer\Expose
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Conversion", inversedBy="revenueDistribution")
     * @ORM\JoinColumn(nullable=false)
     * @var Conversion|null
     */
    private $conversion;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return RevenueDistribution
     */
    public function setPlatform(string $platform): self
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
     * @param int $amount
     * @return RevenueDistribution
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Conversion|null
     */
    public function getConversion(): ?Conversion
    {
        return $this->conversion;
    }

    /**
     * @param Conversion|null $conversion
     * @return RevenueDistribution
     */
    public function setConversion(?Conversion $conversion): self
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getConversionId(): ?int
    {
        return $this->getConversion() ? $this->getConversion()->getId() : null;
    }
}
