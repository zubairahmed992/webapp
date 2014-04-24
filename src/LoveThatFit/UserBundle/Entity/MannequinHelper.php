<?php

namespace LoveThatFit\UserBundle\Entity;

class MannequinHelper {

    private $user;
    private $mannequin;

    public function __construct($user=null) {
        $this->user = $user;            
    }
    private function readConfiguration(){
        #read yaml assign to $mannequin
        
    }
  
}