<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementType;
use LoveThatFit\UserBundle\Form\Type\ProfileSettingsType;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\AdminBundle\Entity\SurveyQuestion;
use LoveThatFit\AdminBundle\Entity\SurveyAnswer;

class ProfileController extends Controller {

    public function aboutMeAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new ProfileMeasurementType(), $measurement);
       
        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'validation_groups' => array('profile_measurement'),
                    'measurement' => $measurement,
                    
                ));
    }

    //-------------------------------------------------------------
    public function aboutMeUpdateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();

        $measurementForm = $this->createForm(new ProfileMeasurementType(), $measurement);
        $measurementForm->bind($this->getRequest());
       
        if($measurementForm->isValid())
        {
        
        $measurement->setUpdatedAt(new \DateTime('now')); 
        $em->persist($measurement);
        $em->flush();
        $this->get('session')->setFlash('Success', 'Your measurement information has been saved.');
        }
       
         return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'measurement' => $measurement,
                    
                ));   
       
             
  
             
       }

    //-------------------------------------------------------------

    public function accountSettingsAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $entity);
        
        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForm->createView(),
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));
    }

    //-------------------------------------------------------------

    public function accountSettingsUpdateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $userForm->bind($this->getRequest());
        
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $entity);
        
        if ($userForm->isValid())
        {
            $entity->uploadAvatar();
            $em->persist($entity);
            $em->flush(); 
            $this->get('session')->setFlash('Success', 'Profile has been updated.');    
        }
        
        
      return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForm->createView(),
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));  
    
            
    }

//-------------------------------------------------------------------------
    
    
    public function passwordResetUpdateAction(Request $request) {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        
        $user_old_password = $entity->getPassword();
        $salt_value_old = $entity->getSalt();

        $userForm = $this->createForm(new UserPasswordReset(), $entity);
        $userForm->bind($request);
        $data = $userForm->getData();
        
        $oldpassword = $data->getOldpassword();
        
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($entity);
        $password_old_enc = $encoder->encodePassword($oldpassword, $salt_value_old);
        
        if ($user_old_password == $password_old_enc) {
        
            $em->persist($entity);
            $em->flush();
            
            if ($userForm->isValid()) {

                $data = $userForm->getData();
                $password = $data->getPassword();
                $salt_value = $entity->getSalt();
                
                $entity->setUpdatedAt(new \DateTime('now'));
            
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($password, $salt_value);


                $entity->setPassword($password);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                
                $this->get('session')->setFlash('success', 'Password Updated Successfully');
                
                } 
                else
                {
                $this->get('session')->setFlash('warning', 'Confirm pass doesnt match');
                }
           }
           else 
           {
            $this->get('session')->setFlash('warning', 'Please Enter Correct Password');
           
        }
     return $this->redirect($this->getRequest()->headers->get('referer'));      
    }
    

    //--------------------------- What I like --------------------------
    public function whatILikeAction()
    {       
        return $this->render('LoveThatFitUserBundle:Profile:whatILike.html.twig', array(
                    'data' =>  $this->getQuestionsList(),                   
                    
        ));
    }
    
     private function getQuestionsList() {
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyQuestion');
        return $repository->findAll();
    }

  
    
    
    
    
    

}

?>