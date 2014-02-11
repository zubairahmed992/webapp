<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;

class UserController extends Controller {

#--------------------Login User -----------------------------------------------#     
    public function loginAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $target_array = $request->request->all();
        $user_helper = $this->get('webservice.helper.user');
        if(isset($target_array['type'])=='type'){
           
            $user_info = $user_helper->loginWebService($target_array,$request);
        }else{
           
         $user_info = $user_helper->loginWebService($decoded,$request);
        }
        
       // $user_info = $user_helper->loginWebService($decoded,$request);
        return new response(json_encode($user_info));
      
    }
#----------------------End of Login User---------------------------------------# 
#----------------------Registration--------------------------------------------#
 public function registrationCreateAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
      //$request_array=array();
      //  $request_array=array('email'=>'test_service26@gmail.com','password'=>'123456','gender'=>'f','zipcode'=>'123','sc_top_id'=>'2','sc_bottom_id'=>'2','sc_dress_id'=>'2',
      //      'weight'=>4,'neck'=>4,'bust'=>5,'body_type'=>'Petite','bodyShape'=>'apple','braSize'=>'888');
        
        $user_info = $user->registerWithReqestArray($request,$request_array);
        return new response(json_encode($user_info));
    }
#--------------------------End Of Registration --------------------------------#    
#-------------------Change Password--------------------------------------------#
 public function changePasswordAction() {
        $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $request_array  = json_decode($jsonInput,true);
#---------------------------Authentication of Token-----------------------------#
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
#---------------------------End Of Authentication Token------------------------#
          $msg=$user->changePasswordWithReqestArray($request_array);
         return new response(json_encode($msg));
    }
#------------------------------End Of Change Password--------------------------#
    #------------------------------Edit Profile----------------------------------------------------------#    
public function editProfileAction()
{        $request = $this->getRequest();
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $decoded = json_decode($jsonInput,true);
      /*   $decoded=array();
        $decoded=array('email'=>'oldnavywomen0@ltf.com','firstName'=>'test','password'=>'123456','gender'=>'f','zipcode'=>'123','sc_top_id'=>'2','sc_bottom_id'=>'2','sc_dress_id'=>'2',
            'weight'=>4,'neck'=>4,'bust'=>5);*/
#------------------------------Authentication of Token--------------------------#
        $user = $this->get('webservice.helper.user');
       $authTokenWebService = $decoded['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
#------------------------------End Of Authentication Token----------------------#
    $entity=$user->updateWithUserArray($decoded);   
    if ($entity) {return new Response(json_encode(array('Message' => 'Update Sucessfully')));}
    else {return new Response(json_encode(array('Message' => 'We can not find user')));}
}        
#------------------------------End of Edit Profile------------------------------#
#------------------------------User Profile-------------------------------------#
public function userProfileAction()
{       $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $email = $decoded['email'];
      /*  $email='oldnavywomen0@ltf.com';*/
        $user = $this->get('webservice.helper.user');
        $entity = $user->getArrayByEmail($email);
        if (count($entity) > 0) {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $entity['id'] . "/";
            $entity['path'] = $baseurl;
            return new Response(json_encode($entity));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
}
#------------------------------End Of User Profile-----------------------------#
#--------------------------Shoulder Height and Outseam Ration Edit/Update------#
 public function shoulderOutseamEditAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
#---------------------------Authentication of Token----------------------------#
       $authTokenWebService = $request_array['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {return new Response(json_encode($tokenResponse));}
        } else {return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));}
#------------------------End Of Authentication Token---------------------------#
        $msg=$user->updateMarkingParamWithReqestArray($request,$request_array);
        return new response(json_encode($msg));
    }
#-------------End Of Outseam Shoulder -----------------------------------------#
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

    public function emailCheckAction(){
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $email = $decoded['email'];
      //  $email='skamrani2002@gmail.com';
        if ($email) {
            $user_helper = $this->get('webservice.helper.user');
            $checkEmail = $user_helper->emailCheck($email);
            return new response(json_encode($checkEmail));
        } else {
            return new Response(json_encode(array('Message' => 'Email missing')));
        }
    }
  
 #-------------------------Measurement Edit Web Service-----------------------------------------------------------------------------#       
 public function measurementEditAction() {
       $user = $this->get('webservice.helper.user');
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
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
            $msg=$user->updateMeasurementWithReqestArray($userinfo['id'],$request_array);
            
           
             return new Response(json_encode($msg));
        } else {
            return new Response(json_encode(array('Message' => 'We can not find user')));
        }
    }

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
                $entity->setDeviceType($deviceType);
                $entity->setDeviceUserPerInchPixelHeight($heightPerInch);

                 $em->persist($entity);
                 $em->flush();
                //  $image_path = $entity->getWebPath(); 
                 $userinfo = array();
                 $userimage = $entity->getIphoneImage();
                 $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
                 $userinfo['heightPerInch']= $entity->getDeviceUserPerInchPixelHeight();
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
#------------------------Constant Fetching Web Service-----------------------------------------------------#
    public function ConstantValuesAction() {
        $utility_helper = $this->get('admin.helper.utility');
        return new response(json_encode($utility_helper->getDeviceBootstrap()));
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