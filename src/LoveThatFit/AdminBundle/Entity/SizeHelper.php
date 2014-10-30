<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class SizeHelper {

    protected $conf;
    
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/sizes_ltf.yml'));
        $this->constant = $this->conf["constants"];
    }

#-------------------Fetch All Sizes -------------------------------------------#
public function getAllSizes(){
  return array(
      'woman_letter_sizes'=>$this->getWomanLetterSizes(),
      'woman_number_sizes'=>$this->getWomanNumberSizes(),
      'woman_waist_sizes'=>$this->getWomanWaistSizes(),
      'woman_bra_sizes'=>$this->getWomanBraSizes(),
      'man_letter_sizes'=>$this->getManLetterSizes(),
      'man_chest_sizes'=>$this->getManChestSizes(),
      'man_waist_sizes'=>$this->getManWaistSizes(),
      'man_shirt_sizes'=>$this->getManShirtSizes(),
      'man_neck_sizes'=>$this->getManNeckSizes(),
      'sleeve' => $this->getManSleeveSizes(),
      'inseam' => $this->getManInseamSizes(),
      );
}


#-------------------#-------------------#-------------------#-------------------
    public function getDefaultArray() {
        return array(
            'body_shapes' => array(
                'man' => $this->getManBodyShape(),
                'woman' => $this->getWomanBodyShape(),
            ),
            'fit_types' => array(
                'man' => $this->getManFitType(),
                'woman' => $this->getWomanFitType(),
            ),
            'sizes' => array(
                'woman' => array(
                    'letter' => $this->getWomanLetterSizes(),
                    'number' => $this->getWomanNumberSizes(),
                    'waist' => $this->getWomanWaistSizes(),
                    'bra' => $this->getWomanBraSizes(),
                    'bra_cup' => $this->getWomanBraCups(),
                    'bra_band' => $this->getWomanBraBands(),
                    
                ),
                'man' => array(
                    'letter' => $this->getManLetterSizes(),
                    'chest' => $this->getManChestSizes(),
                    'waist' => $this->getManWaistSizes(),
                    'shirt' => $this->getManShirtSizes(),
                    'neck' => $this->getManNeckSizes(),
                    'sleeve' => $this->getManSleeveSizes(),
                    'inseam' => $this->getManInseamSizes(),
                ),
            ),
        );
    }

#--------------------Woman Sizes Start Here-------------------------------------#

#---------------------- Get Woman Letter Sizes---------------------------------#
  public function getWomanLetterSizes($key_pair=true){
    return $this->getArray($this->constant['size_titles']['woman']['letter'], $key_pair);  
}
#-----------------------Get Woman Number Sizes----------------------------------#
public function getWomanNumberSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['woman']['number'], $key_pair);
    
}
#------------------------Get Woman Waist Sizes---------------------------------#
public function getWomanWaistSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['woman']['waist'], $key_pair);
}
#-----------------------Get Woman Bra Size ------------------------------------#
public function getWomanBraSizes($key_pair=true){
     return $this->getWomanBraSizesParsed(null, $key_pair);    
}
public function getWomanBraCups($key_pair=true){
     return $this->getWomanBraSizesParsed('cup', $key_pair);
}
public function getWomanBraBands($key_pair=true){
    return $this->getWomanBraSizesParsed('band', $key_pair);    
}

private function getWomanBraSizesParsed($type=null, $key_pair=true){
     $new_arr=array();
     $arr=$this->constant['size_titles']['woman']['bra'];
    foreach($arr as $key){
        $element='';
        if ($type=='cup'){
            $element=trim(str_replace(range(0,9),'',$key['title']));
        }elseif($type=='band'){
            $cup=trim(str_replace(range(0,9),'',$key['title']));
            $element=trim(str_replace($cup,'',$key['title']));
        }else{
            $element=$key['title'];
        } 
        if($key_pair)
            $new_arr[$element]=$element;   
        else
            array_push ($new_arr, $element);
        
    }
    return $new_arr;
}
public function getWomanBraSpecs($bra_size){    
     $arr=$this->constant['size_titles']['woman']['bra'];
    foreach($arr as $key=>$val){
        if($key==$bra_size){            
            return array(
                    'title' => array_key_exists('title', $val)?$val['title']:null,
                    'size' => array_key_exists('size', $val)?$val['size']:null,
                    'cup' => array_key_exists('cup', $val)?$val['cup']:null,
                    'low' => array_key_exists('low', $val)?$val['low']:null,
                    'high' => array_key_exists('high', $val)?$val['high']:null,
                    'average' => array_key_exists('average', $val)?$val['average']:null,
                    'shoulder_across_back' => array_key_exists('shoulder_across_back', $val)?$val['shoulder_across_back']:null,
                );
            break;
        }
    }
    return null;
}
#------------------------Getting All Male Sizes--------------------------------#
#---------------------- Get Man Letter Sizes---------------------------------#
  public function getManLetterSizes($key_pair=true){
        return $this->getArray($this->constant['size_titles']['man']['letter'], $key_pair);
}
#-----------------------Get Man Number Sizes----------------------------------#
public function getManChestSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['man']['chest'], $key_pair);
    
}
#------------------------Get Man Waist Sizes---------------------------------#
public function getManWaistSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['man']['waist'], $key_pair);
}
#--------------------Get Man Shirt Sizes---------------------------------------#
public function getManShirtSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['man']['shirt'], $key_pair);
}

public function getManShirtSpecs($neck, $sleeve){
    $arr=$this->constant['size_titles']['man']['shirt'];
    foreach($arr as $key=>$val){
        if($val['neck']==$neck && $val['sleeve_size']==$sleeve){            
            return array(
                    'title' => array_key_exists('title', $val)?$val['title']:null,
                    'neck' => array_key_exists('neck', $val)?$val['neck']:null,
                    'arm_length' => array_key_exists('arm_length', $val)?$val['arm_length']:null,
                    'half_shoulder' => array_key_exists('half_shoulder', $val)?$val['half_shoulder']:null,
                    'shoulder_across_back' => array_key_exists('shoulder', $val)?$val['shoulder']:null,
                    'sleeve_size' => array_key_exists('sleeve_size', $val)?$val['sleeve_size']:null,                  
                );
            break;
        }
    }
    return null;
}
#------------------------------------------------
public function getManSleeveSizes(){
     return $this->constant['size_titles']['man']['sleeve'];
}
#------------------------------------------------
public function getManInseamSizes(){
     return $this->constant['size_titles']['man']['inseam'];
}

#--------------------Get Man Neck Sizes---------------------------------------#
public function getManNeckSizes($key_pair=true){
     return $this->getArray($this->constant['size_titles']['man']['neck'], $key_pair);
}

#------------------Get Size Title type ----------------------------------------#
public function getAllSizeTitleType(){
     return ($this->constant['size_title_type']);
}
public function getManSizeTitleType(){
     return ($this->constant['size_title_type']['man']);
}
public function getWomanSizeTitleType(){
     return ($this->constant['size_title_type']['woman']);
}
#----------------Get Fit Type --------------------------------------------------#
public function getAllFitType(){
     return ($this->constant['fit_type']);
}
public function getManFitType(){
     return ($this->constant['fit_type']['man']);
}
public function getWomanFitType(){
     return ($this->constant['fit_type']['woman']);
}
  
private function get_fit_type_array($gender) {
        if ($gender == 'm')
            return $this->getManFitType();
        if ($gender == 'f')
            return $this->getWomanFitType();
        return null;
    }

#----------------Get body shape --------------------------------------------------#
public function getAllBodyShape(){
     return ($this->constant['body_shape']);
}
public function getManBodyShape(){
     return ($this->constant['body_shape']['man']);
}
public function getWomanBodyShape(){
     return ($this->constant['body_shape']['woman']);
}
#---------------------------Sorting Of Array----------------------------------#
public function getArray($arr, $key_pair=true){
   
    $new_arr=array();
    foreach($arr as $key){
        if ($key_pair)
            $new_arr[$key['title']]=$key['title'];
        else
            array_push ($new_arr, $key['title']);
    }
    return $new_arr;
    
}
 #------------------------------------------------------
    public function getSizeArray($gender, $type) {        
        $fit_type=  $this->get_fit_type_array($gender);
        $size_array=  $this->get_size_array($gender, $type);
        $body_type_sizes=array();
        if (count($fit_type)>0){
            foreach ($fit_type as $ft => $v) {
                $body_type_sizes[$v]=$size_array;        
            }  
        }
        return $body_type_sizes;
    }
  
    #----------------------------------------------
    private function get_size_array($gender, $type){
        switch ($gender){
            case 'm':
                switch ($type){
                    case 'chest':          
                        return $this->getManChestSizes();
                        break;
                    case 'shirt':         
                        return $this->getManShirtSizes();
                        break;
                    case 'letter':        
                        return $this->getManLetterSizes();
                        break;
                    case 'waist':          
                        return $this->getManWaistSizes();
                        break;
                    case 'neck':          
                        return $this->getManNeckSizes();
                        break;
                    default :                
                        break;
                }
                break;
            case 'f':
                switch ($type){
                    case 'number':       
                        return $this->getWomanNumberSizes();
                        break;
                    case 'letter':                
                        return $this->getWomanLetterSizes();
                        break;
                    case 'waist':            
                        return $this->getWomanWaistSizes();
                        break;
                    case 'bra':             
                        return $this->getWomanBraSizes();
                        break;
                    default :                
                        break;                    
                }
                break;
            default:
                break;
        }
        return null;
    }
}