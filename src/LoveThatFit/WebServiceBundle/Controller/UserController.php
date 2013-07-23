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
        $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $decoded = json_decode($jsonInput,true);
       $email=$decoded['email'];
       $password=$decoded['password'];
      
         $em = $this->getDoctrine()->getManager();
         $entity =$em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email'=>$email));
        
            if (count($entity) >0) {
             
                $authTokenWebService=$entity->getAuthToken();
                $user_db_password = $entity->getPassword();
                $salt_value_db = $entity->getSalt();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password_old_enc = $encoder->encodePassword($password, $salt_value_db);
                if ($user_db_password == $password_old_enc) {
                    $user_id=$entity->getId();
                    $first_name=$entity->getFirstName();
                    $last_name=$entity->getLastName();
                    $gender=$entity->getGender();
                    $zipcode=$entity->getZipcode();
                    $birth_date=$entity->getBirthDate();
                    $image=$entity->getImage();
                    $avatar=$entity->getAvatar();
                   $userinfo=array();
                   $userinfo['id']=$user_id;
                   $userinfo['email']=$email;
                   $userinfo['first_name']=$first_name;
                   $userinfo['last_name']=$last_name;
                   $userinfo['zipcode']=$zipcode;
                   $userinfo['gender']=$gender;
                   $userinfo['authTokenWebService']=$authTokenWebService;
                  
                   if(isset($birth_date)){
                   $userinfo['birth_date']= $birth_date->format('Y-m-d');
                   }
                   
                   $userinfo['image']=$image;
                   $userinfo['avatar']=$avatar;
                   $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$user_id."/";
                   $userinfo['path']=$baseurl;
                 
                   
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
                $measurement = $entity->getMeasurement();
               
                if($measurement)
                {
                $userinfo['weight'] = $measurement->getWeight();
                
                $userinfo['height'] = $measurement->getHeight();
               
                $userinfo['waist'] = $measurement->getWaist();
                $userinfo['hip'] = $measurement->getHip();

                $userinfo['bust'] = $measurement->getBust();
                $userinfo['chest'] = $measurement->getChest();
                $userinfo['neck'] = $measurement->getNeck();
                $userinfo['inseam'] = $measurement->getInseam();
                $userinfo['back'] = $measurement->getBack();
                $userinfo['iphone_shoulder_height'] = $measurement->getIphoneShoulderHeight();
                $userinfo['iphone_outseam'] = $measurement->getIphoneOutseam();
                   
           }
           else
           {
                $userinfo['weight'] = 0;
                $userinfo['height'] = 0;
                $userinfo['hip'] = 0;
                $userinfo['bust'] = 0;
                $userinfo['chest'] = 0;
                $userinfo['neck'] = 0;
                $userinfo['inseam'] = 0;
                $userinfo['back'] = 0;
                $userinfo['iphone_shoulder_height'] = 0;
                $userinfo['iphone_outseam'] = 0;
                }    
          if(!$userinfo['back'])
                   {
                       $userinfo['back']=15.5;
                   }   
            if (!$userinfo['iphone_shoulder_height']) {
                        $userinfo['iphone_shoulder_height'] = 150;
                    }
                 
                    if (!$userinfo['iphone_outseam']) {
                        $userinfo['iphone_outseam'] = 400;
                    }
                     return new Response(json_encode($userinfo));
                } else {
                     return new Response(json_encode(array('Message'=>'Invalid Password')));
                }
             
       }else{
           return new Response(json_encode(array('Message'=>'Invalid Email')));
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
    if($entity)
    {
    return new Response(json_encode(array('Message'=>'Update Sucessfully')));
    }
    else 
   {
     return new Response(json_encode(array('Message'=>'We can not find user')));
   }
          
}        
#------------------------------End of Edit Profile---------------------------------------------------#
#------------------------------User Profile----------------------------------------------------------#
public function userProfileAction()
{
        $request = $this->getRequest();
        $handle = fopen('php://input','r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput,true);
        $email=$decoded['email'];
        
        $user=$this->get('user.helper.user');
        $entity=$user->findByEmail($email);
        
          if (count($entity) >0) {
              $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$entity['id']."/";
              $entity['path']=$baseurl; 
              return new Response(json_encode($entity));
         } 
            
            else {
                     return new Response(json_encode(array('Message'=>'Invalid Email')));
                }
}
#------------------------------End Of User Profile---------------------------------------------------#
#------------------------------------------Registration---------------------------------------------#
    public function registrationCreateAction() {

        $request = $this->getRequest();
        $handle = fopen('php://input','r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput,true);
        
        
        $email = $request_array['email'];
        $password =$request_array['password'];
        $gender = $request_array['gender'];
        $zipcode = $request_array['zipcode'];
       
        #-------------------Measurement data---------------------#
         if (isset($request_array['weight'])) {
                $weight = $request_array['weight'];
            }

            if (isset($request_array['height'])) {
                $height = $request_array['height'];
            }

            if (isset($request_array['waist'])) {
                $waist = $request_array['waist'];
            }

            if (isset($request_array['hip'])) {
                $hip = $request_array['hip'];
            }

            if (isset($request_array['bust'])) {
                $bust = $request_array['bust'];
            }


            if (isset($request_array['neck'])) {
                $neck = $request_array['neck'];
            }

            if (isset($request_array['inseam'])) {
                $inseam = $request_array['inseam'];
            }



            if (isset($request_array['chest'])) {
                $chest = $request_array['chest'];
            }

            if(isset($request_array['sc_top_id']))
            {
                 $sc_top_id=$request_array['sc_top_id'];
            }
            else
            {
                 $sc_top_id=0;
            }
            if(isset($request_array['sc_bottom_id']))
            {
                 $sc_bottom_id=$request_array['sc_bottom_id'];
            }
             else {$sc_bottom_id=0;}
            if(isset($request_array['sc_dress_id']))
            {
                 $sc_dress_id=$request_array['sc_dress_id'];
            }
            else{
                 $sc_dress_id=0;
            }
       
           #-----------------End of Measuremnt data-----------------------# 
            if ($this->isDuplicateEmail(Null, $email)) {
                return new Response(json_encode(array('Message' => 'The Email already exists',)));
            }
            else{
            $user = new User();

            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($password, $user->getSalt());
            //$authTokenWebService=$this->genrateToken($email);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->setGender($gender);
            $user->setZipcode($zipcode);
            //$user->setAuthTokenWebService($authTokenWebService);
            $user->generateAuthenticationToken();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush(); 
           
           
            
 #----------------------Set Data of Measuremnt -------------------------------#
           $measurement = new Measurement();
           $size_chart=new SizeChart();
           if($sc_top_id){
           $top_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($sc_top_id);
           $measurement->setTopFittingSizeChart($top_size); //
            }
           if($sc_bottom_id){
              $bottom_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($sc_bottom_id);
               $measurement->setBottomFittingSizeChart($bottom_size); //
               }     
           if($sc_dress_id){
           $dress_size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findOneById($sc_dress_id);
           $measurement->setDressFittingSizeChart($dress_size); //
           }
        
           
            $measurement->setUser($user);
            $measurement->setUpdatedAt(new \DateTime('now'));


            $measurement->setWeight($weight);

            $measurement->setHeight($height);

            if ($request_array['waist']) {
                $measurement->setWaist($waist);
          
           }
          if ($request_array['hip']) {
             $measurement->setHip($hip);
          }
            if (isset($request_array['bust'])) {
                $measurement->setBust($bust);
            }
           
           
            if (isset($request_array['inseam'])) {
                $measurement->setInseam($inseam);
            }
           
            if (isset($request_array['chest'])) {
                $measurement->setChest($chest);
            }
           
            if (isset($request_array['neck'])) {
                $measurement->setNeck($neck);
            }
#---------------------------------------------------Getting Data-----------------------------#
             $userinfo=array();
   #--------------------User Info-------------------------------#
            
                    $birth_date=$user->getBirthDate();
                    
                   $userinfo['id']=$user->getId();
                   $userinfo['email']=$user->getEmail();
                   $userinfo['first_name']=$user->getFirstName();
                   $userinfo['last_name']=$user->getLastName();
                   $userinfo['zipcode']=$user->getZipcode();
                   $userinfo['gender']=$user->getGender();
                  
                   if(isset($birth_date)){
                   $userinfo['birth_date']= $birth_date->format('Y-m-d');
                   }
                   
                   $userinfo['image']=$user->getImage();
                   $userinfo['avatar']=$user->getAvatar();
                   $userinfo['authTokenWebService']=$user->getAuthToken();
                   $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$userinfo['id']."/";
                   $userinfo['path']=$baseurl;
    #-----------------------Measurement Info--------------------#
            $userinfo['weight']=$measurement->getWeight();
            $userinfo['height']=$measurement->getHeight();
            $userinfo['waist']=$measurement->getWaist();
            $userinfo['hip']=$measurement->getHip();
            $userinfo['bust']=$measurement->getBust();
            $userinfo['inseam']=$measurement->getInseam();
            $userinfo['chest']=$measurement->getChest();
            $userinfo['sleeve']=$measurement->getSleeve();
            $userinfo['neck']=$measurement->getNeck();
           $userinfo['back'] = $measurement->getBack();
            if (!$userinfo['back']) {
                $userinfo['back'] = 15.5;
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($measurement);
            $em->flush();
  #------------------------End of Seting measuremt----------#          
            return new Response(json_encode($userinfo));
            }
    }

 #-------------------------Measurement Edit Web Service-----------------------------------------------------------------------------#       
 public function measurementEditAction() {

        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $email = $request_array['email'];
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

        if ($this->isDuplicateEmail(Null, $email)) {

            $user = $this->get('user.helper.user');
            $userinfo = $user->findByEmail($email);
            $id = $userinfo['id'];

            $em = $this->getDoctrine()->getManager();
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
            }
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

        
        $email = $request_array['email'];
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
        if (isset($request_array['email'])) {
            $email = $request_array['email'];
        }
        if (isset($request_array['password'])) {
            $password = $request_array['password'];
        }
        if (isset($request_array['old_password'])) {
            $old_password = $request_array['old_password'];
        }


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $user_db_password = $entity->getPassword();
            $salt_value_old = $entity->getSalt();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password_old_enc = $encoder->encodePassword($old_password, $salt_value_old);

            if ($password_old_enc == $user_db_password) {

                $entity->setUpdatedAt(new \DateTime('now'));
                $password = $encoder->encodePassword($password, $salt_value_old);
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                return new response(json_encode(array('Message' => 'Paasword has been updated')));
            } else {
                return new response(json_encode(array('Message' => 'Invalid Password')));
            }
        } else {

            return new response(json_encode(array('Message' => 'Invalid Email')));
        }
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
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
            $file_name=$_FILES["file"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $newFilename = 'iphone'."." . $ext;
            $entity->setImage($newFilename);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $entity->getAbsoluteIphonePath())) {
                
               $em->persist($entity);
                $em->flush();
                //  $image_path = $entity->getWebPath(); 
                $userinfo = array();
                $userimage = $entity->getImage();

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
#-----------------------------test form-----------------------------------------#
    public function imageFormAction() {
     return $this->render('LoveThatFitWebServiceBundle::json.html.twig');
    }
# ------------------------------    
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
    #----------------------------------------------------------------------------------------------------#   
    private function genrateToken($email) {
        return md5(time() . $email);
    }   
 #------------------------CHECK TOKEN-------------------------------------------------------------------#   
 private function checkToken($email, $token) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $getAuthTokenWebService = $entity->getAuthTokenWebService();
            if ($getAuthTokenWebService == $tken) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return json_encode(array('Message' => 'User Not Found'));
        }
    }

}

// End of Class