<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ClothingTypeController extends Controller
{
    public function indexAction()
    {
        $clothing_types = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:ClothingType')
         ->findAll();
        return $this->render('LoveThatFitAdminBundle:ClothingType:index.html.twig', array('clothing_types'=> $clothing_types));
    }
    
    public function showAction($id)
    {
        $clothing_type = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:ClothingType')
         ->findOneById($id);
        
        return $this->render('LoveThatFitAdminBundle:ClothingType:show.html.twig', array('clothing_type'=> $clothing_type));
    }
}
