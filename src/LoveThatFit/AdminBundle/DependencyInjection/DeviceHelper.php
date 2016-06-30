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
//--------------------------------------------------------------------------------
    public function getConversionRatio($original, $targeted) {
        if ($targeted && array_key_exists($targeted, $this->conf)) {
            if (array_key_exists($original, $this->conf) && $this->conf[$original]['pixel_per_inch'] > 0) {
                return $this->conf[$targeted]['pixel_per_inch'] / $this->conf[$original]['pixel_per_inch'];
            }
        }
        return;
    }
    //--------------------------------------------------------------------------------
    public function getScreenConversionRatio($original, $targeted) {
        if ($targeted && array_key_exists($targeted, $this->conf)) {
            if (array_key_exists($original, $this->conf) && $this->conf[$original]['total_screen_height_px'] > 0) {
                return $this->conf[$targeted]['total_screen_height_px'] / $this->conf[$original]['total_screen_height_px'];
            }
        }
        return;
    }

}