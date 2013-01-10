<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\UserType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepTwoType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepThreeType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

            $measurement = new Measurement();
            $form = $this->createForm(new RegistrationStepThreeType(), $measurement);
            return $this->render('LoveThatFitUserBundle:Registration:stepthree.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }

    //--------------------------STEP-3-----------------------------------------------
   
    public function stepThreeCreateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        
        $form = $this->createForm(new RegistrationStepThreeType(), $measurement);
        $form->bind($request);
        if ($form->isValid()) {
            
            $measurement->setCreatedAt(new \DateTime('now'));
            $measurement->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($measurement);
            $em->flush();
            
            $form = $this->createForm(new RegistrationStepFourType(), $user);
            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $user));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:stepthree.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $user));
        }
    }

//--------------------------STEP-4-----------------------------------------------
      public function stepFourCreateAction(Request $request, $id) {

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

            $response= new Response(json_encode(array(
            'entity' => $entity,
            'imageurl' => $entity->getWebPath()
                    )));
                $response->headers->set('Content-Type', 'application/json');
            return $response;
                    
        } else {
            $response="Invalid image data";
            return new Response($response);  
        }
    }
    //--------------------------------- for image Cropping & resizing.
      public function stepFourImageUpdateAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $id=$_POST['id'];
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        
        $data = substr($_POST['imageData'], strpos($_POST['imageData'], ",") + 1);
        $decodedData = base64_decode($data);
        $fp = fopen($entity->getAbsolutePath(), 'wb');
        fwrite($fp, $decodedData);
        fclose($fp);
        $response="Image has been updated.";
        return new Response($response);  
                    
       
    }
    

    
    //------------------ Test -----------------------------
    //------------------ Test -----------------------------
    //------------------ Test -----------------------------
    
    
    public function stepTwoRenderAction($id) {
        
       $em = $this->getDoctrine()->getManager();
       $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        
       $form = $this->createForm(new UserType(), $entity);

       $form = $this->createForm(new RegistrationStepTwoType(), $entity);
       return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity));
    }

    
    
    
    
    
     public function stepThreeRenderAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        
        $form = $this->createForm(new RegistrationStepThreeType(), $measurement);
        
        
            return $this->render('LoveThatFitUserBundle:Registration:stepthree.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $user));
        
    }
    
    
    
    
    
    public function stepFourRenderAction($id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        
        
            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        
    }

    
    
    
    
    
}








?>