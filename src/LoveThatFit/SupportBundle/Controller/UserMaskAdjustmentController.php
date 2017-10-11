<?php

namespace LoveThatFit\SupportBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;

class UserMaskAdjustmentController extends Controller {

    
	//----------------All Pending User Display List --------------------------------------------------------------------------
  public function listAction($page_number, $sort = 'id') {
	$orders_with_pagination = $this->get('user.helper.userarchives')->getListWithPagination($page_number, $sort);
	return $this->render('LoveThatFitSupportBundle:PendingUser:index.html.twig', $orders_with_pagination);
  }

  //----------------Pending User status Update --------------------------------------------------------------------------
  public function updateStatusAction($user_id) {
	$archive = $this->get('user.helper.userarchives')->UpdateStatus($user_id);
	$this->get('session')->setFlash('success', 'Status has been Reverted');
	return $this->redirect($this->generateUrl('support_pending_users'));
  }
    //----------------Pending User status discard Status icon --------------------------------------------------------------------------
    public function discardStatusAction($user_id) {
        $archive = $this->get('user.helper.userarchives')->discardStatus($user_id);
        $this->get('session')->setFlash('success', 'Archive has been discarded');
        return $this->redirect($this->generateUrl('support_pending_user'));
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
        
        ############ new code start #############
        /* Get Marker Param*/
        $archive_marker_param = $archive->getMarkerParams();
        $archive_marker_json = json_decode($archive_marker_param);
        $mask_p_x = 0;
        $mask_p_y = 0;
        if(array_key_exists('mask_p_x',$archive_marker_json)){
            $mask_p_x = $archive_marker_json->mask_p_x;
        }

        if(array_key_exists('mask_p_y',$archive_marker_json)){
            $mask_p_y = $archive_marker_json->mask_p_y;
        }
        $pivot_position = array('mask_p_x' => $mask_p_x,'mask_p_y' => $mask_p_y);
        ############ new code end #############

        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);
        $image_actions_archive = json_decode($archive->getImageActions(), 1);

        $device_type = $image_actions_archive['device_type'];
        $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        //change
        $default_marker = $this->get('user.marker.helper')->getDefaultMaskSupport($user->getGender() == 'm' ? 'man' : 'woman', $measurement->getBodyShape());
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
        ##new code from im branch
        $bra_size_body_shape = $this->container->get('admin.helper.size')->getWomanBraSizeBodyShape($measurement->getBrasize(),$measurement->getBodyShape($user->getGender(),true));
        
        return $this->render('LoveThatFitSupportBundle:UserMaskAdjustment:_mask_pending.html.twig', array(
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
                    'pivot_position' => $pivot_position,
                    'bra_size_body_shape' => json_encode($bra_size_body_shape),
                ));
    }

    ### new method copy from im_mask_refinement branch
    public function showTestAction($archive_id, $mode=null)
    {
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
        
        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_pending_test.html.twig', array(
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
        $this->get('user.helper.userarchives')->saveArchivesSupport($archive, $params);
        return new Response('archive updated');
    }
    #------------------------support_archive_to_live:  /support/archive_to_live/{archive_id}----------------------------------------------------

    public function archiveToLiveAction($archive_id) {
        $archive=$this->get('user.helper.userarchives')->makeArchiveToCurrentSupport($archive_id);
        $user=$this->container->get('user.helper.user')->find($archive->getUser()->getId());
        $decoded  = $this->process_request();
        $decoded['auth_token']=$user->getAuthToken();
        $json_data = $this->get('webservice.helper')->userDetail($decoded);

        try {
            //update podio user member calibrated to yes
            $this->updatePodioUserMemberCalibrated($archive->getUser()->getId());
        } catch(\Exception $e) {
            // log $e->getMessage()
        }

        $push_response = $this->get('pushnotification.helper')->sendPushNotification($user, $json_data);
        return new Response('archive to live'.$archive_id);
    }

    private function updatePodioUserMemberCalibrated($user_id){
        ##send update to podio that the user is activated
        $data = $this->container->get('user.helper.podioapi')->updateUserPodio($user_id);
    }
    #--------------------------------------------------------------------------
    
    public function archiveImageUpdateAction(Request $request) {
        $params = $request->request->all();
        $archive = $this->get('user.helper.userarchives')->find($params['archive_id']);
        $image_actions = json_decode($archive->getImageActions());
        $device_type = $image_actions->device_type;
        if (!$archive) {
            throw $this->createNotFoundException('Unable to find archive.');
        }
        $response = $archive->writeImageFromCanvas($_POST['imageData']);
        #if not from mask marker adjustment interface then resize
        $archive->resizeImageSupport($device_type); # image is being resized to 320x568
        #$this->get('user.helper.user')->setImageUpdateTimeToCurrent($entity);
        return new Response($response);
    }
    #----------------------------------------------------------------------------
    #----------------------------------------------------------------------------
    #----------------------------------------------------------------------------    
      public function createArchivesDataAction($user_id) {
          $user = $this->container->get('user.helper.user')->find($user_id);
          $archive=$this->container->get('user.helper.userarchives')->createFromExistingData($user);
          return $this->redirect($this->generateUrl('support_user_profile_archives', array('user_id' => $user->getId(),
                'archive_id' => $archive->getId())));
      }

      //new method for new caliborationpage copy from im mask branch
        public function createArchivesDataTestAction($user_id) {
          $user = $this->container->get('user.helper.user')->find($user_id);
          $archive=$this->container->get('user.helper.userarchives')->createFromExistingData($user);
          return $this->redirect($this->generateUrl('admin_user_profile_archives_test', array('user_id' => $user->getId(),
                'archive_id' => $archive->getId())));
        }
    #-------------------------/support/archive_delete_with_images/{archive_id}------
    public function deleteArchiveWithImagesAction($archive_id) {
      $this->container->get('user.helper.userarchives')->deleteArchiveWithImages($archive_id);
      return new Response('deleted');
    }
    #----------------------------------------------------------------------------    
  
    public function profileArchivesAction($user_id, $archive_id = null, $mode=null)
    {
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
            } elseif ($a->getStatus() == 1 && !$default_archive_id) {
                #pick the active one
                $default_archive_id = $a->getId();
            }
        }
        
        if ($default_archive_id && !$archive) {
            $archive = $this->get('user.helper.userarchives')->find($default_archive_id);
            $archive_id = $default_archive_id;
        }

        if (!$archive) {
            return $this->render('LoveThatFitSupportBundle:UserMaskAdjustment:create_archive.html.twig', array(
                    'user' => $user, #------>
                    ));
        
        }

        if (!$user) {
            return new Response('Authentication error');
        }

        #####new code copy from im branch
         /* Get Marker Param*/
        $archive_marker_param = $archive->getMarkerParams();
        $archive_marker_json = json_decode($archive_marker_param);
        $mask_p_x = 0;
        $mask_p_y = 0;
        if(array_key_exists('mask_p_x',$archive_marker_json)){
            $mask_p_x = $archive_marker_json->mask_p_x;
        }

        if(array_key_exists('mask_p_y',$archive_marker_json)){
            $mask_p_y = $archive_marker_json->mask_p_y;
        }
        $pivot_position = array('mask_p_x' => $mask_p_x,'mask_p_y' => $mask_p_y);


        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);
        $image_actions_archive = json_decode($archive->getImageActions(), 1);

        $device_type = $image_actions_archive['device_type'];
        $device_type=$device_type?$device_type:$user->getImageDeviceType();
        $measurement = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        $default_marker = $this->get('user.marker.helper')->getDefaultMaskSupport($user->getGender() == 'm' ? 'man' : 'woman', $measurement->getBodyShape());
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
        ##### new code from im branch
        $bra_size_body_shape = $this->container->get('admin.helper.size')->getWomanBraSizeBodyShape($measurement->getBrasize(),$measurement->getBodyShape($user->getGender(),true));

        /* Get all Retouch images */
        $original_filename = $archive->getImageName('original');
        $retouch_filename = str_ireplace("original", "retouch", $original_filename);
        $retouch_filename = substr($retouch_filename, 0, -4);
        $user_id = $user->getId();
        $directory_path = $archive->getUploadRootDir();
        $retouch_files = glob($directory_path."/".$retouch_filename."*.*");
        $retouch_filecount = count($retouch_files);
        $original_file_code_image = str_ireplace("original_", "", $original_filename);
        $original_file_code = substr($original_file_code_image, 0, -4);

        return $this->render('LoveThatFitSupportBundle:UserMaskAdjustment:_mask_pending.html.twig', array(
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
                    'pivot_position' => $pivot_position,
                    'bra_size_body_shape' => json_encode($bra_size_body_shape),
                    'retouch_filename' => $retouch_filename,
                    'retouch_filecount' => $retouch_filecount,
                    'original_file_code' => $original_file_code,
                ));
    }

    ##copy method from im branch

    public function profileArchivesTestAction($user_id, $archive_id = null, $mode=null) {
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
            } elseif ($a->getStatus() == 1 && !$default_archive_id) {
                #pick the active one
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

        ##new code from im branch
        /* Get Marker Param*/
        $archive_marker_param = $archive->getMarkerParams();
        $archive_marker_json = json_decode($archive_marker_param);
        $mask_p_x = 0;
        $mask_p_y = 0;
        if(array_key_exists('mask_p_x',$archive_marker_json)){
            $mask_p_x = $archive_marker_json->mask_p_x;
        }

        if(array_key_exists('mask_p_y',$archive_marker_json)){
            $mask_p_y = $archive_marker_json->mask_p_y;
        }
        $pivot_position = array('mask_p_x' => $mask_p_x,'mask_p_y' => $mask_p_y);

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

        return $this->render('LoveThatFitAdminBundle:UserMaskAdjustment:_mask_pending_test.html.twig', array(
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
                    'pivot_position' => $pivot_position,
                ));
    }

    #----------------------------------------------------------------------------
     private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;        
    }

    public function uploadRetouchImageAction(Request $request) {
        $params = $request->request->all();
        $user_id = $params['upl_entity_id'];
        $archive_id = $params['upl_archive_id'];
        $file = $_FILES["upl_user_retouch"];

        /* Save Touch image*/
        $this->get('user.helper.userarchives')->saveretouchimage($params,$file);

        return $this->redirect($this->generateUrl('support_user_profile_archives', array('user_id' => $user_id,
            'archive_id' => $archive_id)));

    }
}
