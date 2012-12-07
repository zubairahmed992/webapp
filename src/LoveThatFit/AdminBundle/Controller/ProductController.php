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
         ->find(2);
        return new Response('<html><body>'.$products->getName().'</body></html>');
    }
}
