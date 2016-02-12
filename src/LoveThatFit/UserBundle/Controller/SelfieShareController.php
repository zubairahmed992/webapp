<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelfieShareController extends Controller {

   

    public function feedbackEditAction($ref) {
        #return new Response($ref);
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_edit.html.twig');        
    }

    #----------------------------------------------

    public function updateAction() {
        return new Response('update');
    }

    #----------------------------------------------

    public function showAction() {
        return new Response('show');
    }

}

?>