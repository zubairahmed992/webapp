<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelfieShareFeedbackController extends Controller {

    public function createAction() {
        return new Response('create');
    }

    #----------------------------------------------

    public function editAction() {
        
          return $this->render('LoveThatFitUserBundle:SelfieshareFeedback:edit.html.twig');
        return new Response('edit');
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