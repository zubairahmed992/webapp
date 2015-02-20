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

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
# USER_AUTHENTICATION_URL   /web_service/login
#----------------------------------------------
    public function loginAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
     //  $decoded=array('email'=>'','password'=>'Apple2013','deviceType'=>'');        
        $user_info = $this->get('webservice.helper.user')->loginWebService($decoded,$request);      
        return new response(json_encode($user_info));
      
    }
    
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    #  USER_REGISTRATION_URL	/web_service/register
#-----------------------------------------
 public function registrationCreateAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
        //{"email":"kamran@hotmail.com","password":"123456","gender":"f","zipcode":"123456","weight":"80.00","height":"61.00","bust":"0.00","neck":"0.00","chest":"0.00","inseam":"0.00","hip":"0.00","waist":"0.00","deviceType":"ipad","bodyShape":"pear","braSize":"30 b","bodyType":"Regular","deviceId":"tempID","sleeve":"0.00","targetTop":"Banana Republic","targetBottom":"CARMEN MARC VALVO","targetDress":"CARMEN MARC VALVO","topSize":"0","bottomSize":"0","dressSize":"4"}
        //  $request_array=array();
        //$request_array=array('deviceId'=>'tempID','email'=>'kamrantes32@hotmail.com','password'=>'123456','gender'=>'f','zipcode'=>'123','weight'=>40.00, "height"=>"73.000000",
        // 'targetTop'=>'Banana Republic','topSize'=>'6',"inseam"=>"36.000000","hip"=>"0.000000","waist"=>"36.000000","deviceType"=>"ipad",
        // 'neck'=>4,'bust'=>5,'bodyType'=>'Regular','bodyShape'=>'banana','targetBottom'=>'CARMEN MARC VALVO','targetDress'=>'CARMEN MARC VALVO',"braSize"=>"28 a","bottomSize"=>"00","dressSize"=>"0");
        $user_info = $user->registerWithReqestArray($request,$request_array);
        return new response(json_encode($user_info));
    }

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    # CHANGE_PASSWORD_URL	/web_service/change_password
#-----------------------------------------------------------
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

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
# EDIT_PROFILE_URL	/web_service/edit_profile
#-----------------------------------------    
public function editProfileAction()
{        $request = $this->getRequest();
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $decoded = json_decode($jsonInput,true);
      /*  $decoded=array();
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

/*
#------------------------------User Profile-------------------------------------#
public function userProfileAction()
{       $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $target_array = $request->request->all();
      
       
         if(isset($target_array['type'])=='type'){
             $email = $target_array['email'];
         }else{
        $email = $decoded['email'];
         }
        $user = $this->get('webservice.helper.user');
         $chk_email = $user->findOneBy($email);
         //return new response(json_encode(count($chk_email)));
           if (count($chk_email)>0) {
        $entity = $user->getArrayByEmail($email);
       $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $entity['userId'] . "/";
        $entity['path'] = $baseurl;
            return new Response(json_encode($entity));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
}
*/
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
# SAVE_MARKING_URL	/web_service/iphone_shoulder_outseam
 public function shoulderOutseamEditAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
       // $request_array= array();
        
      //  $request_array=array('email'=>'iphone@gmail.com','authTokenWebService'=>'121c421783cd4d71d871ec16a1296091','deviceType'=>'4s','heightPerInch'=>'6');
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

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
#    FORGET_PASSWORD_SENDEMAIL	/web_service/forget_password
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

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
# EMAIL_VALIDATION_URL	/web_service/checkemail	
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
  
 #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
 #EDIT_MEASUREMENT_URL	/web_service/measurement_edit	
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

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    #UPLOAD_MANNEQUIN_URL	/web_service/image_upload
#------------------------    
    
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
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    #DEMO_UPLOAD_MANNEQUIN_URL	/web_service/demo_image_upload
#------------------------    
    
    public function demoImageUploadAction() {
        
        $request = $this->getRequest();
                
        $email = $_POST['email'];
        $heightPerInch = $_POST['heightPerInch'];
        $deviceType = $_POST['deviceType'];
                
        $user = $this->get('user.helper.user')->findByEmail($email);        
        
        if ($user) {
            #-------- updating head & foot markers in user measurement
            $measurement = $user->getMeasurement();
            $measurement->setIphoneHeadHeight($_POST['iphoneHeadHeight'] );
            $measurement->setIphoneFootHeight($_POST['iphoneFootHeight'] );            
            $this->get('user.helper.measurement')->saveMeasurement($measurement);
            #----------------------------
            $file_name = $_FILES["file"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $newFilename = 'cropped' . "." . $ext;
            #$user->setIphoneImage($newFilename);
            $user->setImage($newFilename);
            #creating abs path
            $abs_path = $user->getUploadRootDir().'/'.$newFilename;
            
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0700);
            }
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $abs_path)) {
                $this->get('webservice.helper.user')->setMarkingDeviceType($user, $deviceType, $heightPerInch);
                $this->get('webservice.helper.user')->saveUser($user);
                $userinfo = array();
                
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user->getId() . "/";
                $userinfo['heightPerInch'] = $this->get('webservice.helper.user')->getUserDeviceTypeAndMarking($user, $deviceType); 
                $userinfo['iphoneImage'] = $user->getImage();
                $userinfo['path'] = $baseurl;
                return new Response(json_encode($userinfo));
            } else {
                return new response(json_encode(array('Message' => 'Image not uploaded')));
            }
        } else {
            return new response(json_encode(array('Message' => 'We can not find user')));
        }
    }

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    #UPLOAD_MANNEQUIN_URL	/web_service/image_upload
#-----------------------------------
    public function deviceImageUploadAction() {
                
        $email = $_POST['email'];
        $device_type = $_POST['deviceType'];
        $device_name = $_POST['deviceName'];
        $heightPerInch = $_POST['heightPerInch'];
        $user = $email != null ? $this->get('user.helper.user')->findByEmail($email) : null;
        if (!$user) {
            return new response(json_encode(array('Message' => 'Email Not Found')));
        }
        $user_device = $this->get('user.helper.userdevices')->findOneByDeviceTypeAndUser($user->getId(), $device_type);
        if (!$user_device) {
            $user_device = $this->get('user.helper.userdevices')->createNew($user);
            $user_device->setDeviceType($device_type);            
        }
        $user_device->setDeviceName($device_name);
        $user_device->file = $_FILES["file"];

        if ($heightPerInch) {
            $user_device->setDeviceUserPerInchPixelHeight($heightPerInch);
        }

        $user_device->upload();
        $this->get('user.helper.userdevices')->saveUserDevices($user_device);
        $userinfo = array();
        $request = $this->getRequest();
        $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() .'/'. $user_device->getWebPath();
        $userinfo['heightPerInch'] = $user_device->getDeviceUserPerInchPixelHeight();
        $userinfo['iphoneImage'] = $user_device->getDeviceImage();
        $userinfo['path'] = $image_path;
        
        return new Response(json_encode($userinfo));
    }    
    
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    #AVATAR_URL	/web_service/upload_avatar
#-----------------------------------------
public function avatarUploadAction() {
    $request = $this->getRequest();
        if(!isset($_POST['email'])){
            return new response(json_encode(array('Message' => 'Email Not Found')));
        }
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
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    # CONSTANT_URL	/web_service/constant_values
#---------------------------------------------------
    public function ConstantValuesAction() {
        $utility_helper = $this->get('admin.helper.utility');
        $data=array();
        $data=$utility_helper->getDeviceBootstrap();
        $data['body_type']=$utility_helper->getBodyTypesSearching();
        $data['body_shape']=$utility_helper->getBodyShape();
        $data['neck_size']=$this->get('admin.helper.productsizes')->manSizeList($neck=1,$sleeve=0,$waist=0,$inseam=0);
        
       // $data['neck_size']=sort($data['neck_sizes']);
        $data['sleeve_size']=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=1,$waist=0,$inseam=0);
        $data['waist_size']=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=1,$inseam=0);
        $data['inseam_size']=$this->get('admin.helper.productsizes')->manSizeList($neck=0,$sleeve=0,$waist=0,$inseam=1);
     //   $brandList=$this->get('admin.helper.sizechart')->getBrandSizeTitleArray();
        $data['fittingStatus']=$this->get('webservice.helper.product')->getFittingStatus();
        return new response(json_encode(($data)));
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
# SIZE_CHART_URL	/web_service/registration_sizechart	    
#---------------------------------------------
   public function sizeChartForRegAction(){
       $data['sizeChart']=$this->get('admin.helper.sizechart')->getBrandSizeTitleArray();
        return new response(json_encode(($data)));
       
   } 
   
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
#   Standard sizes for search
#-----------------------------
 public function standardSizesAction($gender='f') {
        $all = $this->get('admin.helper.size')->getDefaultArray();
        if($gender=='m'){
            $data['sizes'] = array(
                'letter' => $all['sizes']['man']['letter'],
                'chest' => $all['sizes']['man']['chest'],
                'waist' => $all['sizes']['man']['waist'],
                'neck' => $all['sizes']['man']['neck'],
                'sleeve' => $all['sizes']['man']['sleeve'],
            );
        }else{
            $data['sizes'] = array(            
                'letter' => $all['sizes']['woman']['letter'],
                'dress' => $all['sizes']['woman']['number'],
                'waist' => $all['sizes']['woman']['waist'],
            );
        }
        
        return new response(json_encode(($data)));
    }


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
    # FORGET_PASSWORD_SENDCODE /web_service/check_token_forget_password
#---------------------------------------------------------------    
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
 

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
 # FORGET_PASSWORD_NEWPASSWORD	/web_service/check_update_password
 #-----------------------------------------------------------------
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

