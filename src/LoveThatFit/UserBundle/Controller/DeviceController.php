<?php

namespace LoveThatFit\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller {

     public function loginAction() {
        
        $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
       
        return $this->render(
                        'LoveThatFitUserBundle:Device:login.html.twig', array(
                        'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    
                        )
        );
    }
    
      
    #---------------------------------------------------------------------------
    
   public function editImageAction($auth_token=null, $edit_type=null, $device_type=null) {
       $user = $this->get('webservice.helper.user')->findByAuthToken($auth_token);
       $device_type =$device_type==null? 'iphone5':$device_type;
       if(!$user){
           return new Response ('Authentication error');
       }
       
        $measurement = $user->getMeasurement();                
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
        
        return $this->render('LoveThatFitUserBundle:Device:device_view.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
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
 #---------------------------------------------------------------------------
    
   public function makeoverAction($auth_token=null, $edit_type=null, $device_type=null) {
       $user = $this->get('webservice.helper.user')->findByAuthToken($auth_token);
       $device_type =$device_type==null? 'iphone5':$device_type;
       if(!$user){
           return new Response ('Authentication error');
       }
       
        $measurement = $user->getMeasurement();                
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
        
        return $this->render('LoveThatFitUserBundle:Device:mask_makeover.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
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
#---------------------------------------------------------------------------
    
   public function svgPathAction($auth_token=null, $edit_type=null, $device_type=null) {
       $user = $this->get('webservice.helper.user')->findByAuthToken($auth_token);
       $device_type =$device_type==null? 'iphone5':$device_type;
       if(!$user){
           return new Response ('Authentication error');
       }
       
        $measurement = $user->getMeasurement();                
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
        $device_spec = $user->getDeviceSpecs($device_type);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);
        /*
        $edit_type=$edit_type==null || strtolower($edit_type)=='reg'?'registration':$edit_type;
         return new Response(json_encode(array(
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
            )));*/
        return $this->render('LoveThatFitUserBundle:Device:svg_path.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
                    'user_pixcel_height' => $device_spec==null?0:$device_spec->getUserPixcelHeight(),
                    'top_bar' => $user->getMeasurement()->getIphoneHeadHeight(),
                    'bottom_bar' => $user->getMeasurement()->getIphoneFootHeight(),
                    'per_inch_pixcel' => $device_spec==null?0:$device_spec->getDeviceUserPerInchPixelHeight(),
                    'device_type' => $device_type,
                    'device_screen_height' => $device_screen_height['pixel_height'],
            ));
    
}

   //--------------------------------- write bgcropped image from canvas
    public function bgCroppedImageUploadAction() {
        $id = $_POST['id'];
        $entity = $this->get('user.helper.user')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        $response = $entity->writeBGCroppedFromCanvas($_POST['imageData']);
        $this->get('user.helper.user')->setImageUpdateTimeToCurrent($entity);
        return new Response($response);
        
    }

#----------------------------------------------------------------------------
   public function editImageAuthAction() {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $auth_token=$user->getAuthToken();
        
        if($auth_token){
            return $this->redirect($this->generateUrl('device_browser_image_edit', array('auth_token'=>$auth_token, 'edit_type'=>'edit', 'device_type'=>'iphone6')));
        }else{
            return new Response('Authentication error');
        }
        
    }
#----------------------------------------------------------------------------    
     public function saveUserMarkerAction(Request $request)
    {
        $usermaker=$request->request->all();         
        if (array_key_exists('auth_token', $usermaker)){
            $user = $this->get('webservice.helper.user')->findByAuthToken($usermaker['auth_token']);
            $this->get('user.helper.measurement')->updateWithParams($user->getMeasurement(), $usermaker);        
            return new Response(json_encode($this->get('user.marker.helper')->fillMarker($user,$usermaker)));
        }else{
            return new Response('Authentication token not provided.');
        }
    }
    #--------------------------------------------------------------------------
    
    public function updateImageAction() {
        $auth_token = $_POST['auth_token'];
        $entity = $this->get('webservice.helper.user')->findByAuthToken($auth_token);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Member.');
        }
        $response = $entity->writeImageFromCanvas($_POST['imageData']);
        $entity->resize_image(); # image is being resized to 320x568
        $this->get('user.helper.user')->setImageUpdateTimeToCurrent($entity);
        return new Response($response);
    }
    
    #----------------------------------------------------------------------------
   public function fooAction() {
        $user = $this->get('webservice.helper.user')->find(1243);
        $device_type = 'iphone5';$edit_type = 'registration';
        $auth_token = $user->getAuthToken();
        if ($auth_token) {                        
            if (!$user) {
                return new Response('Authentication error');
            }
            $measurement = $user->getMeasurement();
            $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
            $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
            $form = $this->createForm(new RegistrationStepFourType(), $user);
            $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
            $marker = $this->get('user.marker.helper')->getByUser($user);
            $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
            $device_spec = $user->getDeviceSpecs($device_type);
            $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);

            return $this->render('LoveThatFitUserBundle:Device:device_foo.html.twig', array(
                        'form' => $form->createView(),
                        'measurement_form' => $measurement_form->createView(),
                        'measurement_vertical_form' => $measurement_vertical_form->createView(),
                        'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                        'entity' => $user,
                        'measurement' => $measurement,
                        'edit_type' => $edit_type,
                        'marker' => $marker,
                        'default_marker' => $default_marker,
                        'user_pixcel_height' => $device_spec->getUserPixcelHeight(),
                        'top_bar' => $user->getMeasurement()->getIphoneHeadHeight(),
                        'bottom_bar' => $user->getMeasurement()->getIphoneFootHeight(),
                        'per_inch_pixcel' => $device_spec->getDeviceUserPerInchPixelHeight(),
                        'device_type' => $device_type,
                        'device_screen_height' => $device_screen_height['pixel_height'],
                    ));
        } else {
            return new Response('Authentication error');
        }
    }

}

?>