<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoveThatFitSupportBundle:Default:index.html.twig');
    }
}
