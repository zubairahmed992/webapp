<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementFemaleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;


class RegistrationController extends Controller {

//------------------------- Password reset ------------------------------------------------

    public function passwordResetFormAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('password', 'repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->getForm();
        return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                    'form' => $form->createView(), 'entity' => $entity));
    }

    //---------------------------------------------------------------------------------
    public function passwordUpdateAction(Request $request, $id) {

        $defaultData = array('message' => 'Update password');
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

                return $this->render('LoveThatFitUserBundle:Registration:message.html.twig', array(
                            'message' => 'Your password has been changed.'));
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                            'form' => $form->createView()));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Something went wrong.'));
            return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                        'form' => $form->createView()));
        }
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    
//------------------------- Registration Process ------------------------------------------
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    


    public function registrationAction() {

        //------------------   LOgin form
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

//------------------------ Registration form
        $entity = $this->get('user.helper.user')->createNewUser();
        $form = $this->createForm(new RegistrationType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView(),
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                ));
    }

//----------------------------------------------------------------

    public function registrationCreateAction() {
        try {
            $entity = new User();
            $form = $this->createForm(new RegistrationType(), $entity);
            $form->bind($this->getRequest());

            if ($this->isDuplicateEmail(Null, $entity->getEmail())) {
                $form->get('email')->addError(new FormError('This email address has already been taken.'));
            }


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

                // Adding Measurement record for the user for the later usage
                $measurement = new Measurement();
                $measurement->setUser($entity);
                $em->persist($measurement);
                $em->flush();

                //Login after registration, the rest of the steps are secured for logged In users access only
                $this->getLoggedIn($entity);

                //send registration email ....            
                $this->get('mail_helper')->sendRegistrationEmail($entity);

                if ($entity->getGender() == 'm') {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
                } else {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
                }
               
                return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                            'form' => $registrationMeasurementform->createView(),
                            'measurement' => $measurement,
                            'entity' => $entity,
                           
                    
                    ));
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Some thing went wrong'));

            return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }

//--------------------------------------------------------------------------------
    public function measurementCreateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        }
        $registrationMeasurementform->bind($this->getRequest());

        $request_array = $this->getRequest()->get('measurement');

        if ($entity->getGender() == 'm') {

            if (array_key_exists('top_size', $request_array)) {
                $measurement->top_size = $request_array['top_size'];
            }
            if (array_key_exists('bottom_size', $request_array)) {
                $measurement->bottom_size = $request_array['bottom_size'];
            }
        } else {

            if (array_key_exists('top_size', $request_array)) {
                $measurement->top_size = $request_array['top_size'];
            }
            if (array_key_exists('bottom_size', $request_array)) {
                $measurement->bottom_size = $request_array['bottom_size'];
            }
            if (array_key_exists('dress_size', $request_array)) {
                $measurement->dress_size = $request_array['dress_size'];
            }
        }

        //--------------------------------
        $measurement = $this->evaluateWithSizeChart($measurement);
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $em->persist($measurement);
        $em->flush();

        // Rendering step four
        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $entity,
                    'measurement' => $measurement,
                    'edit_type' => 'registration',
                ));
    }
//-----------------------------------------------------------------------------
 public function measurementEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        }
         #---Suresh Code---------#
        $top_size_chart=$measurement->getTopFittingSizeChart();
        $bottom_size_chart=$measurement->getBottomFittingSizeChart();
        $dress_size_chart=$measurement->getDressFittingSizeChart();
        #---Getting the Top Size Chart --------#
        if ($top_size_chart) {
            $em = $this->getDoctrine()->getManager();
            $topSizeChartId=$top_size_chart->getId();
            $top_brand=$top_size_chart->getBrand();
            $top_brand_id=$top_brand->getId();
        }else{
            $top_brand_id=Null;
            $topSizeChartId=Null;
        }    
     #---Getting The Bottom Size Chart--------#   
        if($bottom_size_chart){
            $bottomSizeChartId=$bottom_size_chart->getId();
            $bottom_brand=$bottom_size_chart->getBrand();
            $bottom_brand_id=$bottom_brand->getId();
        }else{
            $bottom_brand_id=Null;
            $bottomSizeChartId=Null;
        }
        
    #---Getting The Dress Size Chart-----------#
        if($dress_size_chart){
            $dressSizeChartId=$dress_size_chart->getID();
            $dress_brand=$dress_size_chart->getBrand();
            $dress_brand_id=$dress_brand->getId();
        }else{
            $dress_brand_id=Null;
            $dressSizeChartId=Null;
        }
       
     #--End Of Suresh Code---------------------#
return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                    'top_brand_id'=>$top_brand_id,
                    'top_size_chart_id'=>$topSizeChartId,
                    'bottom_brand_id'=>$bottom_brand_id,
                    'bottom_size_chart_id'=>$bottomSizeChartId,
                    'dress_brand_id'=>$dress_brand_id,
                    'dress_size_chart_id'=>$dressSizeChartId,
            ));
  
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//---------------------------------- Image upload STEP ---------------------------------------------------
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //------------------------ Render Fitting room image upload page

    public function stepFourEditAction() {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $this->getMeasurement($entity);

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $entity,
                    'measurement' => $measurement,
                    'edit_type' => 'registration',
                ));
    }
    
#---------------------------Profile Edit Image ---------------#
public function fittingRoomImageEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $this->getMeasurement($entity);

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $entity,
                    'measurement' => $measurement,
                    'edit_type' => 'fitting_room',
                ));
    }    

//--------------------------- update fitting room image, 
    public function stepFourCreateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $image_path="";
            if($entity->getImage()){
                $image_path=$entity->uploadTempImage();
            }else{
                $entity->upload();
                $em->persist($entity);
                $em->flush();
                $image_path=$entity->getWebPath();
            }
            $response = new Response(json_encode(array(
                                'entity' => $entity,
                                //'imageurl' => $entity->getWebPath()
                                'imageurl' => $image_path
                            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $response = "Invalid image data";
            return new Response($response);
        }
    }

//-------------Updates shoulder height & outseam, input via user move sliders on image, form submit via ajax

    public function stepFourMeasurementUpdateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $this->getMeasurement($user);

        $form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($measurement);
            $em->flush();
            return new Response('Measurement Updated');
        } else {
            return new Response('Measurement has not been updated!');
        }
    }

    //--------------------------------- deals with image submitted from canvas, saves image
    public function stepFourImageUpdateAction() {

        $em = $this->getDoctrine()->getManager();
        $id = $_POST['id'];
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $entity->writeImageFromCanvas($_POST['imageData']);
        $response = "true";
        return new Response($response);
    }

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------

    public function renderThisAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        $measurement = $entity->getMeasurement();
//return new Response(var_dump($this->getBrandArray('Top')));        
        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        }
         #---Suresh Code---------#
        $top_size_chart=$measurement->getTopFittingSizeChart();
        $bottom_size_chart=$measurement->getBottomFittingSizeChart();
        $dress_size_chart=$measurement->getDressFittingSizeChart();
        
       #---Getting the Top Size Chart --------#
        if ($top_size_chart) {
            $em = $this->getDoctrine()->getManager();
            $topSizeChartId=$top_size_chart->getId();
            $top_brand=$top_size_chart->getBrand();
            $top_brand_id=$top_brand->getId();
            //$top_brand_id = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandBySizeChartId($topSizeChartId);
            //$top_brand_id=$top_brand_id[0]['brand'];
            
        }else{
            $top_brand_id=Null;
            $topSizeChartId=Null;
        }    
        
     #---Getting The Bottom Size Chart--------#   
        if($bottom_size_chart){
            $bottomSizeChartId=$bottom_size_chart->getId();
            $bottom_brand=$bottom_size_chart->getBrand();
            $bottom_brand_id=$bottom_brand->getId();
            //$em = $this->getDoctrine()->getManager();
           // $bottom_brand_id = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandBySizeChartId($BottomSizeChartId);
            //$bottom_brand_id=$bottom_brand_id[0]['brand'];
        }else{
            $bottom_brand_id=Null;
            $bottomSizeChartId=Null;
        }
        
     
    #---Getting The Dress Size Chart-----------#
        if($dress_size_chart){
            $dressSizeChartId=$dress_size_chart->getID();
            $dress_brand=$dress_size_chart->getBrand();
            $dress_brand_id=$dress_brand->getId();
            //$em = $this->getDoctrine()->getManager();
            //$dress_brand_id = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandBySizeChartId($DressSizeChartId);
            //$dress_brand_id=$dress_brand_id[0]['brand'];
        }else{
            $dress_brand_id=Null;
            $dressSizeChartId=Null;
        }
       
     #--End Of Suresh Code---------------------#

        return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                    'top_brand_id'=>$top_brand_id,
                    'top_size_chart_id'=>$topSizeChartId,
                    'bottom_brand_id'=>$bottom_brand_id,
                    'bottom_size_chart_id'=>$bottomSizeChartId,
                    'dress_brand_id'=>$dress_brand_id,
                    'dress_size_chart_id'=>$dressSizeChartId,
            ));
    }

//------------------------------------------------------------------------
//methods will be moved somewhere on refactoring ------------------------------
//------------------------------------------------------------------------

    private function getBrandArray($target) {

        $brands = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->getBrandsByTarget($target);

        $brands_array = array();
        foreach ($brands as $i) {
            $brands_array[$i['id']] = $i['name'];
        }
        return $brands_array;
    }

//------------------------- Login after registration------------------------------

    private function getLoggedIn($userEntity) {
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);
    }

//-------------------------------------------------------------------------------------
    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    }

//------------------------------ Get Measurement Entity -----------------------

    private function getMeasurement($user) {
        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        return $measurement;
    }

//-------------------------------------------------------------------------------------
    private function evaluateWithSizeChart($measurement) {

        if (is_null($measurement)) {
            return;
        }

        $bust_size = 0;
        $hip_size = 0;

        $em = $this->getDoctrine()->getManager();

        if ($measurement->top_size) {

            $top_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($measurement->top_size);

            $measurement->setTopFittingSizeChart($top_size); // set the selected size chart to the measurement table to have association

            if ($top_size) {

                if ($measurement->getNeck() == null || $measurement->getNeck() == 0) {
                    $measurement->setNeck($top_size->getNeck());
                }
                if ($measurement->getBust() == null || $measurement->getBust() == 0) {
                    $measurement->setBust($top_size->getBust());
                    $bust_size = $top_size->getBust();
                }
                if ($measurement->getChest() == null || $measurement->getChest() == 0) {
                    $measurement->setChest($top_size->getChest());
                }
                if ($measurement->getSleeve() == null || $measurement->getSleeve() == 0) {
                    $measurement->setSleeve($top_size->getSleeve());
                }
                if ($measurement->getBack() == null || $measurement->getBack() == 0) {
                    $measurement->setBack($top_size->getBack());
                }
            }
        }

        if ($measurement->bottom_size) {

            $bottom_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($measurement->bottom_size);

            $measurement->setBottomFittingSizeChart($bottom_size); // set the selected size chart to the measurement table to have association

            if ($bottom_size) {
                if ($measurement->getWaist() == null || $measurement->getWaist() == 0) {
                    $measurement->setWaist($bottom_size->getWaist());
                }
                if ($measurement->getHip() == null || $measurement->getHip() == 0) {
                    $measurement->setHip($bottom_size->getHip());
                    $hip_size = $bottom_size->getHip();
                }
                if ($measurement->getInseam() == null || $measurement->getInseam() == 0) {
                    $measurement->setInseam($bottom_size->getInseam());
                }
                if ($measurement->getBack() == null || $measurement->getBack() == 0) {
                    $measurement->setBack($bottom_size->getBack());
                }
            }
        }

        if ($measurement->dress_size) {

            $dress_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($measurement->dress_size);

            $measurement->setDressFittingSizeChart($dress_size); // set the selected size chart to the measurement table to have association

            if ($dress_size) {

                if ($measurement->getBust() == null || $measurement->getBust() == 0) {
                    $measurement->setBust($dress_size->getBust());
                } else {
                    // If user already selected a brand & size for Top take average value
                    if ($bust_size > 0 && $dress_size->getBust() > 0) {
                        $measurement->setBust(($bust_size + $dress_size->getBust()) / 2);
                    } else {//this condition will not be called as per current condition/ refactor                        
                        $measurement->setBust($dress_size->getBust());
                    }
                }

                if ($measurement->getHip() == null || $measurement->getHip() == 0) {
                    $measurement->setHip($dress_size->getHip());
                } else {
                    // If user already selected a brand & size for bottom/pant
                    if ($hip_size > 0 && $dress_size->getHip() > 0) {
                        $dress_size->getHip(($hip_size + $dress_size->getHip()) / 2);
                    } else {//this condition will not be called as per current condition/ refactor                                                
                        $measurement->setHip($dress_size->getHip());
                    }
                }

                if ($measurement->getBack() == null || $measurement->getBack() == 0) {
                    $measurement->setBack($dress_size->getBack());
                }
            }
        }

        return $measurement;
    }

}

?>