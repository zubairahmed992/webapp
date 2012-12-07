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
         ->find(1);
        return new Response('<html><body>'.$brands->getName().'</body></html>');
    }
}
