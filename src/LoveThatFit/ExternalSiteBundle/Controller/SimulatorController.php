<?php

namespace LoveThatFit\ExternalSiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SimulatorController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoveThatFitExternalSiteBundle:Simulator:index.html.twig', array('name' => '$name'));
    }
    
}
