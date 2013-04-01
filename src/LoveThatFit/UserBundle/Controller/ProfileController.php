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

        $em->persist($measurement);
        $em->flush();
        
        return new Response('updated');
        
        

            
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
                    'form_password_reset' => $passwordResetForm->createView(),
                    ));
        
        }
        
       
        //-------------------------------------------------------------
     
        public function accountSettingsUpdateAction() {
            
        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new UserPasswordReset(), $entity);
        $userForm ->bind($this->getRequest());

        $em->persist($entity);
        $em->flush();
        return new Response('lay giyaeen oey...');
        }
        
      /***************************************************
       * Created: Suresh
       * Description: Password Reset method
       * param :Form password data, id
      ******************************************************/
       
        public function passwordResetUpdateAction(Request $request) {
       
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        $userForm = $this->createForm(new UserPasswordReset(), $entity);
        $userForm ->bind($request);
        $em->persist($entity);
        $em->flush();
         
        if ($userForm->isValid()) {
                $data = $userForm->getData();
                $password=$data->getPassword();
                $entity->setUpdatedAt(new \DateTime('now'));
                $factory = $this->get('security.encoder_factory');
                $salt_value=$entity->getSalt();
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($password,$salt_value);
                
               
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
        
      return new Response('lay giyaeen oey...');

        
        }
    }
}

?>