<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;

class VisitCollection extends ArrayCollection
{
    /**
     * @return Visit|null
     */
    public function getEarliest(): ?Visit
    {
        $earliest = null;

        /** @var Visit $visit */
        foreach ($this->toArray() as $visit) {
            if (!$earliest || $visit->getDate() < $earliest) {
                $earliest = $visit;
            }
        }

        return $earliest;
    }

    /**
     * @return Visit|null
     */
    public function getLatest(): ?Visit
    {
        $latest = new \DateTime('1900-01-01');

        /** @var Visit $visit */
        foreach ($this->toArray() as $visit) {
            if (!$latest || $visit->getDate() > $latest) {
                $latest = $visit;
            }
        }

        return $latest;
    }
}