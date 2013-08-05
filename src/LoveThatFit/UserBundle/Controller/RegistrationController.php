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


    public function registrationAction() {
        $request=$this->getRequest();
        $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
       
        $referer = $request->headers->get('referer');
        $url_bits = explode('/', $referer);
        $security_context['referer'] = $url_bits[sizeof($url_bits) - 1];

        if (array_key_exists ('error', $security_context) and $security_context['error']) {
            $security_context['referer']= "login";
        }
        
        $entity = $this->get('user.helper.user')->createNewUser();
        $form = $this->createForm(new RegistrationType(), $entity);

        return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView(),
                    'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    'referer' => $security_context['referer'],
                ));
    }

//----------------------------------------------------------------

    public function registrationCreateAction() {
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $user_helper = $this->get('user.helper.user');
        try {
            $entity = new User();
            $form = $this->createForm(new RegistrationType(), $entity);
            $form->bind($this->getRequest());

            if ($user_helper->isDuplicateEmail(Null, $entity->getEmail())) {
                $form->get('email')->addError(new FormError('This email address has already been taken.'));
            }

            if ($form->isValid()) {
                $entity = $user_helper->registerUser($entity);

                /*
                  $entity->setCreatedAt(new \DateTime('now'));
                  $entity->setUpdatedAt(new \DateTime('now'));

                  $factory = $this->get('security.encoder_factory');
                  $encoder = $factory->getEncoder($entity);
                  $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                  $entity->setPassword($password);
                  $entity->generateAuthenticationToken();
                  $em = $this->getDoctrine()->getManager();
                  $measurement = new Measurement();
                  $entity->setMeasurement($measurement);


                  $em->persist($entity);
                  $em->flush();
                 */

                // Adding Measurement record for the user for the later usage

                /* $em->persist($entity);
                  $em->flush();

                  // Adding Measurement record for the user for the later usage
                  $measurement = new Measurement();
                  $measurement->setUser($entity);
                  $em->persist($measurement);
                  $em->flush();
                 */
                //Login after registration, the rest of the steps are secured for logged In users access only

                $measurement = $entity->getMeasurement();
                
                //$this->getLoggedIn($entity);
                $this->get('user.helper.user')->getLoggedInById($entity);
               
                //send registration email ....            
                $this->get('mail_helper')->sendRegistrationEmail($entity);

                if ($entity->getGender() == 'm') {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
                } else {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
                }

                return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                            'form' => $registrationMeasurementform->createView(),
                            'measurement' => $measurement,
                            'entity' => $entity,
                        ));
            } else {

                $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
                return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity,
                            'last_username' => $security_context['last_username'],
                            'error' => $security_context['error'],
                            'referer' => 'registration',
                        ));
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
        $user_helper = $this->get('user.helper.user');
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $em = $this->getDoctrine()->getManager();
        $entity = $user_helper->find($id);

        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
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

        #-------------Evalutae Size Chart From Size Chart Helper ----------------------#
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $measurement = $size_chart_helper->evaluateWithSizeChart($measurement);
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

        $size_chart_helper = $this->get('admin.helper.sizechart');
        $em = $this->getDoctrine()->getManager();
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);
        $measurement_helper = $this->get('measurement.helper.measurement');
        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {

            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
        }

        $retaining_array = $measurement_helper->measurementRetain($measurement);

        return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                    'top_brand_id' => $retaining_array['top_brand_id'],
                    'top_size_chart_id' => $retaining_array['topSizeChartId'],
                    'bottom_brand_id' => $retaining_array['bottom_brand_id'],
                    'bottom_size_chart_id' => $retaining_array['bottomSizeChartId'],
                    'dress_brand_id' => $retaining_array['dress_brand_id'],
                    'dress_size_chart_id' => $retaining_array['dressSizeChartId'],
                ));
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//---------------------------------- Image upload STEP ---------------------------------------------------
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //------------------------ Render Fitting room image upload page

    public function stepFourEditAction() {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $user_helper->getMeasurement($entity);

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
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $user_helper->getMeasurement($entity);

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
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $image_path = "";
            if ($entity->getImage()) {
                $image_path = $entity->uploadTempImage();
            } else {
                $entity->upload();
                $em->persist($entity);
                $em->flush();
                $image_path = $entity->getWebPath();
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
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);

        $measurement = $user_helper->getMeasurement($user);

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
        $user_helper = $this->get('user.helper.user');
        $entity = $user_helper->find($id);

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
#-----------------------------Helper Initialize-------------------------------------------------------------#        
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $user = $this->get('user.helper.user');
        $measurement_helper = $this->get('measurement.helper.measurement');
#----------------------------End Of Helper Intialization-----------------------------------------------------#        
        $entity = $user->find($id);
        $measurement = $entity->getMeasurement();


        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper->getBrandArray('Top'), $size_chart_helper->getBrandArray('Bottom'), $size_chart_helper->getBrandArray('Dress')), $measurement);
        }

        $retaining_array = $measurement_helper->measurementRetain($measurement);
        return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                    'top_brand_id' => $retaining_array['top_brand_id'],
                    'top_size_chart_id' => $retaining_array['topSizeChartId'],
                    'bottom_brand_id' => $retaining_array['bottom_brand_id'],
                    'bottom_size_chart_id' => $retaining_array['bottomSizeChartId'],
                    'dress_brand_id' => $retaining_array['dress_brand_id'],
                    'dress_size_chart_id' => $retaining_array['dressSizeChartId'],
                ));
    }

}








//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------

//------------------------- Login after registration------------------------------

   /* private function getLoggedIn($userEntity) {
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);
    }
//-------------------------------------------------------------------------------------
//    private function isDuplicateEmail($id, $email) {
    //      return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    // }
//------------------------------ Get Measurement Entity -----------------------

     private function getMeasurement($user) {
      $measurement = $user->getMeasurement();

      if (!$measurement) {
      $measurement = new Measurement();
      $measurement->setUser($user);
      }
      return $measurement;
      }
   

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


      //$form = $this->createForm(new UserPasswordChangeType(), $entity);

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
     */


?>