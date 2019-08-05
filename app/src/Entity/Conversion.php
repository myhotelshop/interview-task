<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversionRepository")
 * @ORM\Table("conversions")
 *
 * @Serializer\XmlRoot("Conversion")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "app_conversion_getconversionbyid",
 *         parameters = { "id" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "revenue-distributions",
 *     href = @Hateoas\Route(
 *         "app_revenue_getrevenuesdistributions",
 *         parameters = { "conversionId" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(!object.getRevenueDistribution().toArray())"
 *     )
 * )
 */
class Conversion
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
    private $bookingNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @var integer|null
     * @Serializer\Expose
     */
    private $customerId;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @ORM\Column(type="integer")
     * @var integer|null
     * @Serializer\Expose
     */
    private $revenue;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime|null
     * @Serializer\Expose
     */
    private $conversationDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RevenueDistribution", mappedBy="conversion", orphanRemoval=true)
     * @var Collection|RevenueDistribution[]
     */
    private $revenueDistribution;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $entryPoint;

    public function __construct()
    {
        $this->revenueDistribution = new ArrayCollection();
    }

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
    public function getBookingNumber(): ?string
    {
        return $this->bookingNumber;
    }

    /**
     * @param string $bookingNumber
     * @return Conversion
     */
    public function setBookingNumber(string $bookingNumber): self
    {
        $this->bookingNumber = $bookingNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     * @return Conversion
     */
    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
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
     * @return Conversion
     */
    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRevenue(): ?int
    {
        return $this->revenue;
    }

    /**
     * @param int $revenue
     * @return Conversion
     */
    public function setRevenue(int $revenue): self
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getConversationDate(): ?DateTime
    {
        return $this->conversationDate;
    }

    /**
     * @param DateTime $conversationDate
     * @return Conversion
     */
    public function setConversationDate(DateTime $conversationDate): self
    {
        $this->conversationDate = $conversationDate;

        return $this;
    }

    /**
     * @return Collection|RevenueDistribution[]
     */
    public function getRevenueDistribution(): Collection
    {
        return $this->revenueDistribution;
    }

    /**
     * @param RevenueDistribution $revenueDistribution
     * @return Conversion
     */
    public function addRevenueDistribution(RevenueDistribution $revenueDistribution): self
    {
        if (!$this->revenueDistribution->contains($revenueDistribution)) {
            $this->revenueDistribution[] = $revenueDistribution;
            $revenueDistribution->setConversion($this);
        }

        return $this;
    }

    /**
     * @param RevenueDistribution $revenueDistribution
     * @return Conversion
     */
    public function removeRevenueDistribution(RevenueDistribution $revenueDistribution): self
    {
        if ($this->revenueDistribution->contains($revenueDistribution)) {
            $this->revenueDistribution->removeElement($revenueDistribution);
            // set the owning side to null (unless already changed)
            if ($revenueDistribution->getConversion() === $this) {
                $revenueDistribution->setConversion(null);
            }
        }

        return $this;
    }

    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }

    public function setEntryPoint(string $entryPoint): self
    {
        $this->entryPoint = $entryPoint;

        return $this;
    }
}
