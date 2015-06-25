<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class WebServiceHelper {

     private $container;

    public function __construct(Container $container) {
      $this->container = $container;
    }
    #------------------------ User -----------------------
    
    public function matchEmailPassword($request_array) {
        $email = $request_array['email'];
        $password = $request_array['password'];
        $deviceType=$request_array['deviceType'];
       
        $user = $this->container->get('user.helper.user')->findByEmail($email);
        if (count($user) > 0) {
            if ($this->container->get('webservice.helper.user')->matchPassword($user, $password)) {
                    return $this->response_array(true, 'user found', true, $user->toDataArray());
            } else {
                return $this->response_array(false, 'Invalid Password');
            }
        } else {
            return $this->response_array(false, 'Invalid Email');            
        }
        
    }
    
    public function response_array($success, $message=null, $json=true, $data=null){
        $ar= array(
            'data'=>  $data,
            'message'=>$message,
            'success'=>$success,
        );
        return $json?json_encode($ar):$ar;
    }
    
}