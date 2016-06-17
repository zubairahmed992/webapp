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
	$this->get('session')->setFlash('success', 'Status has been Reverted');
	return $this->redirect($this->generateUrl('admin_pending_user'));
  }
    //----------------Pending User status discard Status icon --------------------------------------------------------------------------
    public function discardStatusAction($user_id) {
        $archive = $this->get('user.helper.userarchives')->discardStatus($user_id);
        $this->get('session')->setFlash('success', 'Archive has been discarded');
        return $this->redirect($this->generateUrl('admin_pending_user'));
    }
  //----------------------------------------------------------------------------------------

    public function showAction($archive_id, $mode=null) {
        
        $archive = $this->get('user.helper.userarchives')->find($archive_id);
        $user = $archive->getUser();

        if (!$archive) {
            return new Response('archive not found');
        }
        
        if (!$user) {
            return new Response('Authentication error');
        }
        
        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);
        $image_actions_archive = json_decode($archive->getImageActions(), 1);

        $device_type = $image_actions_archive['device_type'];
        $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        $default_marker = $this->get('user.marker.helper')->getDefaultMask($user->getGender() == 'm' ? 'man' : 'woman', $measurement->getBodyShape());
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $edit_type = "registration";
        if ($mode && $mode == 'refresh') {
            $edit_type = "registration";
            $marker = $this->get('user.marker.helper')->getDefaultObject($user);
        } else {
            if ($archive->getSvgPaths()) {
                $edit_type = "edit";
                $marker = $this->get('user.marker.helper')->arrayToObject($user, $archive->getMarkerArray());
            } else {
                $edit_type = "registration";
                $marker = $this->get('user.marker.helper')->getDefaultObject($user);
            }
        }
        $mode=$archive->getSvgPaths()?$mode:null;
        $image_specs = $this->get('user.helper.userimagespec')->createNewWithParams($user, $image_actions_archive);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
        
        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_pending.html.twig', array(
                    'form' => $form->createView(), #------>
                    'entity' => $user, #------>
                    'user_image_spec' => $image_specs, #------->
                    'measurement' => $measurement, #------>
                    'edit_type' => $edit_type, #------>
                    'marker' => $marker, #------->default marker
                    'default_marker' => $default_marker, #-------->
                    'user_pixcel_height' => $measurement->getHeight() * $image_actions_archive['height_per_inch'], #------>
                    'top_bar' => $measurement->getIphoneHeadHeight(), #------>
                    'bottom_bar' => $measurement->getIphoneFootHeight(), #------>
                    'per_inch_pixcel' => $image_actions_archive['height_per_inch'], #------>
                    'device_type' => $device_type, #------>
                    'device_screen_height' => $device_screen_height['pixel_height'], #------>
                    'archive' => $archive, #------>
                    'mode' => $mode, #------>
                    'device_model' => array_key_exists('device_model',$image_actions_archive)?$image_actions_archive['device_model']:'', #------>
                ));
    }

    #----------------------------------------------------------------------------    

    public function archiveSaveMarkerAction(Request $request) {
        $params = $request->request->all();
        $archive = $this->get('user.helper.userarchives')->find($params['archive_id']);
        $this->get('user.helper.userarchives')->saveArchives($archive, $params);        
        return new Response('archive updated');
    }
    #------------------------admin_archive_to_live:  /admin/archive_to_live/{archive_id}----------------------------------------------------    

    public function archiveToLiveAction($archive_id) {
        $archive=$this->get('user.helper.userarchives')->makeArchiveToCurrent($archive_id);
        $user=$this->container->get('user.helper.user')->find($archive->getUser()->getId());                  
        
        $decoded  = $this->process_request();
        $decoded['auth_token']=$user->getAuthToken();
        $json_data = $this->get('webservice.helper')->userDetail($decoded);
        $push_response = $this->get('pushnotification.helper')->sendPushNotification($user, $json_data);
        return new Response('archive to live'.$archive_id);
    }
    #--------------------------------------------------------------------------
    
    public function archiveImageUpdateAction(Request $request) {
        $params = $request->request->all();
        $archive = $this->get('user.helper.userarchives')->find($params['archive_id']);
        
        if (!$archive) {
            throw $this->createNotFoundException('Unable to find archive.');
        }
        $response = $archive->writeImageFromCanvas($_POST['imageData']);
        #if not from mask marker adjustment interface then resize
        $archive->resizeImage(); # image is being resized to 320x568
        #$this->get('user.helper.user')->setImageUpdateTimeToCurrent($entity);
        return new Response($response);
    }
    #----------------------------------------------------------------------------
    #----------------------------------------------------------------------------
    #----------------------------------------------------------------------------    
  public function createArchivesDataAction($user_id) {
      $user = $this->container->get('user.helper.user')->find($user_id);
      $archive=$this->container->get('user.helper.userarchives')->createFromExistingData($user);
      return $this->redirect($this->generateUrl('admin_user_profile_archives', array('user_id' => $user->getId(),
            'archive_id' => $archive->getId())));
  }
   #-------------------------/admin/archive_delete_with_images/{archive_id}---------------------------------------------------    
  public function deleteArchiveWithImagesAction($archive_id) {
      $this->container->get('user.helper.userarchives')->deleteArchiveWithImages($archive_id);
      return new Response('deleted');
  }
    #----------------------------------------------------------------------------    
  
    public function profileArchivesAction($user_id, $archive_id = null, $mode=null) {
        $archive = null;
        if ($archive_id) {
            $archive = $this->get('user.helper.userarchives')->find($archive_id);
            if ($archive) {
                $user = $archive->getUser();
            } else {
                $user = $this->container->get('user.helper.user')->find($user_id);
            }
        } else {
            $user = $this->container->get('user.helper.user')->find($user_id);
        }

        $default_archive_id = null;
       
  #find all archives associated with user
        foreach ($user->getUserArchives() as $a) {
            if ($a->getStatus() == -1) {#pick the pending one
                $default_archive_id = $a->getId();
            } elseif ($a->getStatus() == 1 && !$default_archive_id) {            #pick the active one
                $default_archive_id = $a->getId();
            }
        }
        
        if ($default_archive_id && !$archive) {
            $archive = $this->get('user.helper.userarchives')->find($default_archive_id);
            $archive_id = $default_archive_id;
        }

        if (!$archive) {
            return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:create_archive.html.twig', array(
                    'user' => $user, #------>
                    ));
        
        }

        if (!$user) {
            return new Response('Authentication error');
        }

        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);
        $image_actions_archive = json_decode($archive->getImageActions(), 1);

        $device_type = $image_actions_archive['device_type'];
        $device_type=$device_type?$device_type:$user->getImageDeviceType();
        $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        $default_marker = $this->get('user.marker.helper')->getDefaultMask($user->getGender() == 'm' ? 'man' : 'woman', $measurement->getBodyShape());
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $edit_type = "registration";
        
        if ($archive->getSvgPaths() && !$mode) {
            $edit_type = "edit";
            $marker = $this->get('user.marker.helper')->arrayToObject($user, $archive->getMarkerArray());
        } else {
            $edit_type = "registration";
            $marker = $this->get('user.marker.helper')->getDefaultObject($user);
        }
        $mode=$archive->getSvgPaths()?$mode:null;
        $image_specs = $this->get('user.helper.userimagespec')->createNewWithParams($user, $image_actions_archive);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);

        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_pending.html.twig', array(
                    'form' => $form->createView(), #------>
                    'entity' => $user, #------>
                    'user_image_spec' => $image_specs, #------->
                    'measurement' => $measurement, #------>
                    'edit_type' => $edit_type, #------>
                    'marker' => $marker, #------->default marker
                    'default_marker' => $default_marker, #-------->
                    'user_pixcel_height' => $measurement->getHeight() * $image_actions_archive['height_per_inch'], #------>
                    'top_bar' => $measurement->getIphoneHeadHeight(), #------>
                    'bottom_bar' => $measurement->getIphoneFootHeight(), #------>
                    'per_inch_pixcel' => $image_actions_archive['height_per_inch'], #------>
                    'device_type' => $device_type, #------>
                    'device_screen_height' => $device_screen_height['pixel_height'], #------>
                    'archive' => $archive, #------>
                    'mode' => $mode, #------>
                    'archives' => $user->getUserArchives(), #------>
                    'device_model' => is_array($image_actions_archive) && array_key_exists('device_model', $image_actions_archive) ? $image_actions_archive['device_model'] : '', #------>
                ));
    }

    #----------------------------------------------------------------------------
     private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;        
    }
}
