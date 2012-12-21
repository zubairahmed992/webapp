<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Form\Type\UserType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepTwoType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller {

    //-------------------------------------------------------------------------

    public function stepOneAction() {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                    'form' => $form->createView()));
    }

    
    
//--------------------------STEP-1-----------------------------------------------

    public function stepOneCreateAction() {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $form = $this->createForm(new RegistrationStepTwoType(), $entity);
            return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                        'form' => $form->createView(),
                    'entity' => $entity));
        } else {
            
           return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity));
        }
    }

    
    
    
 //--------------------------STEP-2-----------------------------------------------
    public function stepTwoCreateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepTwoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                            'entity' => $entity));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                        'form' => $form->createView(),
                            'entity' => $entity));
        }
    }

    
    
    
    
    
    //--------------------------STEP-3-----------------------------------------------
    public function stepThreeCreateAction() {
        $entity = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->find(7);
        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity));
    }
    
    
    
    
    
//--------------------------STEP-4-----------------------------------------------
     public function stepFourCreateAction(Request $request, $id) 
     {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
             
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();

            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                    'entity' => $entity));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                    'entity' => $entity));
        }
     }
}

?>