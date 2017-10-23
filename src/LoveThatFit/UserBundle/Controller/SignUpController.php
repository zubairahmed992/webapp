<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SignUpController extends Controller {




#>>>>>>>>>>>>>>>>>>>>>> Replace with new registration >>>>>>>>>>>>>>>>>>
    public function registrationAction() {           
 
        return $this->render('LoveThatFitUserBundle:SignUp:signup.html.twig');
    }





}

?>