<?php

namespace LoveThatFit\ExternalSiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FittingRoomController extends Controller
{
    public function showAction() {
          return $this->render('LoveThatFitExternalSiteBundle:FittingRoom:_fitting_room.html.twig');
    }
    
    
}
