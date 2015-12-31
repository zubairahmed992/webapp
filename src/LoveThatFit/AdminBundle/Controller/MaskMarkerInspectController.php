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

class MaskMarkerInspectController extends Controller {

    public function indexAction(){
        $form=$this->createForm(new \LoveThatFit\UserBundle\Form\Type\UserDropdownType());
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:index.html.twig', array(
                    'form' => $form->createView(),                    
                ));
    }   
    #-----------------------------------------------------------------------------
    public function userAction($id, $mode=null){
        $user = $this->get('user.helper.user')->find($id);
        $mm_specs=$this->getMaskedMarkerSpecs();
        $ub_specs=$user->getMeasurement()->getArray();
        $user_mm_comparison = $this->get('user.marker.helper')->getComparisionArray($user);
        
        if ($mode && $mode=='json'){
            return new Response(json_encode($user_mm_comparison));
        }
                
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_summary.html.twig', array(
                    'user' => $user,                    
                    'specs'=>$mm_specs,
                    'body_measurement'=>$ub_specs,
                    'specs_comparison'=>$user_mm_comparison,
                ));
    }  
    #-----------------------------------------------------------------------------
     public function pathAxisArrayAction($id){
        $user = $this->get('user.helper.user')->find($id);
        $mm_specs=$this->getMaskedMarkerSpecs();        
        $mm_cordinates = $this->get('user.marker.helper')->getAxisArray($user, $mm_specs);
        return new Response(json_encode($mm_cordinates));
        
    }  
    #-----------------------------------------------------------------------------
    private function getMaskedMarkerSpecs() {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/mask_marker.yml'));
    }
     #-----------------------------------------------------------------------------
    public function simAction($id=6){
        $user = $this->get('user.helper.user')->find($id);
        $measurement = $user->getMeasurement();
        $marker = $this->get('user.marker.helper')->getByUser($user);
                        
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_masked_marker_sim.html.twig', array(
                    'entity' => $user,
                    'measurement' => $measurement,                    
                    'marker' => $marker,
                ));
    }   
    

###################################################################################
###################################################################################
#-----------------------------------------------------------------------------
    
      public function allUserDataAction() {
        #$users = $this->get('user.helper.user')->findAll();  
        $users = null;
        $user_array=array();        
        $mm_specs=$this->getMaskedMarkerSpecs(); 
        if($users){
        foreach ($users as $u) {
            array_push($user_array, $this->get('user.marker.helper')->getMixMeasurementArray($u, $mm_specs));
           #array_push($user_array, $u->toDataArray(false));  
        }        
        }
        #return new Response(json_encode($user_array));
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:user_array_data.html.twig', array(                    
                    'users' => $user_array,
         ));
    }
#-----------------------------------------------------------------------------
    
      public function filterUserDataAction() {
          
        $decoded = $this->getRequest()->request->all();
        $users = $this->get('user.helper.user')->findUserByOptions($decoded);
        $user_array = array();
        $mm_specs = $this->getMaskedMarkerSpecs();
     
            $male_count = 0;
            $female_count = 0;
            foreach ($users as $u) {
                array_push($user_array, $this->get('user.marker.helper')->getMixMeasurementArray($u, $mm_specs));
                if (strtolower($u->getGender()) == 'm') {
                    $male_count = $male_count + 1;
                } else {
                    $female_count = $female_count + 1;
                }
            }
            return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_filter_data.html.twig', array(
                        'users' => $user_array,
                        'users_count' => count($user_array),
                        'male_count' => $male_count,
                        'female_count' => $female_count
                    ));
        
    }
#----------------------------------------------------------------------------------     
    
      public function filterUserDataDownloadAction($options=null) {
         $ar = explode("_", $options); #array of options from string (gender_from-id_to-id) (f_10_108) (none__)
         #none in the option string to be converted to null for gender
        $params = array('gender'=>$ar[0]=='none'?null:$ar[0],'from_id'=> $ar[1], 'to_id'=>$ar[2]);  
        $users = $this->get('user.helper.user')->findUserByOptions($params);
        $mm_specs=$this->getMaskedMarkerSpecs(); 
        #----------------------------------
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="user_data.csv";');
        $f = fopen('php://output', 'w');
        $is_first_element=true;
        foreach ($users as $u) {
            $mix_measurement=$this->get('user.marker.helper')->getMixMeasurementArray($u, $mm_specs);
            if ($is_first_element){                
                fputcsv($f, array_keys($mix_measurement));
                fputcsv($f, $mix_measurement);
                $is_first_element=false;
            }else{
                fputcsv($f, $mix_measurement);
            }
             
        }
        fclose($f);        
        return new Response('');
    }
#----------------------------------------------------------------------------------     
    
      public function userDataDownloadAction() {
        $users = $this->get('user.helper.user')->findAll();          
        $mm_specs=$this->getMaskedMarkerSpecs(); 
        #----------------------------------
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="user_data.csv";');
        $f = fopen('php://output', 'w');
        $is_first_element=true;
        foreach ($users as $u) {
            $mix_measurement=$this->get('user.marker.helper')->getMixMeasurementArray($u, $mm_specs);
            if ($is_first_element){                
                fputcsv($f, array_keys($mix_measurement));
                fputcsv($f, $mix_measurement);
                $is_first_element=false;
            }else{
                fputcsv($f, $mix_measurement);
            }
             
        }
        fclose($f);        
        return new Response('true');
    }

###################################################################################
###################################################################################

    public function maskCompareIndexAction(){
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:mask_compare_index.html.twig', array(
    
                    ));
        
    }
#-------------------------------------------------------
    public function maskCompareFilterAction() {
        $decoded = $this->getRequest()->request->all();
        $ar = explode(',', $decoded['user_ids']);
        $users = $this->get('user.helper.user')->findWhereIdIn($ar);
        $user_array = array();

        foreach ($users as $u) {
            array_push($user_array, $u->toDetailArray(array('user', 'mask_marker', 'measurement', 'device')));
        }
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:mask_compare_show.html.twig', array(
                    'user_json' => json_encode($user_array),
                ));
    }

    ###################################################################################
###################################################################################

    public function maskImproveIndexAction(){
          $form=$this->createForm(new \LoveThatFit\UserBundle\Form\Type\UserDropdownType());
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:mask_improve_index.html.twig', array(
                    'form' => $form->createView(),                    
                ));
    }

     #---------------------------------------------------------------------------
    
   public function maskImproveEditAction($user_id) {
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
        
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_mask_improve_show.html.twig', array(
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
