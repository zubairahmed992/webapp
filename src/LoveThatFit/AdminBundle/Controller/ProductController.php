<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function indexAction()
    {
        $products = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Product')
         ->findAll();
        
        return $this->render('LoveThatFitAdminBundle:Product:index.html.twig', array('products'=> $products));
    }
    
    public function showAction($id)
    {
        $product = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:Product')
         ->findOneById($id);
        
        return $this->render('LoveThatFitAdminBundle:Product:show.html.twig', array('product'=> $product));
    }
}



