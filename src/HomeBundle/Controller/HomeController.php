<?php

namespace HomeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SessionBundle\Service\SessionManager;
use UtilityBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{    
    /**
     * This is the landing page of the site.
     * 
     * @Route("/")
     * 
     * @Template()
     */
    public function indexAction()
    {
        // SessionManager
        $sessionManager = $this->container->get('sess.manager');
        
        // Set user data. If user is not logged in, it will be false.
        $user = $sessionManager->loggedIn();
            
        return $this->render(
            'HomeBundle::index.html.twig',
            array(
                'user' => $user
            )
        );
    }
}
