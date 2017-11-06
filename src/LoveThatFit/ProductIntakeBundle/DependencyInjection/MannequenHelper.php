<?php

namespace LoveThatFit\ProductIntakeBundle\DependencyInjection;
use Symfony\Component\Yaml\Parser;

class MannequenHelper {
    
    protected $conf;
    #------------------------------------------------
    public function __construct() {        
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../src/LoveThatFit/ProductIntakeBundle/Resources/config/mannequin.yml'));
    }
    #------------------------------------------------
    public function getCoordinates($mann='laurie'){        
        $yco=array();
        $md = $this->conf['data'][$mann];
        foreach ($this->conf['map'] as $fp => $attr) {
            $yco[$fp] = $md[$attr['segments']['s1']['a']];
        }
        return $yco;
    }
    #---------------------------------------------
    public function getRawCoordinates($mann='laurie'){                
        return $this->conf['data'][$mann];
    }
    
}