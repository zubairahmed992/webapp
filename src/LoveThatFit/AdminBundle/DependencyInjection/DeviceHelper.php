<?php

namespace LoveThatFit\AdminBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;

class DeviceHelper {

    protected $conf;
    protected $mask_specs;
    
    public function __construct() {
        $conf_yml = new Parser();
        $conf = $conf_yml->parse(file_get_contents('../app/config/config_device.yml'));
        $this->conf = $conf["device_config"];
    }

//--------------------------------------------------------------------------------
    public function getDeviceConfig($node=null) {
        if($node){
            if(array_key_exists($node, $this->conf)){
                return $this->conf[$node];
            }
        }else{
            return $this->conf;
        }    
        return;
    }


}