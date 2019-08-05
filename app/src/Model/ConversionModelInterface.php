<?php


namespace App\Model;


use App\Entity\Conversion;
use App\Entity\MostAttractivePlatform;

interface ConversionModelInterface
{
    /**
     * @param array $params
     * @return Conversion[]
     */
    public function getBy(array $params): array;

    /**
     * @param Conversion $conversion
     */
    public function post(Conversion $conversion);

    /**
     * @param array $params
     * @return int
     */
    public function countBy(array $params): int;

    /**
     * @return MostAttractivePlatform|null
     */
    public function getMostAttractivePlatform(): ?MostAttractivePlatform;
}