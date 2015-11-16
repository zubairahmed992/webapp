<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSUserController extends Controller {

     
    
    private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
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
#~~~~~~~~~~~~~~~~~~~ ws_user_detail   /ws/user_detail

    public function detailAction() {
        $decoded  = $this->process_request();                                 
        $json_data = $this->get('webservice.helper')->userDetail($decoded);
        return new Response($json_data);
    }
    
#~~~~~~~~~~~~~~~~~~~ ws_user_registeration   /ws/user_registeration

    public function registrationAction() {
        $decoded  = $this->process_request();
        $json_data = $this->get('webservice.helper')->registrationService($decoded);
        return new Response($json_data);      
    }  
#~~~~~~~~~~~~~~~~~~~ ws_user_measurement_update   /ws/user_measurement_update
    public function measurementUpdateAction() {
        $decoded  = $this->process_request();
        $json_data = $this->get('webservice.helper')->measurementUpdate($decoded);
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
        $res = $this->container->get('webservice.helper')->response_array(true,"member found",true,$user->toDataArray(true,$decoded['param2']));
        return new Response($res);
    }

#------------------------- ws_image_uploader   /ws/image_uploader
    
    public function passwordChangeAction() {
       return new response($this->get('webservice.helper')->changePassword($this->process_request()));
    }
    
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
#    FORGET_PASSWORD_SENDEMAIL	/ws/forget_password
 public function forgotPasswordAction(){
     $decoded=$this->process_request();
     $res='';
     $user = $this->get('user.helper.user')->findByEmail($decoded['email']);
     
     if($user){
      $user->setAuthToken(uniqid());
      $this->get('user.helper.user')->saveUser($user) ;
      $baseurl = $this->getRequest()->getHost();
      $link = $baseurl . "/" . $this->generateUrl('forgot_password_reset_form', array('email_auth_token' => $user->getAuthToken()));
      #return new Response($this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link (".$link .") to " . $user->getEmail()));    

      $defaultData = $this->get('mail_helper')->sendPasswordResetLinkEmail($user, $link);
      
      if ($defaultData[0]) {
          #$res= $this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link to " . $user->getEmail());
          $res= $this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link (".$link .") to " . $user->getEmail());
          
      }else { 
          
        $res= $this->get('webservice.helper')->response_array(false, " Email not sent due to some problem, please try again later.");  
          }
        
    }else{
        $res= $this->get('webservice.helper')->response_array(false, " User not found.");
        
        }
        return new Response($res);
}
#----------------------------------------------------------
 public function  forgotPasswordTokenAuthAction(){     
     return new response($this->get('webservice.helper')->matchAlternateToken($this->process_request()));
 }
#----------------------------------------------------------
 public function  forgotPasswordUpdateAction(){
     return new response($this->get('webservice.helper')->forgotPasswordUpdate($this->process_request()));
 }
#------------------------- ws_user_profile_update:  /ws/user_profile_update
        
    public function profileUpdateAction() {       
       return new response($this->container->get('webservice.helper')->updateProfile($this->process_request()));
    }    
    
   
}

