<?php

namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSProductController extends Controller {
    
    private function process_request(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        
        if($decoded==null) #if null (to be used for web service testing))
            $decoded  = $request->request->all();
        
        return $decoded;
    }
    #---------------------------  
    private function authenticateUser($token) {        
        return $this->container->get('user.helper.user')->findByAuthToken($token);               
    }
#---------------------------  
    public function productsAction() {
        $decoded = $this->process_request();
        $user = array_key_exists('auth_token', $decoded)?$this->authenticateUser($decoded['auth_token']):null;        
        if ($user) {
            $this->container->get('admin.helper.product')->findByAuthToken($token);               
            
            $res = $this->response_array(true, 'User Authenticated.', true, $user->toArray());            
        } else {
            $res = $this->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    #----------------------------------------------------------------------------------------

    public function response_array($success, $message = null, $json = true, $data = null) {
        $ar = array(
            'data' => $data,
            'message' => $message,
            'success' => $success,
        );
        return $json ? json_encode($ar) : $ar;
    }
}

