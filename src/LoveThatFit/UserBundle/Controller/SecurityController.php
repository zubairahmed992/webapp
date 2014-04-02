<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;

class SecurityController extends Controller {

    
        


    
    public function loginAction() {
        
        $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
       
        return $this->render(
                        'LoveThatFitUserBundle:Security:login.html.twig', array(
                        'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    
                        )
        );
    }

//-------------------------------------------------------------------------

    public function AdminloginAction() {
        $security_context = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
        return $this->render(
                        'LoveThatFitUserBundle:Security:adminLogin.html.twig', array(
                    'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    
                        )
        );
    }

//---------------------------------------------------------------------------------
    public function goSecureAction($id) {
        $user=$this->get('user.helper.user')->find($id);
        $user=$this->get('user.helper.user')->getLoggedIn($user);
        return $this->redirect($this->generateUrl('inner_site_index'));
    }

//---------------------------------- Forgot Password -----------------------------------------------


    public function forgotPasswordFormAction() {

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'email')
                ->getForm();

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            $data = $form->getData();

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $_user = $em->getRepository('LoveThatFitUserBundle:User')->findByEmail($data['email']);
                
                if ($_user) {
                    //updating authentication token 
                    $_user->generateAuthenticationToken();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($_user);
                    $em->flush();


                    $baseurl = $this->getRequest()->getHost();

                    $link = $baseurl.$this->generateUrl('forgot_password_reset_form', array('email_auth_token' => $_user->getAuthToken()));

                    $defaultData = $this->get('mail_helper')->sendPasswordResetLinkEmail($_user, $link);

                    $msg = "";

                    if ($defaultData[0]) {
                        $msg = " Email has been sent with reset password link to " . $_user->getEmail();
                    } else {
                        $msg = " Email not sent due to some problem, please try again later.";
                    }
                    return $this->render('LoveThatFitUserBundle:Security:forgotPasswordForm.html.twig', array(
                               'form' => $form->createView(), "defaultData" => $msg,
                            ));
                } else {
                    if($data['email']==null)
                    {
                    $msg = "Enter your email address";
                    return $this->render('LoveThatFitUserBundle:Security:forgotPasswordForm.html.twig', array(
                              'form' => $form->createView(),  "defaultData" => $msg,));
                    }else
                    {
                        $msg = "email address not found.";
                    return $this->render('LoveThatFitUserBundle:Security:forgotPasswordForm.html.twig', array('form' => $form->createView(),
                                "defaultData" => $msg,));
                    }
                }
            }
            return $this->render('LoveThatFitUserBundle:Security:forgotPasswordForm.html.twig', array(
                        'form' => $form->createView(), "defaultData" => $defaultData));
        } else {

            return $this->render('LoveThatFitUserBundle:Security:forgotPasswordForm.html.twig', array(
                        'form' => $form->createView()));
        }
    }

//---------------------------------------------------------------------------------
    public function forgotPasswordResetFormAction($email_auth_token) {

        $em = $this->getDoctrine()->getManager();

        $_user = $em->getRepository('LoveThatFitUserBundle:User')->loadUserByAuthToken($email_auth_token);

        if ($_user) {
            $defaultData = array('message' => 'Enter your email address');
            $form = $this->createFormBuilder($defaultData)
                    ->add('password', 'repeated', array(
                        'first_name' => 'password',
                        'second_name' => 'confirm',
                        'type' => 'password',
                        'invalid_message' => 'The password fields must match.',
                    ))
                    ->getForm();
            return $this->render('LoveThatFitUserBundle:Security:forgotPasswordResetForm.html.twig', array(
                        'form' => $form->createView(), 'entity' => $_user));
        } else {
            return $this->redirect($this->generateUrl('login'));
        }
    }

    //---------------------------------------------------------------------------------



    public function forgotPasswordUpdateAction(Request $request, $id) {

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('password', 'repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->getForm();
        try {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Authentication expired or link not found.');
            }
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $entity->setUpdatedAt(new \DateTime('now'));

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($data['password'], $entity->getSalt());
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $defaultData = " Your Password has been changed, please login with the new password..";

                return $this->render('LoveThatFitSiteBundle:Home:message.html.twig', array(
                            "message" => $defaultData));
            } else {

                return $this->render('LoveThatFitUserBundle:Security:forgotPasswordResetForm.html.twig', array(
                            'form' => $form->createView(), 'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Something went wrong.'));
            return $this->render('LoveThatFitUserBundle:Security:forgotPasswordResetForm.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }
    
    
    public function lostUserAccountAction()
    {
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'email')
                ->getForm();
        return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array(
                        'form' => $form->createView(),
                        ));
        
    }
    
    public function lostUserAccountFormAction(Request $request)
    {
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'email')
                ->getForm();
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            $data = $form->getData();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $_user = $em->getRepository('LoveThatFitUserBundle:User')->findByEmail($data['email']);                
                if ($_user) {                     
                $secretform = $this->createFormBuilder($defaultData)
                ->add('secretAnswer', 'text')
               ->add('email', 'hidden',array(
                'data' => $data['email']))
                ->getForm();
               return $this->render('LoveThatFitUserBundle:Security:secretQuestionAnswerForm.html.twig', array(
                              'secretForm' => $secretform->createView(),  "secretQuestion" => $_user->getSecretQuestion(),));
                } else {
                    if($data['email']==null)
                    {
                    $msg = "Enter your email address";
                    return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array(
                              'form' => $form->createView(),  "defaultData" => $msg,));
                    }else
                    {
                    $msg = "email address not found.";
                    return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array('form' => $form->createView(),
                                "defaultData" => $msg,));
                    }
                }
            }
            return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array(
                        'form' => $form->createView(), "defaultData" => $defaultData));
        } else {

            return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array(
                        'form' => $form->createView()));
        }
        
     
        
    }
    
    
     public function secretQuestionAnswerAction(Request $request)
      {         
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'email')
                ->getForm();         
          
          if ($this->getRequest()->getMethod() == 'POST') {      
          $defaultData1 = array('message' => 'Enter your answer');
          $secretform = $this->createFormBuilder($defaultData1)
                ->add('secretAnswer', 'text')
                ->add('email', 'hidden')
                ->getForm();
            $secretform->bindRequest($this->getRequest());
            $data = $secretform->getData();            
            if ($secretform->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $_user = $em->getRepository('LoveThatFitUserBundle:User')->findByEmail($data['email']);                
           if ($_user) {
               if($data['secretAnswer']==$_user->getSecretAnswer())
               { 
                    $_user->generateAuthenticationToken();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($_user);
                    $em->flush();
                    $baseurl = $this->getRequest()->getHost();
                    $link = $baseurl.$this->generateUrl('forgot_password_reset_form', array('email_auth_token' => $_user->getAuthToken()));
                    $defaultData = $this->get('mail_helper')->sendPasswordResetLinkEmail($_user, $link);
                    $msg = "";
                  if ($defaultData[0]) {
                        $msg = " Email has been sent with reset password link to " . $_user->getEmail();
                    } else {
                        $msg = " Email not sent due to some problem, please try again later.";
                    }
                    return $this->render('LoveThatFitUserBundle:Security:lostAccountForm.html.twig', array(
                               "defaultData" => $msg,
                            ));
                }
                else
           {
                $msg = "Wrong answer please write correct answer" ;
               return $this->render('LoveThatFitUserBundle:Security:secretQuestionAnswerForm.html.twig', array(
                              'secretForm' => $secretform->createView(),  "secretQuestion" => $_user->getSecretQuestion(),"defaultData" => $msg,));
           }
           }
                
            }
       
      }
    
 }
    
    
    

}
