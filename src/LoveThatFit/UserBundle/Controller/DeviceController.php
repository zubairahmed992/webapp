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
       $user = $this->get('user.helper.user')->findByAuthToken($auth_token);
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
       $user = $this->get('user.helper.user')->findByAuthToken($auth_token);
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
       $user = $this->get('user.helper.user')->findByAuthToken($auth_token);
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
        $device_spec = $user->getDeviceSpecs($device_type);
        $device_screen_height = $this->get('admin.helper.utility')->getDeviceResolutionSpecs($device_type);

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
                    'user_pixcel_height' => $device_spec == null ? 0 : $device_spec->getUserPixcelHeight(),
                    'top_bar' => $user->getMeasurement()->getIphoneHeadHeight(),
                    'bottom_bar' => $user->getMeasurement()->getIphoneFootHeight(),
                    'per_inch_pixcel' => $device_spec == null ? 0 : $device_spec->getDeviceUserPerInchPixelHeight(),
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
    
    public function updateImageAction() {
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
   public function fooAction() {

	 $user = $this->get('user.helper.user')->find('1846');
	$data = array(
	  "measurement" => '{"actual_user":{"body_type":"Regular","bra_size":"32D","height":72,"weight":123,"auth_token":"98225cbb99770c6478c697d537f63c71","email":"makepiece@ss.com","body_shape":"Rectangle","base_path":"http:\/\/192.168.0.203\/webapp\/web\/"},"masked_marker":{"bust":39.784177887419,"shoulder_across_front":17.473892384084,"waist":41.061956257092,"hip":38.489122100453,"inseam":30.789111873716,"thigh":21.630147481259}}',
	 "image_actions" => '{"move_up_down":-29,"move_left_right":0,"img_rotate":0}',
	 "marker_params" => '{"rect_x":32,"rect_y":52.5,"mask_x":158.5919784247323,"mask_y":544.5685208881938,"rect_height":12,"rect_width":15}',
	  "svg_paths" => 'M163.32413,97.93439c-2.20847,10.50671 -2.82382,16.09769 -2.15323,18.08203c-3.35294,-1.17723 0.96729,7.24672 1.30929,8.73676c1.98271,8.52954 2.75936,9.19975 2.984,8.67561c0.77006,2.16582 6.34106,15.28648 7.46877,17.49169c0.16876,3.9436 -5.60982,19.59692 -8.09547,21.81646c-5.90565,5.53871 -20.52782,6.56826 -29.09124,9.49462c-14.17289,5.53154 -11.47649,14.79226 -16.0365,45.83243c-0.24253,4.23253 -5.68094,15.89221 -8.31747,34.55595c-3.20877,24.06873 -8.44042,57.18041 -8.72206,82.91828c-7.36977,21.42414 -1.0832,35.18267 13.6094,47.44567c1.53006,1.36348 7.31819,4.40548 -1.48329,-18.87525c1.11318,-6.59654 1.6613,-17.37667 -1.32841,-29.57281c0.95559,-4.56923 13.91914,-37.13621 20.46744,-79.21917c1.235,-9.31396 4.863,-15.54764 5.9393,-24.51536c0.35094,-2.00344 -1.60316,-14.69092 0.12472,-15.93382c1.61053,1.30498 2.80041,5.23916 4.14718,13.52394c1.19141,7.79288 7.78183,27.66687 7.47112,36.64533c-0.37777,8.80653 -2.25347,10.1688 -3.38342,13.69691c-1.44177,5.1268 -4.05665,12.98374 -6.12765,21.0954c-3.20318,9.34142 -8.01694,26.57057 -7.229,38.50404c0.73206,14.31062 -4.96626,26.24316 -2.89414,41.891c5.25295,30.34888 16.52221,44.10536 16.65521,60.3454c-0.00671,12.90057 2.47094,6.45819 2.09541,20.80463c-1.41718,22.41153 -4.754,21.21293 -4.04541,32.60197c3.46471,27.77712 4.9489,35.3531 6.90813,50.97109c0.88853,9.76766 4.60031,10.3962 4.21584,27.88868c0.48729,5.05039 -10.14159,17.76236 -12.23382,22.97034c-3.76424,7.89914 -5.12942,18.13565 12.66353,18.09542c13.17372,0.04298 15.96013,5.887 16.35242,-17.00467c0.29618,-6.5464 0.35554,-27.25547 -0.35864,-32.05871c0.92765,-25.43699 3.50117,-46.14777 5.51406,-70.37768c0.21682,-7.98988 -0.49942,-31.59177 -0.75313,-37.86117c3.49265,-15.99169 3.22354,-34.22652 5.0006,-74.35739c0.00671,-8.76475 -0.03688,-22.71435 2.71365,-22.71435c2.63877,0 2.74201,10.51463 3.22483,22.66301c0.95782,40.16788 0.02248,58.61284 3.30613,74.2368c-0.10841,6.13568 -0.74754,29.95846 -0.72072,37.92087c2.32583,24.32781 -1.33495,39.96737 0.1627,67.01021c-1.13218,4.05225 -3.5847,30.71727 -2.11611,35.50498c2.11683,23.54549 9.15042,21.10376 18.68172,21.03332c20.90896,0.06594 4.62959,-26.75397 2.62006,-31.58826c-1.96483,-5.25455 -1.3277,-12.48801 -0.75994,-17.48945c-0.63035,-17.38264 3.90843,-22.56132 4.88191,-32.35406c1.71671,-15.76604 4.14953,-14.80262 7.43095,-42.31707c0.79688,-11.63499 -0.20312,-9.9734 -1.69182,-32.34076c-0.627,-9.75453 2.55476,-13.06522 2.21053,-21.09688c0.64488,-20.37347 9.39486,-29.04413 15.10715,-57.51135c2.2923,-17.56173 1.63239,-29.45368 2.49522,-41.85399c0.69071,-8.76833 -1.85188,-24.44759 -5.45294,-36.49091c-2.68906,-9.97302 -0.81524,-12.86819 -3.37242,-20.6145c-1.44847,-3.28932 -4.06194,-9.56947 -5.02647,-18.14199c0.09724,-8.69072 2.39677,-23.26666 4.5963,-37.67638c0.53871,-7.54812 4.39094,-16.47739 5.42142,-16.56693c1.72677,1.00411 0.98054,13.38264 1.14596,15.43384c0.93659,8.22031 0.80094,11.55898 3.94265,32.99864c4.28059,34.53604 13.25732,73.36845 13.63956,77.81948c-3.28477,11.95019 -7.31835,15.60226 -6.96741,18.50116c-7.8783,23.78934 1.39206,27.79724 3.35465,26.44689c16.58701,-12.33345 22.48085,-25.7295 18.30755,-41.91581c-1.05841,-45.04275 -5.73865,-67.62575 -8.52495,-88.48754c-2.81536,-18.60524 -3.8883,-27.35208 -4.47283,-31.60252c-4.00118,-31.19658 -0.23458,-42.99337 -13.56477,-47.84198c-10.54724,-4.37342 -27.35093,-6.53725 -37.11359,-14.11999c-3.34289,-2.45356 -5.306,-14.75345 -5.50271,-18.53467c1.24171,-1.96046 7.60889,-15.20727 7.86818,-16.15885c2.12241,2.14074 4.4453,-9.76914 4.87671,-11.07651c2.98971,-9.27217 -4.77494,-8.52689 -5.17059,-8.25467c3.81118,-6.87234 -0.12682,-15.42841 -0.51129,-17.12501c-4.26494,-15.84842 -22.80784,-16.25078 -22.80784,-16.25078c-19.36549,0 -22.53178,15.1416 -22.88496,16.24839z',
	"marker_json" => '[[326.6482544675684,195.86878438566256],[322.3417944675684,232.03284438566257],[324.9603744675684,249.50636438566258],[330.9283744675684,266.85758438566256],[345.8659144675684,301.8409643856626],[329.6749744675684,345.4738843856626],[271.4924944675684,364.4631243856626],[239.4194944675684,456.12798438566256],[222.7845544675684,525.2398843856625],[205.3404344675684,691.0764443856625],[232.5592344675684,785.9677843856625],[229.5926544675684,748.2172843856625],[226.93583446756838,689.0716643856625],[267.8707144675684,530.6333243856625],[279.7493144675684,481.6026043856625],[279.9987544675684,449.7349643856625],[288.2931144675684,476.78284438566254],[303.2353544675684,550.0735043856625],[296.4685144675684,577.4673243856626],[284.21321446756843,619.6581243856625],[269.7552144675684,696.6662043856625],[263.9669344675684,780.4482043856625],[297.27735446756844,901.1390043856625],[301.4681744675684,942.7482643856624],[293.3773544675684,1007.9522043856624],[307.1936144675684,1109.8943843856623],[315.62529446756844,1165.6717443856624],[291.1576544675684,1211.6124243856623],[316.48471446756844,1247.8032643856623],[349.1895544675684,1213.7939243856622],[348.4722744675684,1149.6765043856622],[359.5003944675684,1008.9211443856622],[357.9941344675684,933.1988043856621],[367.9953344675684,784.4840243856621],[373.4226344675684,739.0553243856621],[379.8722944675684,784.381344385662],[386.4845544675684,932.8549443856621],[385.0431144675684,1008.696684385662],[385.3685144675684,1142.717104385662],[381.1362944675684,1213.727064385662],[418.4997344675684,1255.793704385662],[423.73985446756836,1192.617184385662],[422.21997446756836,1157.638284385662],[431.9837944675684,1092.930164385662],[446.84569446756836,1008.296024385662],[443.46205446756835,943.614504385662],[447.88311446756836,901.420744385662],[478.09741446756834,786.398044385662],[483.0878544675683,702.690064385662],[472.1819744675683,629.708244385662],[465.4371344675683,588.479244385662],[455.3841944675683,552.195264385662],[464.57679446756833,476.842504385662],[475.41963446756836,443.70864438566196],[477.71155446756836,474.57632438566196],[485.59685446756833,540.573604385662],[512.8759744675683,696.212564385662],[498.9411544675683,733.2148843856619],[505.6504544675683,786.1086643856619],[542.2655544675683,702.2770443856618],[525.2156544675684,525.3019643856619],[516.2699944675684,462.0969243856619],[489.14045446756836,366.4129643856619],[414.9132744675684,338.1729843856619],[403.90785446756837,301.1036443856619],[419.64421446756836,268.7859443856619],[429.39763446756837,246.63292438566188],[419.05645446756836,230.12358438566187],[418.03387446756835,195.87356438566187],[372.41819446756836,163.37200438566185],[326.64827446756834,195.86878438566185]]',
	  'default_marker_svg' => 'M164.58956,98.6156c-2.20847,10.50671 0.67618,15.59769 1.34677,17.58203c-3.35294,-1.17723 -1.03271,6.24672 -0.69071,7.73676c1.98271,8.52954 4.25936,7.69975 4.484,7.17561c0.77006,2.16582 2.84106,13.78648 3.96877,15.99169c0.16876,3.9436 -1.10982,14.01333 -3.59547,16.23287c-5.90565,5.53871 -28.02782,9.65185 -36.59124,12.57821c-14.17289,5.53154 -11.97649,24.79226 -16.5365,55.83243c-0.24253,4.23253 -1.68094,12.89221 -4.31747,31.55595c-3.20877,24.06873 -4.44042,52.68041 -4.72206,78.41828c-7.36977,21.42414 -8.5832,29.59908 6.1094,41.86208c1.53006,1.36348 14.31819,10.98907 5.51671,-12.29166c1.11318,-6.59654 5.1613,-17.37667 2.17159,-29.57281c0.95559,-4.56923 5.41914,-32.13621 11.96744,-74.21917c1.235,-9.31396 3.363,-22.54764 4.4393,-31.51536c0.35094,-2.00344 1.39684,-18.27451 3.12472,-19.51741c1.61053,1.30498 3.30041,3.32275 4.64718,11.60753c1.19141,7.79288 2.78183,29.16687 2.47112,38.14533c-0.37777,8.80653 -3.25347,14.6688 -4.38342,18.19691c-1.44177,5.1268 -4.55665,14.48374 -6.62765,22.5954c-3.20318,9.34142 -2.51694,27.57057 -1.729,39.50404c0.73206,14.31062 3.03374,26.74316 5.10586,42.391c5.25295,30.34888 9.02221,46.10536 9.15521,62.3454c-0.00671,12.90057 -0.02906,4.45819 -0.40459,18.80463c-1.41718,22.41153 -1.254,23.71293 -0.54541,35.10197c3.46471,27.77712 5.4489,33.8531 7.40813,49.47109c0.88853,9.76766 3.10031,11.3962 2.71584,28.88868c0.48729,5.05039 -0.14159,8.76236 -2.23382,13.97034c-3.76424,7.89914 -12.62942,24.63565 5.16353,24.59542c13.17372,0.04298 14.46013,-1.69659 14.85242,-24.58826c0.29618,-6.5464 -0.64446,-9.17188 -1.35864,-13.97512c0.92765,-25.43699 3.50117,-54.14777 5.51406,-78.37768c0.21682,-7.98988 -0.49942,-31.59177 -0.75313,-37.86117c3.49265,-15.99169 4.22354,-38.22652 6.0006,-78.35739c0.00671,-8.76475 -1.53688,-28.79794 1.21365,-28.79794c2.63877,0 0.74201,16.59822 1.22483,28.7466c0.95782,40.16788 2.52248,62.61284 5.80613,78.2368c-0.10841,6.13568 -0.74754,29.95846 -0.72072,37.92087c2.32583,24.32781 4.16505,51.46737 5.6627,78.51021c-1.13218,4.05225 -2.5847,9.13368 -1.11611,13.92139c2.11683,23.54549 6.15042,24.68735 15.68172,24.61691c20.90896,0.06594 5.62959,-19.75397 3.62006,-24.58826c-1.96483,-5.25455 -2.3277,-8.98801 -1.75994,-13.98945c-0.63035,-17.38264 2.90843,-19.06132 3.88191,-28.85406c1.71671,-15.76604 3.14953,-21.80262 6.43095,-49.31707c0.79688,-11.63499 0.79688,-12.9734 -0.69182,-35.34076c-0.627,-9.75453 0.05476,-10.56522 -0.28947,-18.59688c0.64488,-20.37347 3.39486,-34.04413 9.10715,-62.51135c2.2923,-17.56173 4.13239,-29.95368 4.99522,-42.35399c0.69071,-8.76833 2.14812,-27.44759 -1.45294,-39.49091c-2.68906,-9.97302 -4.31524,-14.86819 -6.87242,-22.6145c-1.44847,-3.28932 -3.56194,-9.56947 -4.52647,-18.14199c0.09724,-8.69072 0.39677,-23.76666 2.5963,-38.17638c0.53871,-7.54812 3.39094,-11.47739 4.42142,-11.56693c1.72677,1.00411 2.98054,17.38264 3.14596,19.43384c0.93659,8.22031 1.30094,10.05898 4.44265,31.49864c4.28059,34.53604 11.75732,69.78486 12.13956,74.23589c-3.28477,11.95019 2.18165,26.68585 2.53259,29.58475c-7.8783,23.78934 3.39206,13.71365 5.35465,12.3633c16.58701,-12.33345 11.98085,-25.7295 7.80755,-41.91581c-1.05841,-45.04275 -2.23865,-57.54216 -5.02495,-78.40395c-2.81536,-18.60524 -3.8883,-27.35208 -4.47283,-31.60252c-4.00118,-31.19658 -4.73458,-50.99337 -18.06477,-55.84198c-10.54724,-4.37342 -26.85093,-5.03725 -36.61359,-12.61999c-3.34289,-2.45356 -3.306,-12.25345 -3.50271,-16.03467c1.24171,-1.96046 3.60889,-15.20727 3.86818,-16.15885c2.12241,2.14074 3.9453,-5.76914 4.37671,-7.07651c2.98971,-9.27217 -0.27494,-8.02689 -0.67059,-7.75467c3.81118,-6.87234 1.87318,-15.92841 1.48871,-17.62501c-4.26494,-15.84842 -22.80784,-16.25078 -22.80784,-16.25078c-19.36549,0 -22.53178,15.1416 -22.88496,16.24839z'
	);
 	 $user_archive = $this->get('user.helper.userarchives')->createNew($user);
	 $this->get('user.helper.userarchives')->saveArchives($user_archive,$data);
	 echo "record updated";
	 die;
   }

}

?>