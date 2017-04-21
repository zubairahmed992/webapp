<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class ServiceController extends Controller {    
     
#------------------------/product_intake/product_specification
      
    public function getProductSpecificationAction() {
        
         try {
            $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
            $result = $this->get('service.repo')->getProductSpecification($decoded);   
            $data   = $this->get('service.helper')->getResultFormat($result);
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

#------------> /pi/ws/product_detail/{id}
    public function productDetailAction($id) {
            $data = $this->get('service.helper')->getProductDetails($id);  
            $url = 'http://localhost/webapp/web/app_dev.php/pi/ws/save_product';
            $postdata['data'] =  json_encode($data);            
            //open connection
            $ch = curl_init();
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
           // curl_setopt($ch,CURLOPT_POST, count($dat));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata); 
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);
           return new JsonResponse();
    }
    
    public function saveProductAction() {        
         try {              
        $message =  $this->get('service.helper')->createProduct($_POST['data']); 
        return new JsonResponse([
            'success' => true,
            'data'    =>  $message
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
