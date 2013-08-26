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
#--------------------------------Login ----------------------------------------------------------------#
  #---------------------Login Service---------------------------------------------------------#
   
    
    public function emailCheckAction(){
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $email = $decoded['email'];
        if ($email) {
            $user_helper = $this->get('user.helper.user');
            $checkEmail = $user_helper->emailCheck($email);
            return new response(json_encode($checkEmail));
        } else {
            return new Response(json_encode(array('Message' => 'Email missing')));
        }
    }
    public function loginAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $user_helper = $this->get('user.helper.user');
        $user_info = $user_helper->loginWebService($decoded,$request);
        return new response(json_encode($user_info));
      
    }
#------------------------------Edit Profile----------------------------------------------------------#    
  
#----------------------------------------------------------------------------------------------------#
    public function editProfileAction()
{
         $request = $this->getRequest();
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $decoded = json_decode($jsonInput,true);
      /*   $decoded=array();
        $decoded=array('email'=>'oldnavywomen0@ltf.com','firstName'=>'test','password'=>'123456','gender'=>'f','zipcode'=>'123','sc_top_id'=>'2','sc_bottom_id'=>'2','sc_dress_id'=>'2',
            'weight'=>4,'neck'=>4,'bust'=>5);*/
 #------------------------------Authentication of Token--------------------------------------------#
        $user = $this->get('user.helper.user');
       $authTokenWebService = $decoded['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
#------------------------------End Of Authentication Token--------------------------------------#

    $entity=$user->updateWithUserArray($decoded);   
       // return new response(json_encode($entity));
    if ($entity) {
            return new Response(json_encode(array('Message' => 'Update Sucessfully')));
        } else {
            return new Response(json_encode(array('Message' => 'We can not find user')));
        }
          
}        
#------------------------------End of Edit Profile---------------------------------------------------#
#------------------------------User Profile----------------------------------------------------------#
public function userProfileAction()
{
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $email = $decoded['email'];
       /* $email='oldnavywomen0@ltf.com';*/
        $user = $this->get('user.helper.user');
        $entity = $user->getArrayByEmail($email);

        if (count($entity) > 0) {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $entity['id'] . "/";
            $entity['path'] = $baseurl;
            return new Response(json_encode($entity));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
    }
#------------------------------End Of User Profile---------------------------------------------------#
#------------------------------------------Registration---------------------------------------------#
 public function registrationCreateAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('user.helper.user');
        
        #/!!!!!!!!!!!!!!!!!!!!!!!!!!!! TESTING CODE SHOUDL BE REMOVED #/
     /*  $request_array=array();
        $request_array=array('email'=>'abcdfdefg@gmail.com','password'=>'123456','gender'=>'f','zipcode'=>'123','sc_top_id'=>'2','sc_bottom_id'=>'2','sc_dress_id'=>'2',
            'weight'=>4,'neck'=>4,'bust'=>5);*/
        
        $user_info = $user->registerWithReqestArray($request,$request_array);
        return new response(json_encode($user_info));
    }

 #-------------------------Measurement Edit Web Service-----------------------------------------------------------------------------#       
 public function measurementEditAction() {
       $user = $this->get('user.helper.user');
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $email = $request_array['email'];
       
#------------------------------Authentication of Token--------------------------------------------#
        $user = $this->get('user.helper.user');
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

            $user = $this->get('user.helper.user');
            $userinfo = $user->getArrayByEmail($email);
           
            $msg=array();
            $msg=$user->updateMeasurementWithReqestArray($userinfo['id'],$request_array);
            
           
             return new Response(json_encode($msg));
        } else {
            return new Response(json_encode(array('Message' => 'We can not find user')));
        }
    }
#------------------------------------Shoulder Height and Outseam Ration Edit/Update---------------------------#
 public function shoulder_outseamEditAction() {

        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('user.helper.user');
   #---------------------------Authentication of Token--------------------------------------------#
       
        $authTokenWebService = $request_array['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
 #-------------------------------End Of Authentication Token--------------------------------#
        $msg=$user->shoulderOutseamWebService($request,$request_array);
        return new response(json_encode($msg));
        
       
    }

    #--------------Change Password--------------------------------------------------------#
 public function changePasswordAction() {
       
          
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $request_array  = json_decode($jsonInput,true);
#---------------------------Authentication of Token--------------------------------------------#
         $user = $this->get('user.helper.user');
      $authTokenWebService = $request_array['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
#-------------------------------End Of Authentication Token--------------------------------#
         
           $msg=$user->webServiceChangePassword($request_array);
         return new response(json_encode($msg));
    }

 #---------------------------------------Image Upload---------------------------------------#   
 public function imageUploadAction() {
     $request = $this->getRequest();

        $email = $_POST['email'];

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

                $em->persist($entity);
                $em->flush();
                //  $image_path = $entity->getWebPath(); 
                $userinfo = array();
                $userimage = $entity->getIphoneImage();
                 $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
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
    
#----------------------Avatar Image Uploading -------------------------------------------------------------#
public function avatarUploadAction() {
     $request = $this->getRequest();

        $email = $_POST['email'];
         $user_helper = $this->get('user.helper.user');
        $email = $user_helper ->findOneBy(array('email' => $email));
        if ($email) {
           // $em = $this->getDoctrine()->getManager();
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
        //$utility_help=$utility_helper->getDeviceBootstrap();
      /*  $constant_values=array();
        $constant_values['pixcel_per_inch']=$utility_help['resolution_scale']['pixcel_per_inch'];
        $constant_values['inches']=$utility_help['resolution_scale']['inches'];
        $constant_values['standard']=$utility_help['resolution_scale']['standard'];
        $constant_values['iphone4s']=$utility_help['resolution_scale']['iphone4s'];
        $constant_values['iphone5']=$utility_help['resolution_scale']['iphone5'];
        $constant_values['ipad']=$utility_help['resolution_scale']['ipad'];
        $constant_values['ipad_retina']=$utility_help['resolution_scale']['ipad_retina'];*/
        return new response(json_encode($utility_helper->getDeviceBootstrap()));
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