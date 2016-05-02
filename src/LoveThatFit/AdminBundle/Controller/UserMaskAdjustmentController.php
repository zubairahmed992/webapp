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

    public function showAction($archive_id) {
        
        /*
        
                $param = json_decode('{"camera_angle":"122","camera_x":"1.149475","device_type":"iphone5","height_per_inch":"6.89","move_up_down":-18,"move_left_right":-14,"img_rotate":0}',true);
                $arch = json_decode('{"camera_angle":"12","camera_x":"0.149475","device_type":"iphone5","height_per_inch":"6.89"}',true);
                if (is_array($param)&& is_array($arch)){
                    return new response(json_encode(array_merge($arch, $param)));
                }
        
        */
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
         #return new Response(json_encode($archive->getMarkerArray()));   
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        #$marker = $this->get('user.marker.helper')->arrayToObject($user, $archive->getMarkerArray());        
        #return new Response(json_encode($marker->getMarkerJson()));
        $edit_type = "registration";
        if ($archive->getSvgPaths()) {
            $edit_type = "edit";
            $marker = $this->get('user.marker.helper')->arrayToObject($user, $archive->getMarkerArray());
        } else {
            $edit_type = "registration";
            $marker = $this->get('user.marker.helper')->getDefaultObject($user);
        }
        
        #$marker = $this->get('user.marker.helper')->getDefaultObject($user);
        
        $image_specs = $this->get('user.helper.userimagespec')->createNewWithParams($user, $image_actions_archive);

        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
        
        $svgpath = "M139.4159,83.62949c-1.976,9.40073 0.605,13.95582 1.205,15.73127c-3,-1.05438 -0.924,5.5881 -0.618,6.92129c1.774,7.63169 3.626,6.88817 3.827,6.42027c0.689,1.9389 2.727,12.33526 3.736,14.30834c0.151,3.52741 -0.993,12.53609 -3.217,14.52307c-5.672,5.0326 -17.9395,5.20779 -36.5505,11.25417c-10.398,3.78059 -9.05992,22.18146 -13.13992,49.95315c-0.216,3.787 -1.555,11.53513 -3.913,28.23318c-2.808,26.29214 -3.86,20.52458 -4.425,70.16046c-4.963,13.80626 -5.20858,27.80908 7.37242,37.45445c1.369,1.21996 16.889,12.24552 5.124,-10.99779c1.121,-2.21451 5.182,-15.54646 2.508,-26.45773c0.415,-1.47314 -1.07842,-16.21733 8.04358,-66.40337c2.328,-20.58974 2.114,-20.11864 3.025,-28.19793c0.314,-1.79255 1.51292,-17.4608 3.16892,-17.4608c0.797,0.06196 1.499,8.64974 1.842,13.29136c2.861,19.62083 4.093,18.41476 4.216,34.12894c-0.505,9.21913 -1.286,10.15813 -3.278,16.28036c-2.22,6.07629 -2.505,5.94169 -5.041,20.21585c-1.527,17.49605 -1.425,18.83672 -2.08,35.34462c1.261,18.47992 0.79296,8.3517 4.09096,35.01987c5.515,28.96281 7.4561,30.72865 9.0181,55.78054c-0.007,11.54154 0.413,0.03418 -0.676,16.82411c-1.268,20.05241 -1.122,21.21682 -0.488,31.407c3.1,24.85105 5.41561,30.28746 7.16761,44.26037c0.795,8.73948 4.06438,10.19659 3.72038,25.84668c-0.876,9.97546 0.85546,7.83893 -1.01654,12.49977c-1.229,4.56149 -14.82,22.04241 4.067,22.00641c2.331,0.03846 11.557,-0.898 13.633,-22c0.266,-5.8573 -0.22243,-8.20641 -1.36343,-12.50404c1.709,-28.05371 2.87343,-48.44583 4.67443,-70.12414c0.194,-7.1499 0.18111,-28.26523 -0.04589,-33.87362c3.125,-14.30941 3.93303,-34.20158 5.52303,-70.10704c0.005,-7.84107 -2.09514,-22.85767 0.36586,-22.85767c2.362,0 -0.05514,11.94321 0.37686,22.81174c0.857,35.93751 2.41103,56.0209 5.34903,69.99808c-0.098,5.48982 0.02211,26.80491 0.04511,33.9281c2.082,21.7659 2.71443,42.09179 4.74443,70.24271c-1.328,4.16196 -0.91143,6.49719 -1.28443,12.4549c1.893,21.066 12.748,22.08867 14.514,22.02671c17.533,0.059 4.76,-17.67459 2.963,-22c-1.758,-4.7025 -0.54854,-2.73369 -0.86954,-12.51686c-0.564,-15.55287 4.43298,-17.05272 5.30398,-25.8157c1.535,-14.10537 2.816,-19.50759 5.754,-44.12363c0.711,-10.40918 0.713,-11.6067 -0.619,-31.61958c-1.315,-16.36796 -0.391,-5.4332 -0.637,-16.6393c1.457,-25.13094 3.8661,-27.10723 9.0391,-55.92796c3.746,-26.44063 3.24096,-16.81022 4.17996,-34.98676c-0.762,-16.64678 -0.94,-18.03339 -2.259,-35.33287c-2.628,-14.2325 -2.773,-14.41197 -5.194,-20.23294c-2.164,-6.25149 -2.431,-7.22147 -3.043,-16.23229c-0.32,-16.00155 1.263,-14.84675 4.201,-34.15671c0.596,-4.82215 1.037,-13.2561 1.68,-13.2561c1.287,-0.00107 3.16992,15.55074 3.31792,17.38602c0.838,8.13377 0.345,7.4426 2.986,28.18191c7.926,50.19351 7.80358,65.01356 8.25758,66.41939c-2.94,10.69227 1.727,24.05627 2.377,26.46948c-8.449,22.90361 2.924,12.27116 4.68,11.06295c14.952,-9.91991 12.97758,-23.71228 9.96358,-37.50359c-0.274,-49.8709 -2.003,-44.3063 -4.571,-70.14871c-3.115,-16.72583 -3.403,-24.47289 -3.926,-28.27484c-3.579,-27.91163 62.69492,-163.40437 50.94192,-166.9617c-19.662,-6.06988 -96.5815,110.59575 -102.8415,105.70844c-2.991,-2.19422 -2.957,-10.96254 -3.133,-14.34573c1.111,-1.75516 3.537,-13.60543 3.769,-14.4579c1.899,1.9154 3.221,-5.16186 3.608,-6.33161c2.675,-8.29722 -0.247,-7.18195 -0.601,-6.93838c3.409,-6.14787 1.677,-14.25066 1.332,-15.76866c-3.816,-14.18015 -20.406,-14.54016 -20.406,-14.54016c-17.327,0 -20.159,13.54881 -20.475,14.54016z";

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
                    'device_model' => "iphone5s", #------>
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
            #return new Response('archive image update');
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
     private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;        
    }
}
