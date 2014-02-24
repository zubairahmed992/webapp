<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\UserBundle\Entity\User;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;

class WebServiceUserHelper {

    /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;
    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    //---------------------------------------------------------------------   
    #!!!!!!! User Created at and Update At  ----------------!!!#
    public function createNewUser() {
        $class = $this->class;
        $user = new $class();
        $user->setCreatedAt(new \DateTime('now'));
        $user->setUpdatedAt(new \DateTime('now'));            
        return $user;
    }

//-------------------------------------------------------

    public function saveUser(User $user) {
        $user->setUpdatedAt(new \DateTime('now'));            
        $this->em->persist($user);
        $this->em->flush();
    }

#----------------------------All Find Method ----------------------------------#    
    public function find($id) {
        return $this->repo->findOneBy(array('id' => $id));
    }

//---------------------------------------------------------
    public function findByEmail($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }
//-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
#------------------------------------------------------------------------------#
    public function findOneBy($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }

#------------------------------------------------------------------------------#
    public function findByName($firstname, $lastname) {
        return $this->repo->findByName($firstname, $lastname);        
    }

#------------------------------------------------------------------------------#
    public function findByAuthToken($auth_token){
        return $this->repo->loadUserByAuthToken($auth_token);
    }
#----------------- Password encoding ------------------------------------------#
    public function encodePassword(User $user) {
        return $this->encodeThisPassword($user, $user->getPassword());
    }
#------------------------------------------------------------------------------#
    private function encodeThisPassword(User $user, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        return $password;
    }
#------------------------------------------------------------------------------#
    public function matchPassword(User $user, $password) {
        $password = $this->encodeThisPassword($user, $password);
        if ($user->getPassword() == $password) {
            return true;
        }
        return false;
    }
#-------------------------Web Service For Email Checking------------------------#
     public function emailCheck($email) {
        $entity = $this->repo->findOneBy(array('email' => $email));
        if(count($entity)>0){
           return array('Message' => 'The Email already exists');  
        }else{
             return array('Message' => 'Valid Email');
        }
    }
#--------------------------------------Forget Password Checking-----------------# 
    public function emailCheckForgetPassowrd($email) {
        if ($this->isDuplicateEmail(Null, $email) == true) {
            return true;
        } else {
            return false;
        }
    }
#------------------------------------------------------------------------------#
    public function isDuplicateEmail($id, $email) {
        return $this->repo->isDuplicateEmail($id, $email);
    }
#--------------Forget Password Webservices ------------------------------------# 
    public function updateTokenSendEmail($request,$email){
        $_user=$this->repo->findByEmail($email);
        $uniq_id=  uniqid();
        $_user->setAuthToken($uniq_id);
        $this->saveUser($_user) ;
        return $_user;
    }
  #---------------Update Authenicated Token------------------------------------#  
  public function checkTokenforgetPassword($auth_token){
  $entity = $this->repo->findOneBy(array('authToken' => $auth_token));
        if (count($entity) > 0) {
            $user=array();
            $user['email']=$entity->getEmail();
            return $user;
        } else {
            return array('status' => False, 'Message' => 'Authentication Failure');
        };   
   return $user;
  }
#--------------------------Update Forget Password------------------------------#
public function updateForgetPassword($email,$password){
     $entity = $this->repo->findOneBy(array('email' => $email));// this to replace with renamed method
        if (count($entity) > 0) {
                $password = $this->encodeThisPassword($entity, $password);
                $entity->setPassword($password);
                $this->saveUser($entity);
                return array('Message' => 'Paasword has been updated');
        } else {
            return array('Message' => 'Invalid Email');
        }
}  
#----------------------------Chek Token ---------------------------------------#
public function authenticateToken($token) {
        $entity = $this->repo->findOneBy(array('authToken' => $token));
        if (count($entity) > 0) {
            return array('status' => True, 'Message' => 'Authentication Success');
        } else {
            return array('status' => False, 'Message' => 'Authentication Failure');
        }
    }
#-----------------------Save User at Registeration  ---------------------------#
public function registerUser(User $user) {
        $user->setCreatedAt(new \DateTime('now'));
        $user->setUpdatedAt(new \DateTime('now'));
        $password = $this->encodePassword($user);
        $user->setPassword($password);
        $user->generateAuthenticationToken();
        $measurement = new Measurement();
        $user->setMeasurement($measurement);
        $this->saveUser($user);
        return $user;
    }
#-----------------------Get User Measurment ------------------------------------#
 public function getMeasurement($user) {
        $measurement = $user->getMeasurement();
        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        return $measurement;
    }
#------------------------------------------------------------------------------#
    public function getArrayByEmail($email) {//getUserArrayByEmail
        $entity = $this->repo->findOneBy(array('email' => $email));
        $userinfo = array();
        $userinfo = $this->fillUserArray($entity);
        return $userinfo;
    }

#--------------------------------User Detail Array -----------------------------#
 private function gettingUserDetailArray($entity, $request) {
        // change name getUserDetailArrayWithRequestArray
        $userinfo = $this->fillUserArray($entity);
        $entity = $this->repo->find($userinfo['userId']);
        $measurement = $entity->getMeasurement();        
        $user_measurment = $this->fillMeasurementArray($measurement);
        $userinfo['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $userinfo['userId'] . "/";
        $userinfo['authTokenWebService'] = $entity->getAuthToken();        
        return array_merge($userinfo, $user_measurment);
    }
#---------------------Edit/Update Profile for Web Services---------------------#
    public function updateWithUserArray($decoded) {
        $email = $decoded['email'];
        if ($email) {
            $user = $this->repo->findOneBy(array('email' => $email));            
            $user->setUpdatedAt(new \DateTime('now'));            
            $this->saveUser($this->setObjectWithArray($user, $decoded));
            return true;
        } else {
            return false;
        }
    }
#-------------------------Login Web Service------------------------------------#
  public function loginWebService($request_array, $request) {
        $email = $request_array['email'];
        $password = $request_array['password'];
        $deviceType=$request_array['deviceType'];
        /*$email ='amrani192@gmail.com';
        $password =''; 
        $deviceType="iphone4s";*/
       
        $entity = $this->findOneBy($email);
        if (count($entity) > 0) {
                $device_type=array();
                 $pre_device_type=$entity->getDeviceType();
            if ($this->matchPassword($entity, $password)) {
               
                 $device_type['preDeviceType']=$pre_device_type;
                 $entity->setDeviceType($deviceType);
                 $this->saveUser($entity);
                 $user_info=$this->gettingUserDetailArray($entity, $request);
                    return array_merge($user_info,$device_type);                
            } else {
                return array('Message' => 'Invalid Password');
            }
        } else {
            return array('Message' => 'Invalid Email');
        }
    }
#-------------------------------Web Service For Registration-------------------#
public function registerWithReqestArray(Request $request, $request_array) {

        $sizeChartHelper = $this->container->get('admin.helper.sizechart');
        
        $email = $request_array['email'];
        $password = $request_array['password'];

        if ($this->isDuplicateEmail(Null, $email)) {
            return array('Message' => 'The Email already exists');
        } else {
            $user = $this->createNewUser();            
            $password=  $this->encodeThisPassword($user, $password);
            $user->setPassword($password);
            $user = $this->setObjectWithArray($user, $request_array);
            $user->generateAuthenticationToken();
            
             //send registration email ....            
            $this->container->get('mail_helper')->sendRegistrationEmail($user);
                
            $measurement = new Measurement();
            $measurement = $this->setSizechartInMeasurment($measurement, $request_array);
            $measurement->setUser($user);
            $measurement->setUpdatedAt(new \DateTime('now'));
            $measurement = $this->setMeasurmentObjectWithArray($measurement, $request_array);
            $user->setMeasurement($measurement);
            $this->saveUser($user);                        
            return $this->gettingUserDetailArray($user, $request);             
        }
    }
#-----------------------Measurement Edit Service-------------------------------#
public function updateMeasurementWithReqestArray($id, $request_array) {
        $entity = $this->repo->find($id);
        $measurement = $entity->getMeasurement();
        if ($measurement) {
            $measurement->setUpdatedAt(new \DateTime('now'));
            $measurement = $this->setMeasurmentObjectWithArray($measurement, $request_array);
            $entity->setMeasurement($measurement);
            $this->saveUser($entity);
            return array('Message' => 'success');
        } else {
            return array('Message' => 'Sorry We can not find measurment');
        }
    }
#---------------------------------Edit Shoulder/Outseam-----------------------#
 public function updateMarkingParamWithReqestArray($request, $request_array) {
        $email = $request_array['email'];
        $iphone_shoulder_height = $request_array['iphone_shoulder_height'];
        $iphone_outseam = $request_array['iphone_outseam'];
        $entity = $this->repo->findOneBy(array('email' => $email));// has to be change to getByEmail
        
        if (count($entity) > 0) {
            $userinfo = $this->fillUserArray($entity);
            $userinfo['authTokenWebService'] = $entity->getAuthToken();
            $userinfo['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $userinfo['id'] . "/";             

            $entity = $this->repo->find($userinfo['id']);
            $measurement = $entity->getMeasurement();// remove this when added to  
            
            if ($measurement) {
                $measurement->setUpdatedAt(new \DateTime('now'));
                if (isset($iphone_shoulder_height)) {
                    $measurement->setIphoneShoulderHeight($iphone_shoulder_height);
                }
                if (isset($iphone_outseam)) {
                    $measurement->setIphoneOutseam($iphone_outseam);
                }
                $entity->setMeasurement($measurement);
                $this->saveUser($entity);
            }                        
            $user_measurment = $this->fillMeasurementArray($measurement);            
            return  array_merge($userinfo, $user_measurment);
        } else {
            return array('Message' => 'Invalid Email');
        }
}
#---------------------Change Password Action-----------------------------------#  
public function changePasswordWithReqestArray($request_array) {
        if (isset($request_array['email'])) {$email = $request_array['email'];}
        if (isset($request_array['password'])){$password = $request_array['password'];}
        if (isset($request_array['old_password'])){$old_password = $request_array['old_password'];}
        /* $email='oldnavywomen0@ltf.com';
         $password='12';
         $old_password='123456'; */
        $entity = $this->repo->findOneBy(array('email' => $email));// this to replace with renamed method
        if (count($entity) > 0) {
            if ($this->matchPassword($entity, $old_password)) {                
                $password = $this->encodeThisPassword($entity, $password);
                $entity->setPassword($password);
                $this->saveUser($entity);
                return array('Message' => 'Paasword has been updated');
            } else {
                return array('Message' => 'Invalid Password');
            }
        } else {
            return array('Message' => 'Invalid Email');
        }
    }
#----------------- Getting the User Info ---------------------------------------#
private function fillUserArray($entity) {        
        $birth_date = $entity->getBirthDate();
        $userinfo = array();
        $userinfo['userId'] = $entity->getId();
        if($entity->getFirstName()){
        $userinfo['firstName'] = $entity->getFirstName();
        }else{
            $userinfo['firstName'] ='';
        }
        if($entity->getLastName()){
        $userinfo['lastName'] = $entity->getLastName();}
        else{
            $userinfo['lastName'] = '';
        }
        $userinfo['zipCode'] = $entity->getZipcode();
        $userinfo['gender'] = $entity->getGender();
        $userinfo['email'] = $entity->getEmail();
        if (isset($birth_date)) {
            $userinfo['birthDate'] = $birth_date->format('Y-m-d');
        }else{$userinfo['birthDate'] ='';}
        if($entity->getImage()){$userinfo['image'] = $entity->getImage();}else{$userinfo['image']='';}
        if($entity->getAvatar()){$userinfo['avatar'] = $entity->getAvatar();}else{ $userinfo['avatar']='';}
        if($entity->getIphoneImage()){$userinfo['iphoneImage'] = $entity->getIphoneImage();}else{$userinfo['iphoneImage']='';}
        if($entity->getDeviceUserPerInchPixelHeight()){$userinfo['heightPerInch']= $entity->getDeviceUserPerInchPixelHeight();}else{$userinfo['heightPerInch']='';}
        if($entity->getDeviceType()){$userinfo['postDeviceType']= $entity->getDeviceType();} else{$userinfo['postDeviceType']='';}
        if($entity->getDeviceType()){$userinfo['preDeviceType']= $entity->getDeviceType();}else{ $userinfo['preDeviceType']='';}
        return $userinfo;
    }
#------------------------------------------------------------------------------#
    public function fillMeasurementArray($measurement) {
        $userinfo = array();
        if ($measurement) {
           $userinfo['weight'] = $measurement->getWeight();
           // $userinfo['weight']= isset($measurement->getWeight())? $measurement->getWeight():0;
            $userinfo['height'] = $measurement->getHeight();
            $userinfo['waist'] = $measurement->getWaist();
            $userinfo['hip'] = $measurement->getHip();
            $userinfo['bust'] = $measurement->getBust();
            $userinfo['chest'] = $measurement->getChest();
            $userinfo['arm'] = $measurement->getChest();
            $userinfo['inseam'] = $measurement->getInseam();
            $userinfo['shoulderHeight'] = $measurement->getShoulderHeight();
            $userinfo['outseam'] = $measurement->getOutseam();
            $userinfo['sleeve'] = $measurement->getSleeve();
            $userinfo['neck'] = $measurement->getNeck();
           // $userinfo['back'] = $measurement->getBack();
           // $userinfo['iphone_shoulder_height'] = $measurement->getIphoneShoulderHeight();
           // $userinfo['iphone_outseam'] = $measurement->getIphoneOutseam();
            if($measurement->getBodyTypes()){
            $userinfo['bodyType'] = $measurement->getBodyTypes();
            }else{
                $userinfo['bodyType']='';
            }
            if($measurement->getBodyShape()){
            $userinfo['bodyShape'] = $measurement->getBodyShape();
            }else{
                $userinfo['bodyShape'] ='';}
                
            if($measurement->getBraSize()){
            $userinfo['braSize'] = $measurement->getBraSize();
            }else{
                $userinfo['braSize']='';
            }
            $userinfo['thigh'] = $measurement->getThigh();
            $userinfo['shoulderWidth'] = $measurement->getShoulderWidth();
            
            $userinfo['bustHeight'] = $measurement->getbustHeight();
            $userinfo['waistHeight'] = $measurement->getWaistHeight();
            $userinfo['hipHeight'] = $measurement->getHipHeight();
            
            $userinfo['bustWidth'] = $measurement->getBustWidth();
            $userinfo['waistWidth'] = $measurement->getWaistWidth();
            $userinfo['hipWidth'] = $measurement->getHipWidth();
            $userinfo['shoulderAcrossFront'] = $measurement->getShoulderAcrossFront();
            $userinfo['shoulderAcrossBack'] = $measurement->getShoulderAcrossBack();
            $userinfo['bicep'] = $measurement->getBicep();
            $userinfo['tricep'] = $measurement->getTricep();
            $userinfo['wrist'] = $measurement->getWrist();
            $userinfo['centerFrontWaist'] = $measurement->getCenterFrontWaist();
            $userinfo['backWaist'] = $measurement->getBackWaist();
            $userinfo['waistHip'] = $measurement->getWaistHip();
            $userinfo['knee'] = $measurement->getKnee();
            $userinfo['calf'] = $measurement->getCalf();
            $userinfo['ankle'] = $measurement->getAnkle();
            
            if($measurement->getTopBrand()){
            $userinfo['topBrandId'] = $measurement->getTopBrand()->getId();
            }else{
                $userinfo['topBrandId']=0;
            }
            if($measurement->getBottomBrand()){
            $userinfo['bottomBrandId'] = $measurement->getBottomBrand()->getId();
            }else{
                $userinfo['bottomBrandId']=0;
            }
            if($measurement->getDressBrand()){
            $userinfo['dressBrandId'] = $measurement->getDressBrand()->getId();
            }else{
                $userinfo['dressBrandId']=0;
            }
           
            if($measurement->getTopFittingSizeChart()){
            $userinfo['topFittingSizeChartId'] = $measurement->getTopFittingSizeChart()->getId();
            }else{
                $userinfo['topFittingSizeChartId']=0;
            }
            if($measurement->getBottomFittingSizeChart()){
            $userinfo['bottomFittingSizeChartId'] = $measurement->getBottomFittingSizeChart()->getId();}
            else{
                $userinfo['bottomFittingSizeChartId']=0;
            }
            if($measurement->getDressFittingSizeChart()){
            $userinfo['dressFittingSizeChartId'] =$measurement->getDressFittingSizeChart()->getId();
            }else{
                $userinfo['dressFittingSizeChartId']=0;
            }
    
        } 
         
        return $userinfo;
    
    }

#----------------------------Set User Array-------------------------------------#
private function setObjectWithArray($user, $request_array) {

        if (array_key_exists('email', $request_array)) {
            $user->setEmail($request_array['email']);
        }
        if (array_key_exists('gender', $request_array)) {
            $user->setGender($request_array['gender']);
        }
        if (array_key_exists('zipcode', $request_array)) {
            $user->setZipcode($request_array['zipcode']);
        }
        if (array_key_exists('firstName', $request_array)) {
            $user->setFirstName($request_array['firstName']);
        }
        if (array_key_exists('lastName', $request_array)) {
            $user->setLastName($request_array['lastName']);
        }
        if (array_key_exists('dob', $request_array)) {
            $user->setBirthDate(new \DateTime($request_array['dob']));
        }
       if (array_key_exists('deviceType', $request_array)) {
            $user->setDeviceType($request_array['deviceType']);
        } 

        return $user;
    }

#-----------------Set  Size Chart in Measurment---------------------------------#

    private function setSizechartInMeasurment($measurement, $request_array) {

        $sizeChartHelper = $this->container->get('admin.helper.sizechart');        
        
        //user teranary operator
        // $sc_top_id = isset($request_array['sc_top_id'])? $request_array['sc_top_id']:0;
        
        if (isset($request_array['sc_top_id'])) {
            $sc_top_id = $request_array['sc_top_id'];
        } else {
            $sc_top_id = 0;
        }
        if (isset($request_array['sc_bottom_id'])) {
            $sc_bottom_id = $request_array['sc_bottom_id'];
        } else {
            $sc_bottom_id = 0;
        }
        if (isset($request_array['sc_dress_id'])) {
            $sc_dress_id = $request_array['sc_dress_id'];
        } else {
            $sc_dress_id = 0;
        }
        if ($sc_top_id) {
            $top_size = $sizeChartHelper->findOneById($sc_top_id);
            $measurement->setTopFittingSizeChart($top_size); //
        }
        if ($sc_bottom_id) {
            $bottom_size = $sizeChartHelper->findOneById($sc_bottom_id);
            $measurement->setBottomFittingSizeChart($bottom_size); //
        }
        if ($sc_dress_id) {
            $dress_size = $sizeChartHelper->findOneById($sc_dress_id);
            $measurement->setDressFittingSizeChart($dress_size); //
        }
        return $measurement;
    }

#--------------------------SEt Measurment Array With Object------------------------------#

    private function setMeasurmentObjectWithArray($measurement, $request_array) {

        if (array_key_exists('weight', $request_array)) {
            $measurement->setWeight($request_array['weight']);
        }
        if (array_key_exists('height', $request_array)) {
            $measurement->setHeight($request_array['height']);
        }
        if (array_key_exists('waist', $request_array)) {
            $measurement->setWaist($request_array['waist']);
        }
        if (array_key_exists('hip', $request_array)) {
            $measurement->setHip($request_array['hip']);
        }
        if (array_key_exists('bust', $request_array)) {
            $measurement->setBust($request_array['bust']);
        }
        if (array_key_exists('inseam', $request_array)) {
            $measurement->setInseam($request_array['inseam']);
        }
        if (array_key_exists('chest', $request_array)) {
            $measurement->setChest($request_array['chest']);
        }
        if (array_key_exists('neck', $request_array)) {
            $measurement->setNeck($request_array['neck']);
        }
        if (array_key_exists('body_type', $request_array)) {
            $measurement->setBodyTypes($request_array['body_type']);
        }
        if (array_key_exists('bodyShape', $request_array)) {
            $measurement->setBodyShape($request_array['bodyShape']);
        }
        if (array_key_exists('braSize', $request_array)) {
            $measurement->setBraSize($request_array['braSize']);
        }
        return $measurement;
    }


}