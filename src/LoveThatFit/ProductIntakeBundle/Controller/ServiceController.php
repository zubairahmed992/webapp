<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServiceController extends Controller {
    
     
#------------------------/product_intake/product_specification
      
    public function getProductSpecificationAction() {
        
         try {
            $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
            $result = $this->get('service.repo')->getProductSpecification($decoded);           
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
        return new JsonResponse([
            'success' => true,
            'data'    => $data 
             ]);
         } catch (\Exception $exception) {
        return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
         }
    }
   

}
