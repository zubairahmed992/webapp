<?php

namespace LoveThatFit\ExternalSiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoveThatFitExternalSiteBundle:Default:index.html.twig', array('name' => $name));
    }
}
