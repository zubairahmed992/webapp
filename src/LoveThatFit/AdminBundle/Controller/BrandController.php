<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public function indexAction()
    {
        $brands = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Brand')
         ->findAll();
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', array('brands'=> $brands));
    }
    
    public function showAction($id)
    {
        $brand = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Brand')
         ->findOneById($id);
        
        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array('brand'=> $brand));
    }
}
