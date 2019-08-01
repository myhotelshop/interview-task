<?php


namespace App\Model;


use App\Entity\Conversion;

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
}