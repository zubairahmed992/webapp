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
  return array('woman_letter_sizes'=>$this->getWomanLetterSizes(),
           'woman_number_sizes'=>$this->getWomanNumberSizes()
          ,'woman_waist_sizes'=>$this->getWomanWaistSizes(),
         'woman_bra_sizes'=>$this->getWomanBraSizes(),
       'man_letter_sizes'=>$this->getManLetterSizes(),
      'man_chest_sizes'=>$this->getManChestSizes(),
      'man_waist_sizes'=>$this->getManWaistSizes(),
      'man_shirt_sizes'=>$this->getManShirtSizes(),
      'man_neck_sizes'=>$this->getManNeckSizes());
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
                ),
                'man' => array(
                    'letter' => $this->getManLetterSizes(),
                    'chest' => $this->getManChestSizes(),
                    'waist' => $this->getManWaistSizes(),
                    'shirt' => $this->getManShirtSizes(),
                    'neck' => $this->getManNeckSizes(),
                ),
            ),
        );
    }

#--------------------Woman Sizes Start Here-------------------------------------#

#---------------------- Get Woman Letter Sizes---------------------------------#
  public function getWomanLetterSizes(){
        return $this->getArray($this->constant['size_titles']['women']['letter']);
}
#-----------------------Get Woman Number Sizes----------------------------------#
public function getWomanNumberSizes(){
     return $this->getArray($this->constant['size_titles']['women']['number']);
    
}
#------------------------Get Woman Waist Sizes---------------------------------#
public function getWomanWaistSizes(){
     return $this->getArray($this->constant['size_titles']['women']['waist']);
}
#-----------------------Get Woman Bra Size ------------------------------------#
public function getWomanBraSizes(){
     return $this->getArray($this->constant['size_titles']['women']['bra']);
}
#------------------------Getting All Male Sizes--------------------------------#
#---------------------- Get Man Letter Sizes---------------------------------#
  public function getManLetterSizes(){
        return $this->getArray($this->constant['size_titles']['man']['letter']);
}
#-----------------------Get Man Number Sizes----------------------------------#
public function getManChestSizes(){
     return $this->getArray($this->constant['size_titles']['man']['chest']);
    
}
#------------------------Get Man Waist Sizes---------------------------------#
public function getManWaistSizes(){
     return $this->getArray($this->constant['size_titles']['man']['waist']);
}
#--------------------Get Man Shirt Sizes---------------------------------------#
public function getManShirtSizes(){
     return $this->getArray($this->constant['size_titles']['man']['shirt']);
}
#--------------------Get Man Neck Sizes---------------------------------------#
public function getManNeckSizes(){
     return $this->getArray($this->constant['size_titles']['man']['neck']);
}

#------------------Get Size Title type ----------------------------------------#
public function getAllSizeTitleType(){
     return ($this->constant['size_title_type']);
}
#----------------Get Fit Type --------------------------------------------------#
public function getAllFitType(){
     return ($this->constant['fit_type']);
}
public function getManFitType(){
     return ($this->constant['fit_type']['male']);
}
public function getWomanFitType(){
     return ($this->constant['fit_type']['female']);
}
#----------------Get body shape --------------------------------------------------#
public function getAllBodyShape(){
     return ($this->constant['body_shape']);
}
public function getManBodyShape(){
     return ($this->constant['body_shape']['men']);
}
public function getWomanBodyShape(){
     return ($this->constant['body_shape']['women']);
}
#---------------------------Sorting Of Array----------------------------------#
public function getArray($arr){
   
    $new_arr=array();
    foreach($arr as $key){
        $new_arr[$key['title']]=$key['title'];
    }
    return $new_arr;
    
}
}