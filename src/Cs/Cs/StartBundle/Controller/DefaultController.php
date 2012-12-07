<?php

namespace Cs\StartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CsStartBundle:Default:index.html.twig', array('name' => $name));
    }
}
