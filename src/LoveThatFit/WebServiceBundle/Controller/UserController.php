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
   
    public function loginAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);

        $user_helper = $this->get('user.helper.user');

        $email = $decoded['email'];
        $password = $decoded['password'];
         
        /*$email ='my_web115115@gmail.com';
         $password ='123456';*/
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));
 
        if (count($entity) > 0) {
            $user_info = $user_helper->loginWebService($entity, $password, $email);
            if (isset($user_info['id'])) {
                $user_id = $user_info['id'];
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
                $user_info['path'] = $baseurl;
            }
            return new response(json_encode($user_info));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
    }

#------------------------------Edit Profile----------------------------------------------------------#    
  
#----------------------------------------------------------------------------------------------------#
    public function editProfileAction()
{
         $request = $this->getRequest();
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $decoded = json_decode($jsonInput,true);
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

    $entity=$user->editProfileServiceHelper($decoded);   
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

        $user = $this->get('user.helper.user');
        $entity = $user->findByEmail($email);

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
        $user_info = $user->RegistrationWebSerive($request,$request_array);
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
            $userinfo = $user->findByEmail($email);
            //$id = $userinfo['id'];
            $msg=array();
            $msg=$user->measurementEditWebService($userinfo['id'],$request_array);
            
            /*$em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
            $measurement = $entity->getMeasurement();
            if ($measurement) {

                $measurement->setUpdatedAt(new \DateTime('now'));

                if ($request_array['weight']) {
                    $measurement->setWeight($request_array['weight']);
                }
                if ($request_array['height']) {
                    $measurement->setHeight($request_array['height']);
                }
                if ($request_array['waist']) {
                    $measurement->setWaist($request_array['waist']);
                }
                if ($request_array['hip']) {
                    $measurement->setHip($request_array['hip']);
                }
                if ($request_array['bust']) {
                    $measurement->setBust($request_array['bust']);
                }
                if ($request_array['neck']) {
                    $measurement->setNeck($request_array['neck']);
                }
                if ($request_array['inseam']) {
                    $measurement->setInseam($request_array['inseam']);
                }
                if ($request_array['chest']) {
                    $measurement->setChest($request_array['chest']);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($measurement);
                $em->flush();

                return new Response(json_encode(array('Message' => 'success')));
            } else {
                return new Response(json_encode(array('Message' => 'Sorry We can not find measurment')));
            }*/
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
        
        /*$email = $request_array['email'];
        $iphone_shoulder_height = $request_array['iphone_shoulder_height'];
        $iphone_outseam = $request_array['iphone_outseam'];

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $user_id = $entity->getId();
            $birth_date = $entity->getBirthDate();
            $userinfo = array();
            $userinfo['id'] = $user_id;
            $userinfo['email'] = $email;
            $userinfo['first_name'] = $entity->getFirstName();
            $userinfo['last_name'] = $entity->getLastName();
            $userinfo['zipcode'] = $entity->getZipcode();
            $userinfo['gender'] = $entity->getGender();
            $userinfo['authTokenWebService']=$entity->getAuthToken();
            if (isset($birth_date)) {
                $userinfo['birth_date'] = $birth_date->format('Y-m-d');
            }

            $userinfo['image'] = $entity->getImage();
            $userinfo['avatar'] = $entity->getAvatar();
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
            $userinfo['path'] = $baseurl;


            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
            $measurement = $entity->getMeasurement();
            if ($measurement) {
                $measurement->setUpdatedAt(new \DateTime('now'));

                if ($iphone_shoulder_height) {
                    $measurement->setIphoneShoulderHeight($iphone_shoulder_height);
                }
                if ($iphone_outseam) {
                    $measurement->setIphoneOutseam($iphone_outseam);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($measurement);
                $em->flush();
            }



            $userinfo['weight'] = $measurement->getWeight();
            $userinfo['height'] = $measurement->getHeight();
            $userinfo['waist'] = $measurement->getWaist();
            $userinfo['hip'] = $measurement->getHip();

            $userinfo['bust'] = $measurement->getBust();
            $userinfo['chest'] = $measurement->getChest();
            $userinfo['neck'] = $measurement->getNeck();
            $userinfo['inseam'] = $measurement->getInseam();
            $userinfo['back'] = $measurement->getBack();
            if (!$userinfo['back']) {
                $userinfo['back'] = 15.5;
            }
            $userinfo['iphone_shoulder_height'] = $measurement->getIphoneShoulderHeight();
            if (!$userinfo['iphone_shoulder_height']) {
                $userinfo['iphone_shoulder_height'] = 150;
            }
            $userinfo['iphone_outseam'] = $measurement->getIphoneOutseam();
            if (!$userinfo['iphone_outseam']) {
                $userinfo['iphone_outseam'] = 400;
            }

            return new Response(json_encode($userinfo));
        } else {
            return new Response(json_encode(array('Message' => 'Invalid Email')));
        }
         *
         */
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
                $userinfo['image'] = $userimage;
                $userinfo['path'] = $baseurl;
                return new Response(json_encode($userinfo));
            } else {
                return new response(json_encode(array('Message' => 'Image not uploaded')));
            }
        } else {
            return new response(json_encode(array('Message' => 'We can not find user')));
        }
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