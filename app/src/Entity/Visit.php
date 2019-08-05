<?php


namespace App\Entity;


class Visit
{
    /**
     * @var string
     */
    private $platform;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return Visit
     */
    public function setPlatform(string $platform): Visit
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     * @return Visit
     */
    public function setCustomerId(int $customerId): Visit
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Visit
     */
    public function setDate(\DateTime $date): Visit
    {
        $this->date = $date;
        return $this;
    }


}