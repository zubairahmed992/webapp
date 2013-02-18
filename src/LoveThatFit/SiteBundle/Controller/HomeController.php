<?php

namespace LoveThatFit\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
        return $this->render('LoveThatFitSiteBundle:Home:index.html.twig');
    }
public function aboutUsAction() {
        return $this->render('LoveThatFitSiteBundle:Home:about_us.html.twig');
    }

public function contactUsAction() {
        return $this->render('LoveThatFitSiteBundle:Home:contact_us.html.twig');
    }    
    
    
    
    
}








?>