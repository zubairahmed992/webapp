<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSUserController extends Controller {

     
    
    private function process_request(){
        return $this->get('webservice.helper')->processRequest($this->getRequest());        
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
    
#~~~~~~~~~~~~~~~~~~~ ws_user_registeration   /ws/user_registeration

    public function registrationAction() {
        $decoded  = $this->process_request();
        $json_data = $this->get('webservice.helper')->registrationService($decoded);
        return new Response($json_data);      
    }    
#~~~~~~~~~~~~~~~~~~~ ws_size_charts   /ws/size_charts
    public function sizeChartsAction(){
       $decoded  = $this->process_request();
       $json_data=$this->get('webservice.helper')->sizeChartsService($decoded);
        return new response($json_data);
       
   } 
#~~~~~~~~~~~~~~~~~~~ ws_image_uploader   /ws/image_uploader
    public function imageUploaderAction() {
        $decoded = $this->process_request();
        #if email index exists check required....
        $user = $this->get('user.helper.user')->findByEmail($decoded['email']);
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/uploads/ltf/users/'. $user->getId() . "/";
        return new Response($this->get('webservice.helper')->uploadUserImage($user, $decoded, $_FILES));
    }

#---------------------------------------------
    public function fooAction() {
        $decoded = $this->process_request();
        #return new Response($decoded['param1']);
        #$user = $this->container->get('webservice.repo')->findUser($decoded['param1']);
        $user = $this->container->get('user.helper.user')->find($decoded['param1']);
        $res = $this->container->get('webservice.helper')->response_array(true,"user found",true,$user->toDataArray(true,$decoded['param2']));
        return new Response($res);
    }


}

