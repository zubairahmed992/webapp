<?php

namespace LoveThatFit\SiteBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;

class SocialMediaHelper {

    protected $conf;
    
    public function __construct($container) {
        $conf_yml = new Parser();
        $this->container = $container;
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_social_media.yml'));
    }

//--------------------------------------------------------------------------------
    public function getConfiguration($site=null) {
        if($site){
            return $this->conf['social_media'][$site];
        }else{
            return $this->conf['social_media'];
        }
    }



}