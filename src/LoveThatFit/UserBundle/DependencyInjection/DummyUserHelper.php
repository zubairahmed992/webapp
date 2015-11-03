<?php

namespace LoveThatFit\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;

class DummyUserHelper {

    public $conf;

    //--------------------------------------------------------------------
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/dummy_users.yml'));
    }
    
    public function copyUserData($user){
        $ar =  $this->conf[$user->getGender()]['image'];
        $user->setImageDeviceType($ar['device_type']);        
        $user->setImage($ar['device_type']);        
        $user->getMeasurement()->setByArray($this->conf[$user->getGender()]['measurements']);       
        
    }
      
        public function setDeviceInfo($device, $ar){
            if(array_key_exists('device_name', $ar) && $ar['device_name']){$device->setDeviceName($ar['device_name']);}
            if(array_key_exists('device_type', $ar) && $ar['device_type']){$device->setDeviceType($ar['device_type']);}
            if(array_key_exists('device_user_per_inch_pixel_height', $ar) && $ar['device_user_per_inch_pixel_height']){$device->setDeviceUserPerInchPixelHeight($ar['device_user_per_inch_pixel_height']);}
            return $device;
        } 
}