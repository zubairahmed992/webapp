<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\UserType;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementType;
use LoveThatFit\UserBundle\Form\Type\ProfileSettingsType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $this->get('session')->setFlash('success', 'Updated Successfully');
        }
        else
        {
         $this->get('session')->setFlash('warning', 'Try Again');   
        }
             
  
       return $this->redirect($this->getRequest()->headers->get('referer'));      
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
        
        
        if ($userForm->isValid())
        {
            $entity->uploadAvatar();
            $em->persist($entity);
            $em->flush(); 
            $this->get('session')->setFlash('success', 'Profile  Updated Successfully');    
        }
        else
        {
            
            $this->get('session')->setFlash('warning', 'Please Try again');    
        }
        
    
      return $this->redirect($this->getRequest()->headers->get('referer'));      
    }

     /***************************************************
     * Created: Suresh
     * Description: Password Reset method
     * param :Form password data, id
     * **************************************************** */

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

}

?>