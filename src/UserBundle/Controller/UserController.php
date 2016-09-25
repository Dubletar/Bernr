<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UtilityBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/login", name="login", options={"expose":true})
     * 
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        // Grab the parameters.
        $email = $request->request->get('email');
        $password = md5($request->request->get('password'));
        $isApp = $request->request->get('app');
        
        // Set up return data container.
        $data = array();
        
        // Validate the login data
        // Check if a user account exists.
        $userEntity = $this->getEm()->getRepository('UserBundle:User')
            ->findUserByEmail($email);

        // If no user, mark as an error.
        if (!$userEntity) {
            return $this->createJmsResponse(false);
        }

        $data['userEntity'] = $userEntity;
        
        // If no errors exist, log in the user.
        if (!array_key_exists('error', $data)) {
            
            // Prepare the session manager
            $sessionManager = $this->container->get('sess.manager');
            $sessionManager->logUserIn($userEntity, $request->getClientIp());

            if ($isApp) {
                return $this->createJmsResponse($userEntity);
            } else {
                return $this->createJmsResponse(true);
            }
        }

        // Failed to login
        if ($isApp) {
            return $this->createJmsResponse(false);
        } else {
            return $this->render(':navigation_templates/User:login_form.html.twig', $data);
        }
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
