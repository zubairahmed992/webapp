<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use Symfony\Component\Yaml\Parser;


class WebServiceHelper {

    public function getServiceNames(){
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/routing.yml'));
        $ar=array();
        foreach($conf as $k=>$v){            
            if (preg_match("!ws_(.*)!", $k)) {                
                array_push($ar, str_replace("ws_","",$k));    
            }            
        }
        return $ar;
    }

    public function stripToNameArray($conf){
        $ar=array();
        foreach($conf as $k=>$v){            
                array_push($ar, $k);                
        }
        return $ar;
    }
    public function getServiceDetails(){
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/services_list.yml'));        
        return $conf;
    }
    
}