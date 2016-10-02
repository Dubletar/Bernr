<?php

namespace UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GeolocationRepository extends EntityRepository
{
    public function findRegionByCountryCode($countryCode)
    {

    }
}

