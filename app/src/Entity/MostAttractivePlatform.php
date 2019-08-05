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
 *         "app_conversion_getmostattractiveplatform"
 *     )
 * )
 */
class MostAttractivePlatform
{
    /**
     * @var string
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @var int
     * @Serializer\Expose
     */
    private $occurrences;

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return MostAttractivePlatform
     */
    public function setPlatform(string $platform): MostAttractivePlatform
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return int
     */
    public function getOccurrences(): int
    {
        return $this->occurrences;
    }

    /**
     * @param int $occurrences
     * @return MostAttractivePlatform
     */
    public function setOccurrences(int $occurrences): MostAttractivePlatform
    {
        $this->occurrences = $occurrences;
        return $this;
    }
}