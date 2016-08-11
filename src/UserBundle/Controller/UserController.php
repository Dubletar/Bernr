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
        $username = $request->request->get('username');
        $password = md5($request->request->get('password'));
        
        // Set up return data container.
        $data = array();
        
        // Validate the login data
        // Check if a user account exists.
        $userEntity = $this->getEm()->getRepository('UserBundle:User')
            ->findOneBy(array('username' => $username));
        
        $data['userEntity'] = $userEntity;
        
        // If no user, mark as an error.
        if (!$userEntity) {
            $data['error'] = true;
        }
        
        // If there's been no error, check if the password matches too.
        if (!array_key_exists('error', $data)) {
            
            $passwordEntity = $this->getEm()->getRepository('UserBundle:Password')
                ->findOneBy(array('password' => $password, 'current' => 1, 'userId' => $userEntity->getId()));
            
            /**
             * DELETE
             */
            $data['password'] = $passwordEntity;
            
            // If no match, mark as error
            if (!$passwordEntity) {
                $data['error'] = true;
            }
        }
        
        // If no errors exist, log in the user.
        if (!array_key_exists('error', $data)) {
            
            // Prepare the session manager
            $sessionManager = $this->container->get('sess.manager');
            $sessionManager->logUserIn($userEntity, $request->getClientIp());
            
            return $this->createJmsResponse(true);
        }
        
        return $this->render(':navigation_templates/User:login_form.html.twig', $data);
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
