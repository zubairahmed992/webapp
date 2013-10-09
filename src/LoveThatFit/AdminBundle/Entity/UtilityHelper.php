<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class UtilityHelper {

    protected $conf;
    protected $pagination_limit;
    protected $pagination_default_page;
    protected $device_boot_strap_config;

    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $this->pagination_limit = $this->conf["constants"]["pagination"]["limit"];
        $this->pagination_default_page=$this->conf["constants"]["pagination"]["default_page"];
        $this->device_bootstrap = $this->conf["constants"]["device_bootstrap"];
        
    }

//--------------------------------------------------------------------------------
    public function getPaginationLimit() {
        return $this->pagination_limit;
    }
//--------------------------------------------------------------------------------
    public function getPaginationdefaultPage() {
        return $this->pagination_default_page;
    }
//--------------------------------------------------------------------------------
    public function getDeviceBootstrap() {
        return $this->device_bootstrap;
    }
#-------------------------------------------------------------------------------
    public function getPageNumber($count){
       return $count/$this->getPaginationLimit();
        
    }
    
//--------------------------------------------------------------------------------

    public function getPaginationCount($page_number, $rec_count) {
        
        $pagination_count = 0;
        
        if ($page_number == 0 || $this->pagination_limit == 0) {
            $pagination_count = 0;
        } else {
            $pagination_count = ceil($rec_count / $this->pagination_limit);
        }
 
        return $pagination_count;
    }
 #---------------------------------Returning The Gender--------------------------------#   
 public function getGenders() {
        return $this->conf["constants"]["gender"];
 }
 #---------------------------------Returning The Sizes--------------------------------#   
 public function getSizeCharts() {
        return $this->conf["constants"]["size_charts"];
 }
 #------------Target------------------------------------#
 public function getTargets()
 {
       return $this->conf["constants"]["target"];
 }
 #---------------Body Type----------------------------#
 public function getBodyTypes()
 {
    return $this->conf["constants"]["body_types"];
 }
 #----------------Body Type For Searching---------#
 public function getBodyTypesSearching()
 {
    return $this->conf["constants"]["body_types_search"];
 }
#------------Size Titles--------------------------------#
public function getSizeTitle(){
   return $this->conf["constants"]["size_titles"]; 
}
#------------Size Numbers-------------------------------#
public function getSizeNumbers()
{
    return $this->conf["constants"]["size_numbers"]; 
    
}
//--------------------------------------------------------------------------------
public function setSizeTitles($sizes)
{ $new_sizes=array();
    $new_key='';
    foreach ($sizes as $key => $value) {
        switch ($value){
            case "0":
                $new_key="XXS : ".$value;
                break;
            case "1":
                $new_key="XS : ".$value;
                break;
            case "2":
                $new_key="XS : ".$value;
                break;
            case "4":
                $new_key="S : ".$value;
                break;
            case "6":
                $new_key="S : ".$value;
                break;
            case "8":
                $new_key="M : ".$value;
                break;
            case "10":
                $new_key="M : ".$value;
                break;
            case "12":
                $new_key="L : ".$value;
                break;
            case "14":
                $new_key="L : ".$value;
                break;
            case "16":
                $new_key="XL : ".$value;
                break;
            case "18":
                $new_key="XL : ".$value;
                break;
            case "20":
                $new_key="XXL : ".$value;
                break;
            case "22":
                $new_key="XXL : ".$value;
                break;
            case "24":
                $new_key="XXL : ".$value;
                break;
            case "26":
                $new_key="XXL : ".$value;
                break;
            case "28":
                $new_key="XXL : ".$value;
                break;
        }
    
        $new_sizes[$key]=$new_key;
    }
    return $new_sizes;
}


}