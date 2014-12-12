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
            'genders' => array(
                'titles' => $this->getGenders(),
                'descriptions' => $this->getGenderDescriptionArray(),
            ),
            'body_shapes' => array(
                'man' => $this->getManBodyShape(),
                'woman' => $this->getWomanBodyShape(),
            ),
            'fit_types' => array(
                'man' => $this->getManFitType(),
                'woman' => $this->getWomanFitType(),
            ),
            'targets' => array(
                'man' => $this->getManTarget(),
                'woman' => $this->getWomanTarget(),
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
          'size_title_type'=>array(
                'man'=>$this->getSizeTitleType('m'),
                'woman'=>$this->getSizeTitleType('f'),
                
              
              
          ),
        );
    }
    #==============================================
    public function getByGender($gender = null) {
        $specs = array(
            'man' => array(
                'body_shapes' => $this->getManBodyShape(),
                'fit_types' => $this->getManFitType(),
                'targets' => $this->getManTarget(),
                'sizes' => array(
                    'letter' => $this->getManLetterSizes(),
                    'chest' => $this->getManChestSizes(),
                    'waist' => $this->getManWaistSizes(),
                    'shirt' => $this->getManShirtSizes(),
                    'neck' => $this->getManNeckSizes(),
                    'sleeve' => $this->getManSleeveSizes(),
                    'inseam' => $this->getManInseamSizes(),
                ),
                'size_title_type' => $this->getSizeTitleType('m'),
            ),
            'woman' => array(
                'body_shapes' => $this->getWomanBodyShape(),
                'fit_types' => $this->getWomanFitType(),
                'targets' => $this->getWomanTarget(),
                'sizes' => array(
                    'letter' => $this->getWomanLetterSizes(),
                    'number' => $this->getWomanNumberSizes(),
                    'waist' => $this->getWomanWaistSizes(),
                    'bra' => $this->getWomanBraSizes(),
                    'bra_cup' => $this->getWomanBraCups(),
                    'bra_band' => $this->getWomanBraBands(),
                ),
                'size_title_type' => $this->getSizeTitleType('f'),
            ),
        );
        if ($gender == 'm') {
            return $specs['man'];
        } elseif ($gender == 'f') {
            return $specs['woman'];
        } else {
            return $specs;
        }
    }
#############################################################################
public function getGenders($key_pair=true){
     return $this->getArray($this->constant['genders'], $key_pair);    
}
#------------------------------------
public function getGenderDescriptionArray(){
    $arr=$this->constant['genders'];
    $new_arr=array();
    foreach($arr as $key){        
            $new_arr[$key['title']]=$key['description'];        
    }
    return $new_arr;   
}

#############################################################################
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
#------------------------------------------
public function getWomanBraCups($key_pair=true){
     return $this->getWomanBraSizesParsed('cup', $key_pair);
}
#------------------------------------------
public function getWomanBraBands($key_pair=true){
    return $this->getWomanBraSizesParsed('band', $key_pair);    
}
#------------------------------------------
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
#------------------------------------------
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
#------------------------------------------------------------------------------#
public function getBustAverage($bra_size){
    $bra_specs=$this->getWomanBraSpecs($bra_size);
    if (is_array($bra_specs)){
        return $bra_specs['average'];
    }else{
        return;
    }
    
}
#############################################################################

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
#------------------------------------------
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


#############################################################################
#------------------Get Size Title type ----------------------------------------#
public function getAllSizeTitleType(){
     return ($this->constant['size_title_type']);
}
#-----------------
public function getSizeTitleType($gender='f',$key_pair=true){
    if ($gender=='f')
        return ($this->getWomanSizeTitleType($key_pair));
    else
        return ($this->getManSizeTitleType($key_pair));
}
#-----------------
public function getManSizeTitleType($key_pair=true){
    return $this->transformArray($this->constant['size_title_type']['man'], $key_pair);
}
#-----------------
public function getWomanSizeTitleType($key_pair=true){
    return $this->transformArray($this->constant['size_title_type']['woman'], $key_pair);    
}


#############################################################################
#----------------Get Fit Type --------------------------------------------------#
public function getAllFitType(){
     return ($this->constant['fit_type']);
}
#-----------------
public function getFitType($gender='f',$key_pair=true){
    if ($gender=='f')
        return ($this->getWomanFitType($key_pair));
    else
        return ($this->getManFitType($key_pair));
}
#-----------------
public function getManFitType($key_pair=true){
    return $this->transformArray($this->constant['fit_type']['man'], $key_pair);    
}
#-----------------
public function getWomanFitType($key_pair=true){
    return $this->transformArray($this->constant['fit_type']['woman'], $key_pair);    
}
#-----------------  
private function get_fit_type_array($gender) {
        if ($gender == 'm')
            return $this->getManFitType();
        if ($gender == 'f')
            return $this->getWomanFitType();
        return null;
    }
    
    
#############################################################################
#----------------Get body shape --------------------------------------------------#
public function getAllBodyShape(){
     return ($this->constant['body_shape']);
}
#-------------------------
public function getBodyShape($gender='f',$key_pair=true){
    if ($gender=='f')
        return ($this->getWomanBodyShape($key_pair));
    else
        return ($this->getManBodyShape($key_pair));
}
#--------------------------------
public function getManBodyShape($key_pair=true){
     return  $this->transformArray($this->constant['body_shape']['man'], $key_pair);
}
#--------------------------------
public function getWomanBodyShape($key_pair=true){
     return  $this->transformArray($this->constant['body_shape']['woman'], $key_pair);
}

#############################################################################

public function getAllTarget(){
     return ($this->constant['targets']);
}
#-------------------------
public function getTarget($gender='f',$key_pair=true){
    if ($gender=='f')
        return ($this->getWomanTarget($key_pair));
    else
        return ($this->getManTarget($key_pair));
}
#--------------------------------
public function getManTarget($key_pair=true){
     return  $this->transformArray($this->constant['targets']['man'], $key_pair);
}
#--------------------------------
public function getWomanTarget($key_pair=true){
     return  $this->transformArray($this->constant['targets']['woman'], $key_pair);
}

#############################################################################
#---------------------------Sorting Of Array----------------------------------#

private function getArray($arr, $key_pair=true, $field='title'){   
    $new_arr=array();
    foreach($arr as $key){
        if ($key_pair)
            $new_arr[$key[$field]]=$key[$field];
        else
            array_push ($new_arr, strval($key[$field]));
    }
    return $new_arr;    
}
#-------------------------------
private function transformArray($arr, $key_pair=true){
    if($key_pair)
        return $arr;
    else
        return array_keys($arr);
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