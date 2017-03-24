<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getResultFormat($result){
         if($result != null){
                $data_array = array('clothing_type','brand','style_name','style_id_number'); 
                $size_array = array('garment_dimension','grade_rule');
                $result_data = json_decode($result[0]['specs_json']);                
                foreach ($result_data as $key => $value) {
                    if (in_array($key, $data_array)) {
                    $data[$key] = $value;  
                  }
                }
                foreach ($result_data->sizes as $label_key => $size_labels) {                   
                    foreach ($size_labels as $attr_key => $size_attr) {                        
                        foreach ($size_attr as $key => $value) {
                            if (in_array($key, $size_array)) {
                                $data[$label_key][$attr_key][$key] = $value;  
                            }
                        }
                    }
                }
            } else {
                $data = 'Record not found!';
            }
            return $data;        
    } 

}