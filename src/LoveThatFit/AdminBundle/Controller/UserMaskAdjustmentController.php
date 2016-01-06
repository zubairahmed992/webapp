<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;

class UserMaskAdjustmentController extends Controller {

      public function indexAction(){
          $form=$this->createForm(new \LoveThatFit\UserBundle\Form\Type\UserDropdownType());
        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:index.html.twig', array(
                    'form' => $form->createView(),                    
                ));
    }

     #---------------------------------------------------------------------------
    
   public function showCanvasAction($user_id) {
       $user = $this->get('webservice.helper.user')->find($user_id);
      # return  new Response ($user->getEmail());
       $edit_type='edit';
       $device_type='iphone5';
       $device_type =$device_type==null? 'iphone5':$device_type;
       if(!$user){
           return new Response ('Authentication error');
       }
       
        $measurement = $user->getMeasurement();               
        if ($user->getUserMarker()->getDefaultUser()){# if demo account, then get measurement from json
            $decoded=$measurement->getJSONMeasurement('actual_user');            
            if(is_array($decoded)){
                $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($decoded, $user);
            }
        }
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
        $device_spec = $user->getDeviceSpecs($device_type);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
        
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_canvas.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                       'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
                    'user_pixcel_height' =>  $device_spec->getUserPixcelHeight(),
                    'top_bar' => $user->getMeasurement()->getIphoneHeadHeight(),
                    'bottom_bar' => $user->getMeasurement()->getIphoneFootHeight(),
                    'per_inch_pixcel' => $device_spec->getDeviceUserPerInchPixelHeight(),
                    'device_type' => $device_type,
                    'device_screen_height' => $device_screen_height['pixel_height'],
            ));
    
}
}
