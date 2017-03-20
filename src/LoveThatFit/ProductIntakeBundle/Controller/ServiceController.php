<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller {
    
     
#------------------------/product_intake/product_specification
      
    public function getProductSpecificationAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $result = $this->get('service.repo')->getProductSpecification($decoded);
        ($result == null ? $data = json_encode('Record not found!'):$data=$result[0]['specs_json'] );
    return new Response($data);
    }
   

}
