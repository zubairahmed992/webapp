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
       $user = $this->get('user.helper.user')->find($user_id);
      # return  new Response ($user->getEmail());
       $device_type =$user->getImageDeviceType()==null? 'iphone5':$user->getImageDeviceType();
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
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
        $device_spec = $user->getDeviceSpecs($device_type);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
        
        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_canvas.html.twig', array(
                    'form' => $form->createView(),               
                    'entity' => $user,
                    'user_image_spec' => $user->getUserImageSpec(),
                    'measurement' => $measurement,
                    'edit_type' => 'edit',
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

	//----------------All Pending User Display List --------------------------------------------------------------------------
	public function listAction($page_number, $sort = 'id') {
	  $orders_with_pagination = $this->get('user.helper.userarchives')->getListWithPagination($page_number, $sort);
	  // print_r($orders_with_pagination);die;
	  return $this->render('LoveThatFitAdminBundle:PendingUser:index.html.twig', $orders_with_pagination);
	}

  //-----------------------Display Single order Detail by Id-----------------------------------------------------------------

	public function showAction($user_id) {
	  $user = $this->get('user.helper.user')->find($user_id);
	  # return  new Response ($user->getEmail());
	  $device_type =$user->getImageDeviceType()==null? 'iphone5':$user->getImageDeviceType();
	  if(!$user){
		return new Response ('Authentication error');
	  }
	  $measurement_archive = $this->get('user.helper.userarchives')->getArchive($user);
	  $measurement='';
	  //$measurement = $user->getMeasurement();
	  //$temp = array("height" => $measurement->getHeight(),"bust" => $measurement->getBust());
	  //print_r($temp);
		$decoded_measurement_archive = json_decode($measurement_archive->getMeasurementJson(),1);
	  
		if(is_array($decoded_measurement_archive)){
		  $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($decoded_measurement_archive, $user);
		}
	  $form = $this->createForm(new RegistrationStepFourType(), $user);
	  $marker = $this->get('user.marker.helper')->getByUser($user);
	  $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
	  $device_spec = $user->getDeviceSpecs($device_type);
	  $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
	  //$temp = array("height" => $measurement->getHeight(),"bust" => $measurement->getBust());
		//print_r($temp);

	  $marker_param_decoded_value = json_decode($measurement_archive->getMarkerParams(),1);
	  //$decoded_marker_param_archive=$marker_param_decoded_value['marker_params'];

	  return $this->render('LoveThatFitAdminBundle:PendingUser:show.html.twig', array(
		'form' => $form->createView(),
		'entity' => $user,
		'user_image_spec' => $measurement_archive->getImageActions(),
		'measurement' => $measurement,
		'edit_type' => 'edit',
		'marker' => $marker,
		'default_marker' => $measurement_archive->getDefaultMarkerSvg(),
		'user_pixcel_height' =>  $device_spec->getUserPixcelHeight(),
		'top_bar' => $user->getMeasurement()->getIphoneHeadHeight(),
		'bottom_bar' => $user->getMeasurement()->getIphoneFootHeight(),
		'per_inch_pixcel' => $marker_param_decoded_value['height_per_inch'],
		'device_type' => $marker_param_decoded_value['device_type'],
		'device_screen_height' => $device_screen_height['pixel_height'],
	  ));
	}
}
