<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FitModelSpecsController extends Controller {

    public function createNewAction(){        
         return $this->render('LoveThatFitAdminBundle:FitModelSpecs:create_new.html.twig');        
    }
 
}
