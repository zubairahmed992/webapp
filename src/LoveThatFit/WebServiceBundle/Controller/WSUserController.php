<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSUserController extends Controller {

    
    
    private function process_request(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        
        if($decoded==null) #if null (to be used for web service testing))
            $decoded  = $request->request->all();
        
        return $decoded;
    }
#~~~~~~~~~~~~~~~~~~~ ws_user_Login   /ws/login

    public function loginAction() {
        $decoded  = $this->process_request();                         
        $user_info = $this->get('webservice.helper')->loginService($decoded);
        
        return new Response($user_info);
      
    }

#~~~~~~~~~~~~~~~~~~~ ws_email_exists   /ws/email_exists

    public function emailExistsAction() {
        $decoded  = $this->process_request();                         
        $exists = $this->get('webservice.helper')->emailExists($decoded['email']);
        return new Response($exists?'true':'false');
    }
    
    
    
}

