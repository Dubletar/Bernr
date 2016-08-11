<?php

namespace SessionBundle\Service;

use Doctrine\ORM\EntityManager;
use SessionBundle\Entity\UserSession;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Session\Session;
use UserBundle\Entity\User;

class SessionManager
{
    const USER_LOGGED_IN = 'UserLoggedIn';
    
    /**
     *
     * @var array
     */
    protected static $sessionTimes = array(
        'default' => 0, // Close session when browser closes
        'remember' => 31536000 // 1 year
    );

    /**
     * 
     * @var Container
     */
    protected $container;
    
    /**
     *
     * @var EntityManager
     */
    protected $entityManager;
    
    /**
     * 
     * @var Session
     */
    protected $session;
    
    /**
     * 
     * @param Container $container
     */
    public function __construct(Container $container, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->session = $this->container->get('session');
        $this->entityManager = $entityManager;
    }
    
    protected function activateSession()
    {
        // Check that the session is started. If not, start it.
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }
    
    /**
     * Invalidates old session.
     */
    protected function createNewSession($time = null)
    {
        $this->session->invalidate($time);
        
        // Grab and set the new session.
        $this->getAndSetSession();
    }
    
    public function logUserIn(User $user, $ipAddress, $remember = false)
    {
        // Determine the amount of time given to the new Session
        $newSessionTime = self::$sessionTimes[($remember === true ? 'remember' : 'default')]; 
        
        // Invalidate the session
        $this->createNewSession($newSessionTime);
        
        // Activate the session
        $this->activateSession();
        
        // Log the user session
        $this->logUserSession($user, $ipAddress);
    }
    
    protected function logUserSession(User $user, $ipAddress)
    {
        // Create new user session object
        $userSession = new UserSession();
        $userSession->setUserId($user);
        $userSession->setSessionId($this->session->getId());
        $userSession->setIpAddress($ipAddress);
        
        $this->entityManager->persist($userSession);
        $this->entityManager->flush();
    }
    
    /**
     * Grabs, sets, and returns new session.
     * @return Session
     */
    protected function getAndSetSession()
    {
        $this->session = $this->container->get('session');
        return $this->session;
    }
    
    public function pingUserSession($ipAddress)
    {
        // Start the session if it has not started already.
        $this->activateSession();
        
        // Check if there's a UserSession with
        // - current session Id
        // - matching IP Address
        // - valid
        $userSession = $this->entityManager->getRepository("SessionBundle:UserSession")
            ->findOneBy(array('valid' => 1, 'sessionId' => $this->session->getId(), 'ipAddress' => $ipAddress));
        
        if ($userSession) {
            $this->setSessionDataIfNotExist($userSession);
        }
    }
    
    protected function setSessionDataIfNotExist(UserSession $userSession)
    {
        // Only continue if we have NOT set session data for the user.
        if (!$this->session->get(self::USER_LOGGED_IN)) {
            
            $this->session->set('user', $userSession->getUserId());
            $this->session->set(self::USER_LOGGED_IN, true);
        }
    }
    
    public function loggedIn()
    {
        if ($this->session->get(self::USER_LOGGED_IN)) {
            
            // Grab the user session
            $userSession = $this->entityManager->getRepository('SessionBundle:UserSession')
                ->findOneBy(array('id' => $this->session->get('user')));
            
            if ($userSession) {
                return $userSession->getUserId();
            }
        }
        
        return;
    }
}