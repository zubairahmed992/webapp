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
         ->find(2);
        return new Response('<html><body>'.$clothing_types->getName().'</body></html>');
    }
}
