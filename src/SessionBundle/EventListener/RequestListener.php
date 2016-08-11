<?php

namespace SessionBundle\EventListener;

use SessionBundle\Service\SessionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{
    protected $container;
    protected $sessionManager;
    
    public function __construct(ContainerInterface $container, SessionManager $sessionManager)
    {
        $this->container = $container;
        $this->sessionManager = $sessionManager;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        
        $this->sessionManager->pingUserSession($event->getRequest()->getClientIp());
    }
}
