<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
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
        if(array_key_exists('create_default_user', $decoded)){
            $json_data = $this->get('webservice.helper')->registrationWithDefaultValues($decoded);
        }else{
            $json_data = $this->get('webservice.helper')->registrationService($decoded);
        }
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
#~~~~~~~~~~~~~~~~~~~ ws_file_uploader   /ws/file_uploader
  public function fileUploaderAction() {
	$decoded = $this->process_request();
	//$decoded = array("auth_token" =>'76aff354be53cc674748e0601b81f113',"device_type" => "iphone5");
	#if email index exists check required....
	$user = $this->get('user.helper.user')->findByAuthToken($decoded['auth_token']);
	return new Response($this->get('webservice.helper')->uploadUserFile($user, $decoded, $_FILES));
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
       return new response($this->get('webservice.helper')->updateProfile($this->process_request()));
    }    
    
#------------------------- ws_user_list:  /ws/user_list
   public function userAdminListAction() {       
       $users = $this->get('webservice.helper')->userAdminList();
       return new response($users);       
    }    

#~~~~~~~~~~~~~~~~~~~ ws_user_feedback_add   /ws/user_feedback_add

    public function feedbackAddAction() {
        $ra=$this->process_request();
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ufb = new \LoveThatFit\UserBundle\Entity\UserFeedback();
            $ufb->setUser($user);
            $ufb->setMessage($ra['message']);
            $ufb->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($ufb);
            $em->flush();

		  	#feedback email
		  	$this->get('webservice.helper')->feedbackService($user,$ra['message']);
		  	#feedback email end
            
            return  new Response($this->get('webservice.helper')->response_array(true, 'Feedback added'));
        } else {
            return  new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }
        
    }  
    #~~~~~~~~~~~~~~~~~~~ ws_user_default_values   /ws/user_default_values

    public function defaultValuesAction() {        
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/dummy_users.yml'));
        return new Response(json_encode($conf));
        
    }
 #~~~~~~~~~~~~~~~~~~~~~~ ws_selfieshare_create	/ws/selfieshare/create
 
    
     public function selfieshareCreateAction(){
      $ra=$this->process_request();
      #return new Response(json_encode($ra));
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ss=$this->get('user.selfieshare.helper')->createWithParam($ra, $user);      
            $ss_ar['to_email']=$ss->getFriendEmail();
            $ss_ar['template']='LoveThatFitAdminBundle::email/selfieshare.html.twig';
            $ss_ar['template_array']=array('user'=>$user, 'selfieshare'=>$ss);
            $ss_ar['subject']='SelfieStyler friend share';
            $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
            return  new Response($this->get('webservice.helper')->response_array(true, 'selfieshare created'));
        } else {
            return  new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }
 }
}

