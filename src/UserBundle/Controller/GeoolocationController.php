<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function loginAction(Request $request)
    {
        $countries = $this->getEm()->getRepository('UserBundle:Country')
            ->findAll();

        return $this->createJmsResponse($countries);
    }
    
    /**
     * @Route("/test", name="test", options={"expose":true})
     * 
     * @param Request $request
     * @return Response
     */
    public function testAction(Request $request)
    {
        $session = $this->container->get('session');
        
        return $this->createJmsResponse($session->getId());
    }
}
