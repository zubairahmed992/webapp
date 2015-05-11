<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class WSTestController extends Controller {
    
    private function process_request(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        
        if($decoded==null) #if null (to be used for web service testing))
            $decoded  = $request->request->all();
        
        return $decoded;
    }
#-----------------------------------------------------------------------------------    
    private function authenticate_token($token){
    if ($token) {
            $tokenResponse = $this->get('webservice.helper.user')->authenticateToken($token);
            return $tokenResponse;
        } else {
            return array('status'=>False, 'Message' => 'Please Enter the Authenticate Token');
        }
}

/*  
 * Services being called from original controller  
1.	Brands
2.	Brand list for size charts
3.	Clothing type brands
4.	Constant values
5.	Registration size charts
6.	Images URL
*/
    
    
#####################################################################################    
#####################################################################################
#####################################################################################


    #------------------------------------------------
   public function sizeChartForRegAction(){
       $data['sizeChart']=$this->get('admin.helper.sizechart')->getBrandSizeTitleArray();
        return new response(json_encode(($data)));
       
   } 

    
    
#####################################################################################    
#####################################################################################
#####################################################################################


#--------------------Login User -----------------------------------------------#     
    public function loginAction() {
        $decoded  = $this->process_request();                         
        $user_info = $this->get('webservice.helper.user')->loginWebService($decoded, $this->getRequest());
        return new response(json_encode($user_info));      
    }

#~~~~~~~~~~~~~~> USER DETAIL SERVICE being called from original controller.
    
#------------------------------User Profile-------------------------------------#
    public function userProfileAction() {
        $request = $this->getRequest();
        $decoded = $this->process_request();
        $chk_email = $this->get('webservice.helper.user')->findOneBy($decoded['email']);
        if (count($chk_email) > 0) {
            $entity = $this->get('webservice.helper.user')->getArrayByEmail($decoded['email']);
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $entity['userId'] . "/";
            $entity['path'] = $baseurl;
            return new Response(json_encode($entity));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
    }
    #----------------------------------------------------
      public function userDetailAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
    
        $user = $this->get('webservice.helper.user')->findByAuthToken($decoded['authTokenWebService']);      
        $user_obj = $this->get('webservice.helper.user')->userDetailObject($user, $decoded['deviceType']);
        $user_obj['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/' . $user_obj['path'].'/';
        return new response(json_encode($user_obj));      
    }    

    #--------------------------End Of Registration --------------------------------#    


    public function emailCheckAction(){        
        $decoded=$this->process_request();
        if ($decoded['email']) {            
            $checkEmail = $this->get('webservice.helper.user')->emailCheck($decoded['email']);
            return new response(json_encode($checkEmail));
        } else {
            return new Response(json_encode(array('Message' => 'Email missing')));
        }
    }
    #-------------------------------------------------------------------------
    
      
 #-------------------Get Brand Sync-------------------------------------------#
# BRANDS_URL	/web_service/brand_sync
    
    
 public function getBrandSyncAction() {
        $request_array = $this->process_request();      
        if($request_array){
             $date_fromat=$this->get('webservice.helper.product')->returnFormattedTime($request_array);
      
       if($date_fromat){
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService($date_fromat);
        }
        $total_record = count($brand);
         $data = array();
        $data['data'] = $brand;
        
        return new Response($this->json_view($total_record, $data));
        
        }else{
            return new response(json_encode(array("Message"=>"No Data Found")));
        }
    }
    
    
     #--------------------Get product Detail Sunc-----------------------------------#
    # PRODUCT_DETAIL_URL	/web_service/product_detail_sync
    public function getProductDetailSynAction() {
        $request_array = $this->process_request();
        $check =  $this->authenticate_token($request_array['authTokenWebService']);
        if ($check['status']==False) {
                  return new Response(json_encode($check['status']));
        }
       $product_response=$this->get('webservice.helper.product')->newproductDetailWebService($this->getRequest(),$request_array);        
       return new response(json_encode($product_response));
    }

    
    #--------------------------------------Try On History Service----------------------------------------------#
#RECENTLY_TRIED_URL	
    public function userTryHistoryAction()
    {        
      $request_array = $this->process_request();
      $check =  $this->authenticate_token($request_array['authTokenWebService']);
      if ($check['status']==False) {
                return new Response(json_encode($check['status']));
      }
     
        $msg=$this->get('webservice.helper.product')->getUserTryHistoryWebService($this->getRequest(),$request_array['userId']);
        return new Response(json_encode($msg));
     }
    
#####################################################################################    
#####################################################################################
#####################################################################################
    
    
#----------------------Registration--------------------------------------------#
 public function registrationCreateAction() {
        $request_array = $this->process_request();
        $user_info = $this->get('webservice.helper.user')->registerWithReqestArray($request,$request_array);
        return new response(json_encode($user_info));
    }    
    
    
#-------------------Change Password--------------------------------------------#

    public function changePasswordAction() {
      $request_array = $this->process_request();
      $check =  $this->authenticate_token($request_array['authTokenWebService']);
      if ($check['status']==False) {
                return new Response(json_encode($check));
      }
      $msg=$this->get('webservice.helper.user')->changePasswordWithReqestArray($request_array);
      return new response(json_encode($msg));
    }
#-------------------------------------------------------------------------------

    public function editProfileAction() {
        $decoded = $this->process_request();
        $check = $this->authenticate_token($decoded['authTokenWebService']);
        if ($check['status'] == False) {
            return new Response(json_encode($check));
        }

        $entity = $this->get('webservice.helper.user')->updateWithUserArray($decoded);
        $msg=$entity?'Update Sucessfully':'We could not find the user';
        return new Response(json_encode(array('Message' => $msg)));
    }

#--------------------------Shoulder Height and Outseam Ration Edit/Update------#
 public function shoulderOutseamEditAction() {
        $request_array = $this->process_request();
        $check = $this->authenticate_token($request_array['authTokenWebService']);
        if ($check['status'] == False) {
            return new Response(json_encode($check));
        }
        $msg=$this->get('webservice.helper.user')->updateMarkingParamWithReqestArray($this->getRequest(),$request_array);
        return new response(json_encode($msg));
    }
#------------------------Password Forget Services------------------------------#
 public function forgetPasswordAction(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $email = $decoded['email'];
     if($this->get('webservice.helper.user')->emailCheckForgetPassowrd($email)){
      $userData=$this->get('webservice.helper.user')->updateTokenSendEmail($request,$email);
      $baseurl = $this->getRequest()->getHost();
      $link = $baseurl . "/" . $this->generateUrl('forgot_password_reset_form', array('email_auth_token' => $userData->getAuthToken()));
      $defaultData = $this->get('mail_helper')->sendPasswordResetLinkEmail($userData, $link);
      $msg = "";
      if ($defaultData[0]) {$msg = " Email has been sent with reset password link to " . $userData->getEmail();}
      else { $msg = " Email not sent due to some problem, please try again later.";}
       return new response(json_encode(array("Message"=>$msg)));
      }else{return new response(json_encode(array("Message"=>"This Email doesn't exist")));}
}
#---------------------------------End of ofrgot password-----------------------#


  
 #-------------------------Measurement Edit Web Service-----------------------------------------------------------------------------#       
 public function measurementEditAction() {
       $user = $this->get('webservice.helper.user');
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        
        
        $request_array = json_decode($jsonInput, true);
       // $request_array= array();
     //  $request_array=array('email'=>'kamran@hotmail.com','authTokenWebService'=>'97fc8d115394f3f1947f315783e29e0e','deviceType'=>'4s','heightPerInch'=>'6','weight'=>'100','thigh'=>10);
        $email = $request_array['email'];
       
#------------------------------Authentication of Token--------------------------------------------#
        $user = $this->get('webservice.helper.user');
        $authTokenWebService = $request_array['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
#-------------------------------End Of Authentication Token--------------------------------------#

        if ($user->isDuplicateEmail(Null,$email)) {

            $user = $this->get('webservice.helper.user');
            $userinfo = $user->getArrayByEmail($email);
           
            $msg=array();
            $msg=$user->updateMeasurementWithReqestArray($userinfo['userId'],$request_array,$request);
            
           
             return new Response(json_encode($msg));
        } else {
            return new Response(json_encode(array('Message' => 'We can not find user')));
        }
    }

  
    #-----------------------------Check Token -------------------------------------#
 public function  checkTokenforgetPasswordAction(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $authToken = $decoded['auth_token'];
        
        if($authToken){
        $updatePassword=$this->get('webservice.helper.user')->checkTokenforgetPassword($authToken);
        return new response(json_encode($updatePassword));
 }else{
 return new response(json_encode(array("Message"=>"Authenticated Token Missing")));
 }
 }
#---------------------End of Token forgot password-----------------------------# 
#----------------------------Update Forget Password----------------------------# 
 public function updateForgetPasswordAction(){
      $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $password = $decoded['password'];
        $email = $decoded['email'];
        $updatePassword=$this->get('webservice.helper.user')->updateForgetPassword($email,$password);
        return new response(json_encode($updatePassword));
     
 }
 
 
#####################################################################################    
#####################################################################################
#####################################################################################

    



#-------------------------------Image Upload-----------------------------------#   
 public function imageUploadAction() {
         $request = $this->getRequest();
        $email = $_POST['email'];
        $deviceType=$_POST['deviceType'];
        $heightPerInch=$_POST['heightPerInch'];

        if ($email) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));
        } else {
            return new response(json_encode(array('Message' => 'Email Not Found')));
        }
        if (count($entity) > 0) {
            $user_id = $entity->getId();
            $file_name = $_FILES["file"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
           $newFilename = 'iphone' . "." . $ext;
            $entity->setIphoneImage($newFilename);
            if (!is_dir($entity->getUploadRootDir())) {
                @mkdir($entity->getUploadRootDir(), 0700);
            }
   if (move_uploaded_file($_FILES["file"]["tmp_name"], $entity->getAbsoluteIphonePath())) {
       $this->get('webservice.helper.user')->setMarkingDeviceType($entity, $deviceType,$heightPerInch);
             //   $entity->setDeviceType($deviceType);
             //   $entity->setDeviceUserPerInchPixelHeight($heightPerInch);

                 $em->persist($entity);
                 $em->flush();
                //  $image_path = $entity->getWebPath(); 
                 $userinfo = array();
                 $userimage = $entity->getIphoneImage();
                 $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
                $userinfo['heightPerInch']= $this->get('webservice.helper.user')->getUserDeviceTypeAndMarking($entity,$deviceType);//$entity->getDeviceUserPerInchPixelHeight();
                 $userinfo['iphoneImage'] = $userimage;
                 $userinfo['path'] = $baseurl;
                return new Response(json_encode($userinfo));
          } else {
              return new response(json_encode(array('Message' => 'Image not uploaded')));
            }
        } else {
            return new response(json_encode(array('Message' => 'We can not find user')));
        }
    }   
#----------------------Avatar Image Uploading ----------------------------------#
public function avatarUploadAction() {
    $request = $this->getRequest();

        $email = $_POST['email'];
        $user_helper = $this->get('webservice.helper.user');
        $email_chk = $user_helper ->findOneBy(array('email' => $email));
        
        
        if ($email) {
           $em = $this->getDoctrine()->getManager();
           // $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));
            $entity = $user_helper ->findOneBy(array('email' => $email));
        } else {
            return new response(json_encode(array('Message' => 'Email Not Found')));
        }
      //  return new response(count($entity));
        
        if (count($entity) > 0) {
            $user_id = $entity->getId();
            $file_name = $_FILES["file"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $newFilename = 'avatar' . "." . $ext;
            $entity->setAvatar($newFilename);
            if (!is_dir($entity->getUploadRootDir())) {
                @mkdir($entity->getUploadRootDir(), 0700);
            }
     if (move_uploaded_file($_FILES["file"]["tmp_name"], $entity->getAbsoluteAvatarPath())) {

                $em->persist($entity);
                $em->flush();
                //  $image_path = $entity->getWebPath(); 
                $userinfo = array();
                $userimage = $entity->getAvatar();
                 $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
                $userinfo['avatarImage'] = $userimage;
                $userinfo['path'] = $baseurl;
                return new Response(json_encode($userinfo));
            } else {
                return new Response(json_encode(array('Message' => 'Image not uploaded')));
            }
        } else {
            return new Response(json_encode(array('Message' => 'We can not find user')));
        }
    }   
#--------------------------------------------End Of Image Uploading----------------------------------------#  

    



#---------------------------Render Json--------------------------------------------------------------------#

    private function json_view($rec_count, $entity) {
        if ($rec_count > 0) {
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
                        JsonEncoder()));
            return $serializer->serialize($entity, 'json');
        } else {
            return json_encode(array('Message' => 'Record Not Found'));
        }
    }

    #----------------------------------------------------------------------------------------------#

    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    }
   
}

// End of Class