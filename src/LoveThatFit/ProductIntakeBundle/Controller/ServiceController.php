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
        $resp=$this->get('service.helper')->getProductDetails($id);;        
        return new Response (json_encode($resp));
    }
   

}
