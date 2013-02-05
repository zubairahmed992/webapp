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
        return $this->render('LoveThatFitSiteBundle:Home:after_login_about_us.html.twig');
    }

public function contactUsAction() {
        return $this->render('LoveThatFitSiteBundle:Home:after_login_contact_us.html.twig');
    }    
    
    
    
    
}








?>