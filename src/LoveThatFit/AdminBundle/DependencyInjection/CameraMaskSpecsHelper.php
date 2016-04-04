<?php

namespace LoveThatFit\AdminBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;

class CameraMaskSpecsHelper {

    protected $conf;
    protected $mask_specs;
    
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/camera_mask_specs.yml'));
        $this->mask_specs = $this->conf["mask_specs"];
    }

//--------------------------------------------------------------------------------
    public function getMaskSpecs($node=null) {
        if($node){
            if(array_key_exists($node, $this->mask_specs)){
                return $this->mask_specs[$node];
            }
        }else{
            return $this->mask_specs;
        }    
        return;
    }


}