<?php

namespace SessionBundle\Controller;

use UtilityBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    public function indexAction($name)
    {
        return $this->render('SessionBundle:Default:index.html.twig', array('name' => $name));
    }
}
