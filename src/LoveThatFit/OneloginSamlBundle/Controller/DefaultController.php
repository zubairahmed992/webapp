<?php

namespace LoveThatFit\OneloginSamlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoveThatFitOneloginSamlBundle:Default:index.html.twig', array('name' => $name));
    }
}
