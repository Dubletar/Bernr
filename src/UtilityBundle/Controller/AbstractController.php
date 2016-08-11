<?php

namespace UtilityBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class AbstractController extends Controller
{
    protected $entityManagers = array();

    /**
     * wrapper function for UtilityBundle:JsonResponseHelper.
     *
     * @param bool   $success
     * @param string $message
     * @param int    $status
     * @param array  $additionalData
     * @param array  $headers
     * @param string $translationDomain
     *
     * @return JsonResponse
     */
    protected function createJsonResponse(
        $success,
        $message,
        $status = 200,
        $additionalData = array(),
        $headers = array(),
        $translationDomain = 'ajax'
    ) {
        return $this->get('utility.json_response')
            ->createJsonResponse($success, $message, $status, $additionalData, $headers, $translationDomain);
    }

    /**
     * @param mixed                     $data
     * @param bool                      $success
     * @param string                    $message
     * @param int                       $status
     * @param array                     $headers
     * @param string                    $translationDomain
     * @param null|SerializationContext $serializationContext
     *
     * @return Response
     */
    public function createJmsResponse(
        $data,
        $success = true,
        $message = '',
        $status = 200,
        $headers = array(),
        $translationDomain = 'ajax',
        SerializationContext $serializationContext = null
    ) {
        return $this->get('utility.json_response')
            ->createJmsResponse(
                $data,
                $success,
                $message,
                $status,
                $headers,
                $translationDomain,
                $serializationContext
            );
    }

    /**
     * @param string $name
     * @return ObjectManager
     */
    protected function getEm($name = 'default')
    {
        if (!isset($this->entityManagers[$name])) {
            $this->entityManagers[$name] = $this->getDoctrine()->getManager($name);
        }

        return $this->entityManagers[$name];
    }
    
    /**
     * Redirects the page with the attributes intact
     * @return RedirectResponse
     */
    protected function refreshPage()
    {
        $request = $this->getRequest();
        return $this->redirect($this->generateUrl($request->get('_route'), $request->query->all()));
    }
    
    /**
     * 
     * @return User|null
     */
    protected function isUserLoggedIn()
    {
        // SessionManager
        $sessionManager = $this->container->get('sess.manager');
        
        // Set user data. If user is not logged in, it will be null.
        return $sessionManager->loggedIn();
    }
    
    /**
     * @param string $message
     */
    public function displayAccessDenied()
    {
        return $this->render(':Globals:access_denied.html.twig');
    }
    
    /**
     * 
     * @param string $templatePath
     * @param array $parameters
     * @return Response
     */
    protected function renderTemplate($templatePath, array $parameters = array())
    {
        return $this->render($templatePath, $parameters);
    }
    
    public function getSitePath()
    {
        $request = $this->getRequest();
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    }
}
