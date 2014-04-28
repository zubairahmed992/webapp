<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\Yaml\Parser;

class MannequinHelper {

    private $user;
    private $mannequin;

    public function __construct($user=null) {
        $this->user = $user;            
    }
    private function readConfiguration(){
        #read yaml assign to mannequin
        $conf_yml = new Parser();
        $this->mannequin = $conf_yml->parse(file_get_contents('../app/config/config_mannequin.yml'));
        return $this->mannequin;
    }
        
    public function userMannequin($user)
    {
        $usermeasurements=$user->getMeasurementArray();
        $mannequin=  $this->readConfiguration();
        $mannequinTop=$this->_compare($mannequin['sizes'], 'waist', $usermeasurements);
        $mannequinBottom=$this->_compare($mannequin['sizes'], 'shoulder_across_back', $usermeasurements);
        return array(
            'name'=>$user->getFirstName()." ".$user->getLastName(),         
            'user measurement'=>$usermeasurements,
            'mannequin top size'=>$mannequinTop,
            'mannequin top measurement'=>$mannequin['sizes'][$mannequinTop],
            'mannequin bottom size'=>$mannequinBottom,
            'mannequinbottom measurement'=>$mannequin['sizes'][$mannequinBottom]
           );
    }
      
    
    private function compare($sizes, $fit_point, $user){
        $prev_size=null;
        $prev_key=null;
        foreach ($sizes as $key=>$value) {
            if($value[$fit_point]==$user[$fit_point]){
             return $key;    
            }            
        }
        return null;
    }
    
    
    
     private function _compare($sizes, $fit_point, $user){
        $prev_size=null;
        $prev_key=null;
        foreach ($sizes as $key=>$value) {
            if($value[$fit_point]==$user[$fit_point]){
             return $key;    
            }elseif($value[$fit_point]>$user[$fit_point]){
                if ($prev_size==null) return $key;                    
                else{
                    $avg=($prev_size[$fit_point] + $value[$fit_point])/2;
                    if ($avg>$user[$fit_point]) return $prev_key;
                    else return $key;
                }
            }
            $prev_size=$value;
            $prev_key=$key;            
        }
        return $prev_key;
    }
    
  
  
    
}