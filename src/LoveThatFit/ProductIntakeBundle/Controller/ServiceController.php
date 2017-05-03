<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


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

#------------> /pi/ws/product_detail
    public function productDetailAction(Request $request) {
         $product_id =  $request->get('product_id');
         $server_url =  $request->get('server_url');    
            $data = $this->get('service.helper')->getProductDetails($product_id);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            //$imagepath = $protocol.$_SERVER["HTTP_HOST"]. '/webapp/web/uploads/ltf/products/fitting_room/web/'; 
            $imagepath =  str_replace('\\', '/', getcwd()). '/uploads/ltf/products/fitting_room/web/'; 
            //$imagepath = 'http://192.168.0.113/webapp/web/uploads/ltf/products/fitting_room/web/'; 
            // $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            // $prodcut_Color_pattren = $protocol.$_SERVER["HTTP_HOST"]. '/webapp/web/uploads/ltf/products/pattern/web/'; 
            $prodcut_Color_pattren = str_replace('\\', '/', getcwd()). '/uploads/ltf/products/pattern/web/';

            $postdata['imagepath'] = $imagepath;
            $postdata['prodcut_Color_pattren'] = $prodcut_Color_pattren;     

//  echo $protocol;
          //  echo $imagepath;
           // die;
            $url = $server_url.'/webapp/web/app_dev.php/pi/ws/save_product';
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
        $message =  $this->get('service.helper')->createProduct($_POST['data'],$_POST['imagepath'], $_POST['prodcut_Color_pattren']); 
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
