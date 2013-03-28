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
        
        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForm->createView(),
                    'entity' => $entity,
                    ));
        
        }
        
        
        //-------------------------------------------------------------
     
        public function accountSettingsUpdateAction() {
            
        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $userForm ->bind($this->getRequest());

        $em->persist($entity);
        $em->flush();
        
        return new Response('lay giyaeen oey...');
        
        

            
        }
       
}

?>