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
    public function authenticateUser($token) {        
        return $this->container->get('')->findByAuthToken($token);               
    }
#---------------------------  
    public function productsAction() {
        $decoded = $this->process_request();
        $user = $this->authenticateUser($decoded['auth_token']);
        if ($user) {
            $res = $this->response_array(false, 'User not authenticated.');
        } else {
            $res = $this->response_array(true, 'User Authenticated.');
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

