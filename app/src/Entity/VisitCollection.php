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
            /** @var Visit $earliest */
            if (!$earliest || $visit->getDate() < $earliest->getDate()) {
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
        $latest = null;

        /** @var Visit $visit */
        foreach ($this->toArray() as $visit) {
            /** @var Visit $latest */
            if (!$latest || $visit->getDate() > $latest->getDate()) {
                $latest = $visit;
            }
        }

        return $latest;
    }
}