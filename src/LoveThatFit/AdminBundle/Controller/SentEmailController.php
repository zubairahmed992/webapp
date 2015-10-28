<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;

class SentEmailController extends Controller {

    public function showAction($email_type, $id) {
        $userParentChilds=$this->get('user.helper.parent.child')->find($id);
        #return new Response($userParentChilds->getEmail());
        return $this->render('LoveThatFitAdminBundle::email/parent_registration.html.twig', array('entity' => $userParentChilds, 'reset_link' => ''));
    }

}
