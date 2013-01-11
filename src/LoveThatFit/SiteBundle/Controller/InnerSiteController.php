<?php

namespace LoveThatFit\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InnerSiteController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
        return $this->render('LoveThatFitSiteBundle:InnerSite:index.html.twig');
    }

    public function productsAction() {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findAll();
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:list.html.twig', array('products' => $entity));
        //$response = new Response(json_encode(array($entity)));
        //$response->headers->set('Content-Type', 'application/json');
        //return $response;
    }
    
    public function ajaxAction() {
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');
        
    }

}

?>