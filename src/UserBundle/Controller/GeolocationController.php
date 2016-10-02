<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Country;
use UtilityBundle\Controller\AbstractController;

/**
 * @Route("/geolocation")
 */
class GeolocationController extends AbstractController
{
    /**
     * @Route("/get-countries", name="get_countries", options={"expose":true})
     *
     * @return Response
     */
    public function getCountriesAction()
    {
        $countries = $this->getEm()->getRepository('UserBundle:Country')
            ->findAll();

        return $this->createJmsResponse($countries);
    }
    
    /**
     * @Route("/get-country-regions/{id}", name="get_country_regions", options={"expose":true})
     *
     * @param int $id
     *
     * @return Response
     */
    public function getRegionsAction($id)
    {
        $country = $this->getEm()->getRepository("UserBundle:Country")
            ->findOneBy(array("id" => $id));

        if (!($country instanceof Country)) {
            return $this->createJmsResponse(false);
        }

        $regions = $this->getEm()->getRepository("UserBundle:Geolocation")
            ->findRegionByCountryCode($country->getCode());

        if (!$regions) {
            return $this->createJmsResponse(false);
        }

        return $this->createJmsResponse($regions);
    }

    /**
     * @Route("/get-region-cities/{countryCode}/{region}", name="get_country_regions", options={"expose":true})
     *
     * @param string $countryCode
     * @param string $region
     *
     * @return Response
     */
    public function getCitiesAction($countryCode, $region)
    {
        $country = $this->getEm()->getRepository("UserBundle:Country")
            ->findOneBy(array("code" => $countryCode));

        if (!($country instanceof Country)) {
            return $this->createJmsResponse(false);
        }

        $cities = $this->getEm()->getRepository("UserBundle:Geolocation")
            ->findCitiesByRegionAndCountry($country->getCode(), $region);

        if (!$cities) {
            return $this->createJmsResponse(false);
        }

        return $this->createJmsResponse($cities);
    }
}
