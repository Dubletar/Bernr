<?php

namespace UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GeolocationRepository extends EntityRepository
{
    /**
     * @param $countryCode string
     * @return array
     */
    public function findRegionByCountryCode($countryCode)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("g")
            ->from("UserBundle:Geolocation", "g")
            ->where($qb->expr()->eq("g.country", ":countryCode"))
            ->setParameter("countryCode", $countryCode)
            ->groupBy("g.region")
            ->orderBy("g.region", "ASC");

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $countryCode string
     * @param $region string
     * @return array
     */
    public function findCitiesByRegionAndCountry($countryCode, $region)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("g")
            ->from("UserBundle:Geolocation", "g")
            ->where($qb->expr()->andX(
                $qb->expr()->eq("g.country", ":countryCode"),
                $qb->expr()->eq("g.region", ":region")
            ))
            ->setParameter("countryCode", $countryCode)
            ->setParameter("region", $region)
            ->orderBy("g.city", "ASC");

        return $qb->getQuery()->getResult();
    }
}

