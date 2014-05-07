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
  return array('women_letter_sizes'=>$this->getWomanLetterSizes(),'woman_number_sizes'=>$this->getWomanNumberSizes()
          ,'woman_waist_sizes'=>$this->getWomanWaistSizes(),'man_letter_sizes'=>$this->getManLetterSizes(),'man_chest_sizes'=>$this->getManChestSizes()
          ,'man_waist_sizes'=>$this->getManWaistSizes(),'man_shirt_sizes'=>$this->getManShirtSizes());
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

#---------------------------Sorting Of Array----------------------------------#
public function getArray($arr){
   
    $new_arr=array();
    foreach($arr as $key){
        $new_arr[$key['title']]=$key['title'];
    }
    return $new_arr;
    
}
}