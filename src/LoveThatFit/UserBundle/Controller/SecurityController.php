<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController extends Controller {

    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
                        'LoveThatFitUserBundle:Security:login.html.twig', array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                        )
        );
    }

//-------------------------------------------------------------------------

    public function AdminloginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }


        return $this->render(
                        'LoveThatFitUserBundle:Security:adminLogin.html.twig', array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                        )
        );
    }

    public function goSecureAction($id) {
        $em = $this->getDoctrine()->getManager();
        $userEntity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);

        $user = $token->getUser();

        //return new Response($session->get(SecurityContext::USERNAME));

        return new Response($user->getUserName());
    }

    public function forgotPasswordAction() {
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'email', array('constraints' => array(new NotBlank())))
                ->getForm();

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $data = $form->getData();
                return new Response("your email is ..." . $this->get('request')->request->get('email'));
            } else {
                return $this->render('LoveThatFitUserBundle:Security:forgotPassword.html.twig', array(
                            'form' => $form->createView()));
            }
        }

        return $this->render('LoveThatFitUserBundle:Security:forgotPassword.html.twig', array(
                    'form' => $form->createView()));
    }

    public function resetPasswordAction()
    {
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
        
        ->add('password', 'repeated', array(
            'first_name' => 'password',
            'second_name' => 'confirm',
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
        ))
                 ->getForm();
         return $this->render('LoveThatFitUserBundle:Security:passwordReset.html.twig', array(
                    'form' => $form->createView()));
        
    }
}
