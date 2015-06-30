<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class WebServiceHelper {

     private $container;

    public function __construct(Container $container) {
      $this->container = $container;
    }
    #------------------------ User -----------------------
    
    public function loginService($request_array) {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('webservice.helper.user')->matchPassword($user, $request_array['password'])) {
                $response_array=null;    
                if(array_key_exists('user_detail', $request_array) && $request_array['user_detail']=='true'){
                    $response_array['user']=$user->toDataArray(true, $request_array['deviceType']);
                }
                if(array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand']=='true'){
                    $response_array['retailer']=$this->container->get('admin.helper.brand')->getBrandRetailerList();
                }
                
                return $this->response_array(true, 'user found', true, $response_array);
            } else {
                return $this->response_array(false, 'Invalid Password');
            }
        } else {
            return $this->response_array(false, 'Invalid Email');            
        }
    }
    #--------------------------------User Detail Array -----------------------------#
 private function getBasePath($request) {
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
 }
    #----------------------------------------------------------------------------------------
    public function response_array($success, $message=null, $json=true, $data=null){
        $ar= array(
            'data'=>  $data,
            'message'=>$message,
            'success'=>$success,
        );
        return $json?json_encode($ar):$ar;
    }
    
}