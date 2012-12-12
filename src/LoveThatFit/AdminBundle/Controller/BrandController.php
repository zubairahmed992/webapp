<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public function indexAction()
    {
        $entity = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Brand')
         ->findAll();
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', array('brands'=> $entity));
    }
    
    public function showAction($id)
    {
        $entity = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Brand')
         ->find($id);
         
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand.');
        }
        
        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
            'brand'=> $entity
            ));
    }
}
