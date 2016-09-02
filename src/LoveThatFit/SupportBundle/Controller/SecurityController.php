<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\AdminBundle\Entity\SupportAdminUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;

class SecurityController extends Controller {

  public function loginAction() {
    $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());

    $this->get('session')->remove('Permissions');

    return $this->render(
        'LoveThatFitSupportBundle:Security:supportLogin.html.twig', array(
            'last_username' => $security_context['last_username'],
            'error' => $security_context['error'],

        )
    );
  }


    
    
    

}
