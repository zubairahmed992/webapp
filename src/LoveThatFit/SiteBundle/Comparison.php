<?php

namespace LoveThatFit\SiteBundle;
use Symfony\Component\Yaml\Parser;

class Comparison {

    private $user;
    private $product;
    
    function __construct($user = null, $product = null) {
        $this->user=$user;
        $this->product=$product;
    }

        function getFeedBackJSON() {
        return json_encode($this->array_mix());
    }

    private function array_mix(){
        $sizes = $this->product->getProductSizes();
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb=array();
        foreach ($sizes as $size) {
              $fb[$size->getTitle()] = $size->getPriorityMeasurementArray();
        }
        return $fb;
        
        foreach ($sizes as $size) {
           $size_specs = $size->getPriorityMeasurementArray();#~~~~~~~~>
           foreach ($size_specs as $fp_specs) {
               $fb[$size->getTitle()][$fp_specs['fit_point']] = 
                       $this->get_fit_point_array($fp_specs, $body_specs);
           
               
           }
           
       }
    }
    private function get_fit_point_array($fp_specs, $body_specs){
        
        $body_measurement=array_key_exists($fp_specs['fit_point'], $body_specs)?$body_specs[$fp_specs['fit_point']]:0;
        
        return array(  'fit_point' => $fp_specs['fit_point'],  
                'ideal_low' => $fp_specs['ideal_body_size_low'], 
                'ideal_high' => $fp_specs['ideal_body_size_high'], 
                'max' => $fp_specs['max_body_measurement'], 
                'priority' => $fp_specs['fit_priority'],
                'body' => $body_measurement,
            );
    }
    
}