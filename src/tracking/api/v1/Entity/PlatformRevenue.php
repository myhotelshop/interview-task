<?php
namespace App\tracking\api\v1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

/**
 * @ORM\Entity(repositoryClass="App\tracking\api\v1\Repository\PlatformRepository")
 */
class PlatformRevenue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     */
    private $platform;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $revenue;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank()
     */
    private $created;

    /**
     * @ORM\Column(type="guid")
     */
    private $bookingReference;

    public function __construct(
        Customer $customer,
        Platform $platform,
        string $bookingReference,
        int $revenueShare,
        DateTimeImmutable $created
    ) {
        $this->setBookingReference($bookingReference);
        $this->setCreated($created);
        $this->setCustomer($customer);
        $this->setPlatform($platform);
        $this->setRevenue($revenueShare);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform(Platform $platform): void
    {
        $this->platform = $platform;
    }

    /**
     * @return int
     */
    public function getRevenue(): int
    {
        return (int) $this->revenue;
    }

    /**
     * @param int $revenue
     */
    public function setRevenue(int $revenue): void
    {
        $this->revenue = $revenue;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param DateTimeImmutable $created
     */
    public function setCreated(DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getBookingReference()
    {
        return $this->bookingReference;
    }

    /**
     * @param mixed $bookingReference
     */
    public function setBookingReference($bookingReference): void
    {
        $this->bookingReference = $bookingReference;
    }
}
