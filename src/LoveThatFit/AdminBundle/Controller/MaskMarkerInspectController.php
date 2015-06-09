<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;


class MaskMarkerInspectController extends Controller {

    public function indexAction(){
        $form=$this->createForm(new \LoveThatFit\UserBundle\Form\Type\UserDropdownType());
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:index.html.twig', array(
                    'form' => $form->createView(),                    
                ));
    }   
    
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
    
     public function pathAxisArrayAction($id){
        $user = $this->get('user.helper.user')->find($id);
        $mm_specs=$this->getMaskedMarkerSpecs();        
        $mm_cordinates = $this->get('user.marker.helper')->getAxisArray($user, $mm_specs);
        return new Response(json_encode($mm_cordinates));
        
    }  
    
    private function getMaskedMarkerSpecs() {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/mask_marker.yml'));
    }
     
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

#############
    public function _userDataDownloadAction() {
        $users = $this->get('user.helper.user')->findAll();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="user_data.csv";');
        // open the "output" stream
        $f = fopen('php://output', 'w');
         $headers= array(
            'id',
            'email', 
            'name',
            'zipcode',
            'gender',
            'birth_date',
            'weight',
            'height',
            'waist',
            'belt',
            'hip',
            'bust',
            'chest',
            'arm',
            'inseam',
            'shoulder_height',
            'outseam',
            'sleeve',
            'neck',
            'thigh',
            'center_front_waist',
            'shoulder_across_front',
            'shoulder_across_back',
            'bicep',
            'tricep',
            'wrist',
            'back_waist', 
            'waist_hip',
            'knee',
            'calf',
            'ankle',
        );
         fputcsv($f, $headers);
        foreach ($users as $u) {
            //generate csv lines from the inner arrays
            fputcsv($f, $u->toDataArray(false));
        }
        return new Response('');
    }
    
}
