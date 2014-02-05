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
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class RegistrationController extends Controller {

    public function registrationAction() {
        
        $request = $this->getRequest();
        $security_context = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());

        $referer = $request->headers->get('referer');
        $url_bits = explode('/', $referer);
        $security_context['referer'] = $url_bits[sizeof($url_bits) - 1];

        $routeName = $request->get('_route');


        if (array_key_exists('error', $security_context) and $security_context['error']) {
            $security_context['referer'] = "login";
        }
        if($routeName=='login'){
               $security_context['referer'] = "login"; 
        }
 
        $user = $this->get('user.helper.user')->createNewUser();
        $form = $this->createForm(new RegistrationType(), $user);

        $twitter_helper = $this->get('twitter_helper');

        $twitters = array();
        $twitters = $twitter_helper->twitter_latest();

        return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView(),
                    'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    'referer' => $security_context['referer'],
                    'twitters' => $twitters,
                ));
    }

//----------------------------------------------------------------

    public function registrationCreateAction() {
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $brandHelper=$this->get('admin.helper.brand');
        $user_helper = $this->get('user.helper.user');
        try {
            $user = new User();
           
            #---------Start of CRF Protection----------------------------------#
            if ($this->getRequest()->getMethod() == 'POST') {
           
            $form = $this->createForm(new RegistrationType(), $user);
            $form->bind($this->getRequest());

            if ($user_helper->isDuplicateEmail(Null, $user->getEmail())) {
                $form->get('email')->addError(new FormError('This email address has already been taken.'));
            }

            if ($form->isValid()) {
                $u = $user_helper->registerUser($user);
                $user = $user_helper->find($u->getId());
                $measurement = $user->getMeasurement();
                $user_helper->getLoggedInById($user);

                //send registration email ....            
                $this->get('mail_helper')->sendRegistrationEmail($user);

                if ($user->getGender() == 'm') {

                    $neck_size=$this->get('admin.helper.productsizes')->manSizeList($neck=1,$sleeve=0,$waist=0,$inseam=0);
                    $sleeve_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=1,$waist=0,$inseam=0);
                    $waist_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=1,$inseam=0);
                    $inseam_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=0,$inseam=1);
                    
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper,$neck_size,$sleeve_size,$waist_size,$inseam_size,$brandHelper), $measurement);
                } else {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper,$this->get('admin.helper.utility')->getBodyShape(),$this->get('admin.helper.utility')->getBraLetters(),$this->get('admin.helper.utility')->getBraNumbers(),$this->get('admin.helper.utility')->getBodyTypesSearching(),$brandHelper), $measurement);
                }

                return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                            'form' => $registrationMeasurementform->createView(),
                            'measurement' => $measurement,
                            'entity' => $user,
                        ));
            } else {



                $twitter_helper = $this->get('twitter_helper');
                $twitters = array();
                $twitters = $twitter_helper->twitter_latest();

                $security_context = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
                return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $user,
                            'last_username' => $security_context['last_username'],
                            'error' => $security_context['error'],
                            'referer' => 'registration',
                            'twitters' => $twitters,
                        ));
            }
            
            
            }//End of CRF Protection
            
            
            } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Some thing went wrong'));

            return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $user));
        }
    }

//--------------------------------------------------------------------------------
    public function measurementCreateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $brandHelper=$this->get('admin.helper.brand');
        $user = $this->get('user.helper.user')->find($id);
        $measurement = $user->getMeasurement();
   #---------Start OF CRF Protection--------------------------------------#
 if ($this->getRequest()->getMethod() == 'POST') {
        if ($user->getGender() == 'm') {
    $neck_size=$this->get('admin.helper.productsizes')->manSizeList($neck=1,$sleeve=0,$waist=0,$inseam=0);
    $sleeve_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=1,$waist=0,$inseam=0);
    $waist_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=1,$inseam=0);
    $inseam_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=0,$inseam=1);
    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper,$neck_size,$sleeve_size,$waist_size,$inseam_size,$brandHelper), $measurement);
   } else {
      $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper,$this->get('admin.helper.utility')->getBodyShape(),$this->get('admin.helper.utility')->getBraLetters(),$this->get('admin.helper.utility')->getBraNumbers(),$this->get('admin.helper.utility')->getBodyTypesSearching(),$brandHelper), $measurement);
        }
        $registrationMeasurementform->bind($this->getRequest());
        #-------------Evaluate Size Chart From Size Chart Helper ----------------------#
        
        $request_array = $this->getRequest()->get('measurement');     
       
        $measurement = $size_chart_helper->calculateMeasurements($user, $request_array);
       
        $measurement->setBraSize($measurement->bra_numbers." ".$measurement->bra_letters);
        if(isset($request_array['neck'])!=0 and isset($request_array['sleeve'])!=0){
            $sizeBaseOnSleeveNeck=$this->get('admin.helper.productsizes')->shirtSizeBaseOnNeckSleeve($request_array['neck'],$request_array['sleeve']);
            $measurement->setArm($sizeBaseOnSleeveNeck['arm_length']);
            $measurement->setShoulderAcrossBack($sizeBaseOnSleeveNeck['shoulder']);
        }
        $this->get('user.helper.measurement')->saveMeasurement($measurement);

        // Rendering step four
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
       return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
            'measurement_vertical_form' => $measurement_vertical_form->createView(),
            'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => 'registration',
                ));
    }}

//-----------------------------------------------------------------------------
    public function measurementEditAction() {

        $size_chart_helper = $this->get('admin.helper.sizechart');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $measurement = $user->getMeasurement();
        $brandHelper=$this->get('admin.helper.brand');
       // $shirtSizesBaseNeckSleeve = $this->get('admin.helper.productsizes')->shirtSizeBaseOnNeckSleeve(14,32);
        
        if ($user->getGender() == 'm') {
            $neck_size=$this->get('admin.helper.productsizes')->manSizeList($neck=1,$sleeve=0,$waist=0,$inseam=0);
            $sleeve_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=1,$waist=0,$inseam=0);
            $waist_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=1,$inseam=0);
            $inseam_size=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=0,$inseam=1);
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper,$neck_size,$sleeve_size,$waist_size,$inseam_size,$brandHelper), $measurement);
       } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper,$this->get('admin.helper.utility')->getBodyShape(),$this->get('admin.helper.utility')->getBraLetters(),$this->get('admin.helper.utility')->getBraNumbers(),$this->get('admin.helper.utility')->getBodyTypesSearching(),$brandHelper), $measurement);
           
            $registrationMeasurementform->get('body_types')->setData($measurement->getBodyTypes());   
            $registrationMeasurementform->get('bra_letters')->setData($measurement->getBraCup());   
            $registrationMeasurementform->get('bra_numbers')->setData($measurement->getBraNumberSize());
            $registrationMeasurementform->get('body_shape')->setData($measurement->getBodyShape());
        }
        $retaining_array = $this->get('user.helper.measurement')->measurementRetain($measurement);
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);      
        #-----------End of Retaining BodyType----------------------------------#
        return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $user,
                    'top_brand_id' => $retaining_array['top_brand_id'],
                    'top_size_chart_id' => $retaining_array['topSizeChartId'],
                    'bottom_brand_id' => $retaining_array['bottom_brand_id'],
                    'bottom_size_chart_id' => $retaining_array['bottomSizeChartId'],
                    'dress_brand_id' => $retaining_array['dress_brand_id'],
                    'dress_size_chart_id' => $retaining_array['dressSizeChartId'],
            'measurement_vertical_form' => $measurement_vertical_form->createView(),
            'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                   
                ));
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//---------------------------------- Image upload STEP ---------------------------------------------------
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //------------------------ Render Fitting room image upload page

    public function stepFourEditAction() {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        $measurement = $user->getMeasurement();
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                        'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => 'registration',
                ));
    }

#---------------------------Profile Edit Image ---------------#

    public function fittingRoomImageEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $user->getMeasurement();

        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
$measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => 'fitting_room',
            'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                ));
    }

//--------------------------- update fitting room image, 
    public function stepFourCreateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $form->bind($request);
        $response_array = "";
        if ($form->isValid()) {
            $image_path = $this->get('user.helper.user')->uploadFittingRoomImage($entity);
            $response_array = array(
                "entity" => $entity,
                "imageurl" => $image_path,
                "status" => "true",
                "msg" => "image has been saved",
            );
        } else {
            $response_array = array(
                "status" => "false",
                "msg" => "invalid image data",
            );
        }

        $response = new Response(json_encode($response_array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

//-------------Updates shoulder height & outseam, input via user move sliders on image, form submit via ajax

    public function stepFourMeasurementUpdateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        $form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('user.helper.measurement')->saveMeasurement($measurement);
            return new Response('Measurement Updated');
        } else {
            return new Response('Measurement has not been updated!');
        }
    }

    //--------------------------------- deals with image submitted from canvas, saves image
    public function stepFourImageUpdateAction() {
        $id = $_POST['id'];
        $entity = $this->get('user.helper.user')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $response = $entity->writeImageFromCanvas($_POST['imageData']);
        return new Response($response);
    }
    
    
    public function stepFourVerticalMeasurementUpdateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        $form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('user.helper.measurement')->saveVerticalPositonMeasurement($measurement);
            return new Response('Measurement Updated');
        } else {
            return new Response('Vertical Position Measurement has not been updated!');
        }
    }
    
    
    
    
    public function stepFourHorizontalMeasurementUpdateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        $form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('user.helper.measurement')->savehorizontalMeasurement($measurement);
            return new Response('Measurement Updated');
        } else {
            return new Response('Horizontal Position Measurement has not been updated!');
        }
    }
    #-----------------Downloading pdf ----------------------------------------#
    public function downloadMeasurementPdfAction(Request $request){
    $basename = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath()."/bundles/lovethatfit/site/pdf/final_version_tape.pdf";
      
   $response =new Response();
    //then send the headers to foce download the zip file
   $response->headers->set('Content-Type','application/pdf');
   $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($basename) . '"');        
   $response->headers->set('Pragma', "no-cache");
   $response->headers->set('Expires', "0");
   $response->headers->set('Content-Transfer-Encoding', "binary");
   $response->sendHeaders();
   $response->setContent(readfile($basename));
   return $response;
    }

}

?>