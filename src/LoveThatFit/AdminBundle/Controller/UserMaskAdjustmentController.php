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

  //----------------Pending User status Update --------------------------------------------------------------------------
  public function updateStatusAction($user_id) {
	$archive = $this->get('user.helper.userarchives')->UpdateStatus($user_id);
	$this->get('session')->setFlash('success', 'Status has been Revert');
	return $this->redirect($this->generateUrl('admin_pending_user'));
  }

  //----------------------------------------------------------------------------------------

    public function showAction($user_id) {

        $user = $this->get('user.helper.user')->find($user_id);

        if (!$user) {
            return new Response('Authentication error');
        }
        $archive = $this->get('user.helper.userarchives')->getPendingArchive($user);
        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);
        $image_actions_archive = json_decode($archive->getImageActions(), 1);

        $device_type = $image_actions_archive['device_type'];
        $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        $default_marker = $this->get('user.marker.helper')->getDefaultMask($user->getGender() == 'm' ? 'man' : 'woman', $measurement->getBodyShape());

        $form = $this->createForm(new RegistrationStepFourType(), $user);
        #$marker = $this->get('user.marker.helper')->arrayToObject($user, $archive->getMarkerArray());        

        if ($archive->getSvgPaths()) {
            $marker = $this->get('user.marker.helper')->arrayToObject($archive->getMarkerArray());
        } else {
            $marker = $this->get('user.marker.helper')->getDefaultObject($user);
        }

        $image_specs = $this->get('user.helper.userimagespec')->createNewWithParams($user, $image_actions_archive);

        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);

        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_canvas.html.twig', array(
                    'form' => $form->createView(), #------>
                    'entity' => $user, #------>
                    'user_image_spec' => $image_specs, #------->
                    'measurement' => $measurement, #------>
                    'edit_type' => 'edit', #------>
                    'marker' => $marker, #------->default marker
                    'default_marker' => $default_marker, #-------->
                    'user_pixcel_height' => $measurement->getHeight() * $image_actions_archive['height_per_inch'], #------>
                    'top_bar' => $measurement->getIphoneHeadHeight(), #------>
                    'bottom_bar' => $measurement->getIphoneFootHeight(), #------>
                    'per_inch_pixcel' => $image_actions_archive['height_per_inch'], #------>
                    'device_type' => $device_type, #------>
                    'device_screen_height' => $device_screen_height['pixel_height'], #------>
                    'archive' => $archive, #------>
                ));
    }

    #----------------------------------------------------------------------------    
     public function archiveSaveMarkerAction(Request $request)
    {
        $params=$request->request->all();                 
         $archive = $this->get('user.helper.userarchives')->find($params['archive_id']);
         return new Response($archive->getId());
        
        if (array_key_exists('auth_token', $usermaker)){
            $user = $this->get('user.helper.user')->findByAuthToken($usermaker['auth_token']);
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~demo account check
            if ($user->getUserMarker()->getDefaultUser()){# if demo account, then get measurement from json
                $measurement = $user->getMeasurement(); 
                $decoded=$measurement->getJSONMeasurement('actual_user');
                 if(is_array($decoded)){
                    $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($decoded, $user);
                }
            }
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $this->get('user.helper.measurement')->updateWithParams($user->getMeasurement(), $usermaker);
            #update actions in the user_image_specs/adjustment
            if(array_key_exists('image_actions', $usermaker) && $usermaker['image_actions']){
                $this->container->get('user.helper.userimagespec')->updateWithParam(json_decode($usermaker['image_actions'],true), $user);
             }
            
            return new Response(json_encode($this->get('user.marker.helper')->fillMarker($user,$usermaker)));
        }else{
            return new Response('Authentication token not provided.');
        }
    }
    #--------------------------------------------------------------------------
    
    public function archiveImageUpdateAction() {
            return new Response('archive image update');
        $auth_token = $_POST['auth_token'];
        $entity = $this->get('user.helper.user')->findByAuthToken($auth_token);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $response = $entity->writeImageFromCanvas($_POST['imageData']);
        #if not from mask marker adjustment interface then resize
        if (!array_key_exists('env', $_POST) || (array_key_exists('env', $_POST) && !$_POST['env']=='admin')){  
            $entity->resize_image(); # image is being resized to 320x568
        }
            #$entity->resize_image(); # image is being resized to 320x568
        $this->get('user.helper.user')->setImageUpdateTimeToCurrent($entity);
        return new Response($response);
    }
    
    #----------------------------------------------------------------------------
}
