<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\UserParentChildLink;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementFemaleType;
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\UserParentChildLinkType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use \DateTime;
use Symfony\Component\Yaml\Parser;

class RegistrationController extends Controller {

    public function loginIndexAction() {
        return $this->render('LoveThatFitUserBundle:Registration:landing.html.twig');
    }


#>>>>>>>>>>>>>>>>>>>>>> Replace with new registration >>>>>>>>>>>>>>>>>>
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
#>>>>>>>>>>>>>>>>>>>>>> Replace with new registration flow / new controller in web services maybe >>>>>>>>>>>>>>>>>>
    public function registrationCreateAction() {
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $brandHelper=$this->get('admin.helper.brand');
        $user_helper = $this->get('user.helper.user');
        $sizes = $this->get('admin.helper.size')->getDefaultArray();
        $security_context = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
        $twitter_helper = $this->get('twitter_helper');
                $twitters = array();
                $twitters = $twitter_helper->twitter_latest();
              
                 $user = new User();
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
                $users=$this->get('user.helper.parent.child')->findUserByParentEmail($user->getEmail());              
                foreach($users as $parent)
                {
                    $userParentChilds=$this->get('user.helper.parent.child')->find($parent->getId());
                    $this->get('user.helper.parent.child')->updateParent($userParentChilds,$user);
                }              
                //send registration email ....            
                $this->get('mail_helper')->sendRegistrationEmail($user);
                  
                if ($user->getGender() == 'm') {
                   $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper,$sizes, $brandHelper), $measurement);
                } else {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper,$sizes,$brandHelper), $measurement);
                }
                  
                return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                            'form' => $registrationMeasurementform->createView(),
                            'measurement' => $measurement,
                            'entity' => $user,
                        ));
            } else {
                
              

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
                            'entity' => $user,
                            'last_username' => $security_context['last_username'],
                            'error' => $security_context['error'],
                            'referer' => 'registration',
                            'twitters' => $twitters,
                        ));
        }
    }

//--------------------------------------------------------------------------------
    #>>>>>>>>>>>>>>>>>>>>>> Remove Replace with new registration (same method with additional params email) >>>>>>>>>>>>>>>>>>
    public function measurementCreateAction(Request $request) {

        $user = $this->get('security.context')->getToken()->getUser();
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $brandHelper = $this->get('admin.helper.brand');
        
        $measurement = $user->getMeasurement();
        $data = $request->request->all();
        #return new response(json_encode($data));
        #---------------------------------
        $manual_measurement=array();
        if (array_key_exists('bust', $data['measurement']))
            $manual_measurement['bust']=$data['measurement']['bust'];
        if (array_key_exists('waist', $data['measurement']))
            $manual_measurement['waist']=$data['measurement']['waist'];
        if (array_key_exists('hip', $data['measurement']))
            $manual_measurement['hip']=$data['measurement']['hip'];
        #------------------------------------------------
        $sizes = $this->get('admin.helper.size')->getDefaultArray();

        #---------Start OF CRF Protection--------------------------------------#
        if ($this->getRequest()->getMethod() == 'POST') {
            if ($user->getGender() == 'm') {
                $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper, $sizes, $brandHelper), $measurement);
            } else {
                $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper, $sizes, $brandHelper), $measurement);
            }
            $registrationMeasurementform->bind($this->getRequest());
            #-------------Evaluate Size Chart From Size Chart Helper ----------------------#

            $request_array = $this->getRequest()->get('measurement');
            $measurement = $size_chart_helper->calculateMeasurements($user, $request_array);

            $measurement->setBraSize($measurement->bra_numbers . $measurement->bra_letters);
            if ($user->getGender() == 'f') {
                $measurement->setBust($request_array['bust']);
            }
            
            $bra_size_spec = $this->get('admin.helper.size')->getWomanBraSpecs($measurement->getBrasize());
            if ($bra_size_spec != null) {
                $measurement->setShoulderAcrossBack($bra_size_spec['shoulder_across_back']);
                #return new Response(json_encode($bra_size_spec));
            }
            if (isset($request_array['neck']) != 0 and isset($request_array['sleeve']) != 0) {
                $sizeBaseOnSleeveNeck = $this->get('admin.helper.productsizes')->shirtSizeBaseOnNeckSleeve($request_array['neck'], $request_array['sleeve']);
                $measurement->setArm($sizeBaseOnSleeveNeck['arm_length']);
                $measurement->setShoulderAcrossBack($sizeBaseOnSleeveNeck['shoulder']);
            }

            #------------ JSON stored
            $ar=json_decode($measurement->getMeasurementJson(), true);
            $ar['size_charts']=$this->get('admin.helper.sizechart')->measurementFromSizeCharts($measurement);
            $ar['manual']=$manual_measurement;
            $measurement->setMeasurementJson(json_encode($ar));
            #--------------
            $user->setBirthDate($measurement->birthdate);
            if ($data['measurement']['timespent']) {
                $user_reg_time = $user->getTimeSpent() + $data['measurement']['timespent'];
                $user->setTimeSpent($user_reg_time);
            }
            $this->get('user.helper.user')->saveUser($user);
            if ($user->getAge() < 15 and $user->isApproved != 1) {        // Rendering step four
                $form = $this->createForm(new UserParentChildLinkType());
                return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $user,
                            'edit_type' => 'registration',
                            'isapproved' => $user->isApproved,
                        ));
            } else {
                  $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
                $form = $this->createForm(new RegistrationStepFourType(), $user);
                $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
                $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
                $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
                $marker = $this->get('user.marker.helper')->getByUser($user);
               
                return $this->render('LoveThatFitUserBundle:Registration:step_image_edit.html.twig', array(
                            'form' => $form->createView(),
                            'measurement_form' => $measurement_form->createView(),
                            'measurement_vertical_form' => $measurement_vertical_form->createView(),
                            'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                            'entity' => $user,
                            'measurement' => $measurement,
                            'edit_type' => 'registration',
                            'marker' => $marker,
                            'default_marker' => $default_marker,
                        ));
            }
        }
  }

#>>>>>>>>>>>>>>>>>>>>>> Removed >>>>>>>>>>>>>>>>>>
    public function measurementEditAction() {

        $size_chart_helper = $this->get('admin.helper.sizechart');
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $measurement = $user->getMeasurement();
         
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
        
        $brandHelper=$this->get('admin.helper.brand');
       // $shirtSizesBaseNeckSleeve = $this->get('admin.helper.productsizes')->shirtSizeBaseOnNeckSleeve(14,32);
        $sizes=$this->get('admin.helper.size')->getDefaultArray();
       //return new response(json_encode($user->getBirthDate()));
        if ($user->getGender() == 'm') {
            
            
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($size_chart_helper,$sizes,$brandHelper), $measurement);
            $registrationMeasurementform->get('birthdate')->setData($user->getBirthDate());
            $registrationMeasurementform->get('body_types')->setData($measurement->getBodyTypes());   
          
       } else {
              //  return new response(json_encode($measurement->getBraCup()));
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType($size_chart_helper,$sizes,$brandHelper), $measurement);           
            $registrationMeasurementform->get('body_types')->setData($measurement->getBodyTypes());   
            $registrationMeasurementform->get('bra_letters')->setData($measurement->getBraCup());   
            $registrationMeasurementform->get('bra_numbers')->setData($measurement->getBraNumberSize());
            $registrationMeasurementform->get('body_shape')->setData($measurement->getBodyShape());
            $registrationMeasurementform->get('birthdate')->setData($user->getBirthDate());
        }
     //  $retaining_array = $this->get('user.helper.measurement')->measurementRetain($measurement);
          $retaining_array = $measurement->getMeasurmentArray();
        
       //return new response(json_encode($retaining_array));
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
                    'default_marker' => $default_marker,
                   
                ));
    }

    #>>>>>>>>>>>>>>>>>>>>>> Ask if require in iOS & android then Removed >>>>>>>>>>>>>>>>>>
    public function parentChildEmailAction(Request $request)
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $userParentChild = $this->get('user.helper.parent.child')->createNew();
        $form = $this->createForm(new UserParentChildLinkType(), $userParentChild);
        $form->bindRequest($request);  
        if ($form->isValid()) {
          
          $users=$this->get('user.helper.user')->findByEmail($userParentChild->getEmail());
          if($users)
          {
              $userParentChild->setParent($users);
              $userParentChild->setChild($user);  
              $this->get('user.helper.parent.child')->saveUserParentLink($userParentChild);            
          }else
          {
              $userParentChild->setChild($user);  
              $this->get('user.helper.parent.child')->saveUserParentLink($userParentChild);            
          }
         } 
         $this->get('mail_helper')->sendParentRegistrationEmail($userParentChild);
         return $this->render('LoveThatFitUserBundle:Registration:user_parent_child_email.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,
                    'parent'=>$userParentChild,
                ));        
    }
    
    #>>>>>>>>>>>>>>>>>>>>>> Ask if require in iOS & android then Removed >>>>>>>>>>>>>>>>>>
    public function parentChildUpdateEmailAction(Request $request)
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);        
        $userParentChild=$this->get('user.helper.parent.child')->findByUser($user);           
        $userParentChilds=$this->get('user.helper.parent.child')->find($userParentChild->getId());
        $form = $this->createForm(new UserParentChildLinkType(), $userParentChilds);
        $form->bindRequest($request);  
        if ($form->isValid()) {         
           $users=$this->get('user.helper.user')->findByEmail($userParentChilds->getEmail());
           if($users)
           {   
               $this->get('user.helper.parent.child')->updateparent($userParentChilds,$users);            
               $this->get('user.helper.parent.child')->update($userParentChilds);            
           }else
           {
               $this->get('user.helper.parent.child')->update($userParentChilds);            
           }
        } 
        $this->get('mail_helper')->sendParentRegistrationEmail($userParentChilds);
         return $this->render('LoveThatFitUserBundle:Registration:user_parent_child_email.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,
                    'parent'=>$userParentChilds,
                ));
    }
    
    
#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>

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

#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>
    public function fittingRoomImageEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $user->getMeasurement();
        $users=$this->get('user.helper.parent.child')->findByUser($user);
        if($users)
        {
        if($users->getIsApproved()=='0')
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'disaprove',
                    'isapproved'=>0,
                ));
        }
        if($users->getIsApproved()==NULL or $users->getIsApproved()==null or $users->getIsApproved()=='' )
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'fitting_room',
                    'isapproved'=>$users->getIsApproved(),
                ));
        }
        
        
        if($users->getIsApproved()==1)
        {        // Rendering step four
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
        }elseif(!$users  and $user->getAge()<15 and $user->isApproved==NULL){        
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'registration',
                    'isapproved'=>$user->isApproved,
                ));
        }else
        {
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
    }

#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>
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
   

#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>
    
    
   public function stepFourEditIpadAction( $edit_type = null) {
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
        $marker = $this->get('user.marker.helper')->getByUser($user);
        #return new response($marker->getRectHeight());   
        $edit_type=$edit_type==null?'registration':'fitting_room';
        
        return $this->render('LoveThatFitUserBundle:Registration:ipad_stepfour.html.twig', array(
                    'form' => $form->createView(),                    
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
            ));
   }
   
#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>
   
   
   public function stepFourEditIRangeOverAction( $edit_type = null) {
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
        $marker = $this->get('user.marker.helper')->getByUser($user);
        #return new response($marker->getRectHeight());   
        $edit_type=$edit_type==null?'registration':'fitting_room';
        
        return $this->render('LoveThatFitUserBundle:Registration:range_over_stepfour.html.twig', array(
                    'form' => $form->createView(),                    
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
            ));
   }
   
   #-------------Step Four Marking Inspection--------------------------------#
  public function stepFourMarkingInspectionAction( $edit_type = null) {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $measurement = $user->getMeasurement();
        #-----------------------------------------
        $user_mm_comparison = $this->get('user.marker.helper')->getComparisionArray($user);
        #-----------------------------------------
        
        
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $edit_type=$edit_type==null?'registration':'fitting_room';
        #return new Response(json_encode($user_mm_comparison));
        return $this->render('LoveThatFitUserBundle:Registration:step_four_marker_inspection.html.twig', array(
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                     'mesurement_segs'=>$user_mm_comparison,
            ));
   }
#>>>>>>>>>>>>>>>>>>>>>> Remove >>>>>>>>>>>>>>>>>>
public function stepFourTimeSpentAction(Request $request){
     $data = $request->request->all();
     $id = $this->get('security.context')->getToken()->getUser()->getId();
    $user = $this->get('user.helper.user')->find($id);
    $user->setTimeSpent($_GET['chk_time']);
    $this->get('user.helper.user')->saveUser($user); 
}
#>>>>>>>>>>>>>>>>>>>>>> Check for usage & Remove >>>>>>>>>>>>>>>>>>

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

    #>>>>>>>>>>>>>>>>>>>>>> check for usage then Remove >>>>>>>>>>>>>>>>>>
    public function stepFourImageUpdateAction() {
        $id = $_POST['id'];
        $entity = $this->get('user.helper.user')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $response = $entity->writeImageFromCanvas($_POST['imageData']);
        $this->get('user.helper.user')->updateImageDeviceType($entity);
        return new Response($response);
        
    }
        #>>>>>>>>>>>>>>>>>>>>>> check for usage then Remove >>>>>>>>>>>>>>>>>>
    public function stepFourVerticalMeasurementUpdateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        $form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('user.helper.measurement')->saveMeasurement($measurement);
            return new Response('Measurement Updated');
        } else {
            return new Response('Vertical Position Measurement has not been updated!');
        }
    }
        #>>>>>>>>>>>>>>>>>>>>>> check for usage then Remove >>>>>>>>>>>>>>>>>>
    public function stepFourHorizontalMeasurementUpdateAction(Request $request, $id) {

        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        $form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('user.helper.measurement')->saveMeasurement($measurement);
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
    
#>>>>>>>>>>>>>>>>>>>>>> check for usage then Remove >>>>>>>>>>>>>>>>>>
      public function stepImageEditAction($edit_type=null) {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $measurement = $user->getMeasurement();
         $users=$this->get('user.helper.parent.child')->findByUser($user);
        if($users)
        {
        if($users->getIsApproved()=='0')
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'disaprove',
                    'isapproved'=>0,
                ));
        }
        if($users->getIsApproved()==NULL or $users->getIsApproved()==null or $users->getIsApproved()=='' )
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'fitting_room',
                    'isapproved'=>$users->getIsApproved(),
                ));
        }
        
        
        if($users->getIsApproved()==1)
        {        // Rendering step four
          $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Registration:step_image_edit.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                        'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
        }        
        }elseif(!$users  and $user->getAge()<15 and $user->isApproved==NULL){        
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'registration',
                    'isapproved'=>$user->isApproved,
                ));
        }else
        {        
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Registration:step_image_edit.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
    }
}


    #>>>>>>>>>>>>>>>>>>>>>> check for usage then Remove >>>>>>>>>>>>>>>>>>
      public function deviceViewAction($edit_type=null) {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $measurement = $user->getMeasurement();
         $users=$this->get('user.helper.parent.child')->findByUser($user);
        if($users)
        {
        if($users->getIsApproved()=='0')
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'disaprove',
                    'isapproved'=>0,
                ));
        }
        if($users->getIsApproved()==NULL or $users->getIsApproved()==null or $users->getIsApproved()=='' )
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'fitting_room',
                    'isapproved'=>$users->getIsApproved(),
                ));
        }
        
        
        if($users->getIsApproved()==1)
        {        // Rendering step four
          $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Registration:device_view.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                        'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
        }        
        }elseif(!$users  and $user->getAge()<15 and $user->isApproved==NULL){        
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'registration',
                    'isapproved'=>$user->isApproved,
                ));
        }else
        {        
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Registration:device_view.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
    }
}




}

?>