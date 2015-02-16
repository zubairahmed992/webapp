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
            return $this->redirect($this->generateUrl('device_browser_image_edit', array('auth_token'=>$auth_token)));
        }else{
            return new Response('Authentication error');
        }
        
    }
}

?>