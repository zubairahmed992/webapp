<?php

namespace LoveThatFit\UserBundle\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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

    //------------------------- Login after registration------------------------------
    
    public function getLoggedIn($userEntity)
    {
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);    
    }
    
    //------------------------- These methods will likely to be moved somewhere on refactoring------------------------------
            private function isDuplicateUserName($username)
            {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('LoveThatFitUserBundle:User')->findByUsername($username);
                return $entity ? true : false;                
            }
            
               private function isDuplicateEmail($id, $email)
            {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
                
                //$entity = $em->getRepository('LoveThatFitUserBundle:User')->findByEmail($email);
                return $entity? true : false;                
            }
    
//--------------------------STEP-1-----------------------------------------------
      
        
    public function stepOneCreateAction() {
        try {
            $entity = new User();
            $form = $this->createForm(new UserType(), $entity);
            $form->bind($this->getRequest());

            if ($form->isValid()) {

                if ($this->isDuplicateUserName($entity->getUsername())){
                 $form->get('username')->addError(new FormError('User name already taken'));
                    //$form->addError(new FormError('User name already taken.'));
                return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                                'form' => $form->createView(),
                                'entity' => $entity));
                }
                else    {
                $entity->setCreatedAt(new \DateTime('now'));
                $entity->setUpdatedAt(new \DateTime('now'));

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $measurement = new Measurement();
                $measurement->setUser($entity);

                //Login after registration
                $this->getLoggedIn($entity);

                $form = $this->createForm(new RegistrationStepTwoType(), $entity);
                return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
                }
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
                
            //return new Response($e->getMessage());           
            $form->addError(new FormError('User cannot be created'));
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
      

        $measurement = new Measurement(); 
        $measurement->setUser($entity);
                
        $form = $this->createForm(new RegistrationStepTwoType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {       
              // -------------- check for duplicate email??????????????????????????
            //---------------------- Need to work here ------------------------
            $em->persist($entity);            
            $em->flush();

            $measurement = $entity->getMeasurement();
            if (!$measurement){
                $measurement = new Measurement();
            }
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
    
    
     //------------------------render step two, Name should change to edit
    public function stepTwoRenderAction($id) {
        
       $em = $this->getDoctrine()->getManager();
       $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        
       $form = $this->createForm(new UserType(), $entity);

       $form = $this->createForm(new RegistrationStepTwoType(), $entity);
       
       return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity));
    }

    
//------------------------render step three, Name should change to edit
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

    
    //------------------------render step four, Name should change to edit
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

     public function getStepTestAction() {         
        return new Response($this->isDuplicateEmail(47, 'farooq2@cs.com')?'true':'false');
         //return new Response($this->isDuplicateUserName('farooq3')?'got it already':'naaaah');

    }
    
    
    
    
}








?>