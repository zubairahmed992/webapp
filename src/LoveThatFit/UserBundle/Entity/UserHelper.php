<?php

namespace LoveThatFit\UserBundle\Entity;

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

class UserHelper {

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

//----------------------------------------------------------
    public function updateProfile(User $user) {
        $user->uploadAvatar();
        $this->saveUser($user);
    }

//-------------------------------------------------------

    public function getLoggedIn(User $userEntity) {
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->container->get('security.context')->setToken($token);
        return $token->getUser();
    }

//-------------------------------------------------------

    public function getLoggedInById($id) {
        $userEntity = $this->find($id);
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->container->get('security.context')->setToken($token);
        return $token->getUser();
    }

#----------------------------All Find Method -------------------------------------------------------------#    

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

    #---------------------------------------------------------------#

    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
    }

#---------------------------------------------------------------#
    //have to be removed

    public function findOneBy($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }

    #---------------------------------------------------------------    

    public function findByGender($gender) {
        return  $this->repo->findUserByGender($gender);        
    }

    
    
    #---------------------------------------------------------------

    public function findWithSpecs($id) {
        $entity = $this->repo->findOneBy(array('id' => $id));
        if (!$entity) {
            $entity = $this->createNewUser();
            return array(
                'entity' => $entity,
                'message' => 'User not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'User found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }

#---------------------------------------------------------------

    public function getUserBirthDate($age) {
        $agedate = new \DateTime();
        $agedate->sub(new \DateInterval("P" . $age . "Y"));
        return $agedate->format("Y-m-d");
    }

    #---------------------------------------------------------------

    public function findByBirthDateRange($beginDate, $endDate) {
        return $this->repo->findUserByAge($beginDate, $endDate);        
    }

    #---------------------------------------------------------------

    public function findByName($firstname, $lastname) {
        return $this->repo->findByName($firstname, $lastname);        
    }

    #---------------------------------------------------------------

    public function findByGenderName($firstname, $lastname, $gender) {
        return $this->repo->findByGenderName($firstname, $lastname, $gender);        
    }

    #---------------------------------------------------------------

    public function findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate) {
        return $this->repo->findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate);        
    }

    private function countByGender($gender) {
        return  count($this->repo->findUserByGender($gender));        
    }
   #---------------------------------------------------------------------------#
    public function findByAuthToken($auth_token){
        return $this->repo->loadUserByAuthToken($auth_token);
    }
    
    #---------------------------------------------------------------
// only use in website for security context in login
    public function getRegistrationSecurityContext($request) {        
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return array('last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        );
    }

//------------- Password encoding ------------------------------------------
    public function encodePassword(User $user) {
        return $this->encodeThisPassword($user, $user->getPassword());
    }

//-------------------------------------------------------
    private function encodeThisPassword(User $user, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        return $password;
    }

//-------------------------------------------------------
    public function matchPassword(User $user, $password) {
        $password = $this->encodeThisPassword($user, $password);
        if ($user->getPassword() == $password) {
            return true;
        }
        return false;
    }
    
//-------------------------------------------------------
    public function resetPassword($user, $request_array) {
        $oldPassword=$request_array->getOldpassword();
        $oldEncodedPassword = $this->encodeThisPassword($user, $oldPassword);
        $_user =  $this->find(1);
        return array('status' => true, 'header' => 'tester', 'message' => $oldEncodedPassword.' >~~> '.$_user->getPassword(), 'entity' => $user);
        if ($oldEncodedPassword == $user->getPassword()) {
            $user->getPassword($this->encodeThisPassword($user, $newPassword));
            $this->saveUser($user);
            return array('status' => true, 'header' => 'Success', 'message' => 'Password has been successfully changed', 'entity' => $user);
        } else {
            return array('status' => false, 'header' => 'Warning', 'message' => 'Old password Invalid', 'entity' => $user);
        }
    }
    //-------------------------------------------------------------------
    public function _resetPassword($user, $oldPassword, $newPassword) {
        $oldEncodedPassword = $this->encodeThisPassword($user, $oldPassword);
        return array('status' => true, 'header' => 'tester', 'message' => $oldEncodedPassword.' ~> '.$user->getPassword(), 'entity' => $user);
        if ($oldEncodedPassword == $user->getPassword()) {
            $user->getPassword($this->encodeThisPassword($user, $newPassword));
            $this->saveUser($user);
            return array('status' => true, 'header' => 'Success', 'message' => 'Password has been successfully changed', 'entity' => $user);
        } else {
            return array('status' => false, 'header' => 'Warning', 'message' => 'Old password Invalid', 'entity' => $user);
        }
    }

    #-------------------------Web Service For Email Checking--------------------------------------#

    public function emailCheck($email) {
        if ($this->isDuplicateEmail(Null, $email) == false) {
            return array('Message' => 'Valid Email');
        } else {
            return array('Message' => 'The Email already exists');
        }
    }
    #----------------------------------------------------------------------------------------------#

    public function isDuplicateEmail($id, $email) {
        return $this->repo->isDuplicateEmail($id, $email);
    }
    #------------------------Chek Token ------------------------#

    public function authenticateToken($token) {
        $entity = $this->repo->findOneBy(array('authToken' => $token));
        if (count($entity) > 0) {
            return array('status' => True, 'Message' => 'Authentication Success');
        } else {
            return array('status' => False, 'Message' => 'Authentication Failure');
        }
    }

#-----------------------Save User at Registeration  ------------------------------------------------------------------------------#

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

#-----------------------Get User Measurment ------------------------------------------#

    public function getMeasurement($user) {
        $measurement = $user->getMeasurement();
        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        return $measurement;
    }

//----------------------------------------------------------------------
    public function getArrayByEmail($email) {//getUserArrayByEmail
        $entity = $this->repo->findOneBy(array('email' => $email));
        $userinfo = array();
        $userinfo = $this->fillUserArray($entity);
        return $userinfo;
    }

#--------------------------------User Detail Array -----------------------------------#

    private function gettingUserDetailArray($entity, $request) {
        // change name getUserDetailArrayWithRequestArray
        
        $userinfo = $this->fillUserArray($entity);
        $entity = $this->repo->find($userinfo['id']);
        $measurement = $entity->getMeasurement();        
        $user_measurment = $this->fillMeasurementArray($measurement);
        $userinfo['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $userinfo['id'] . "/";
        $userinfo['authTokenWebService'] = $entity->getAuthToken();        
        return array_merge($userinfo, $user_measurment);
    }

#-------------Edit/Update Profile for Web Services----------------#
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

#---------------------------------------------Login Web Service -----------------------------------#


    public function loginWebService($request_array, $request) {
        $email = $request_array['email'];
        $password = $request_array['password'];
        $deviceType=$request_array['deviceType'];
        /*$email ='oldnavywomen0@ltf.com';
        $password ='123456'; 
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

#---------------------------------Web Service For Registration--------------------#

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

#------------------------------------------------Measurement Edit Service--------------------------------------------#

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

    #-----------------------------------------------Edit Shoulder/Outseam--------------------------------------------

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

    #---------------------Change Password Action-----------------------------------------------------#  

    public function changePasswordWithReqestArray($request_array) {
        
//-------break functionality into further methods

        if (isset($request_array['email'])) {
            $email = $request_array['email'];
        }
        if (isset($request_array['password'])) {
            $password = $request_array['password'];
        }
        if (isset($request_array['old_password'])) {
            $old_password = $request_array['old_password'];
        }

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

#-------- Getting the User Info ------------------------------------------------------------#

    private function fillUserArray($entity) {        
        $birth_date = $entity->getBirthDate();
        $userinfo = array();
        $userinfo['id'] = $entity->getId();
        $userinfo['first_name'] = $entity->getFirstName();
        $userinfo['last_name'] = $entity->getLastName();
        $userinfo['zipcode'] = $entity->getZipcode();
        $userinfo['gender'] = $entity->getGender();
        $userinfo['email'] = $entity->getEmail();
        if (isset($birth_date)) {
            $userinfo['birth_date'] = $birth_date->format('Y-m-d');
        }
        $userinfo['image'] = $entity->getImage();
        $userinfo['avatar'] = $entity->getAvatar();
        $userinfo['iphoneImage'] = $entity->getIphoneImage();
        $userinfo['heightPerInch']= $entity->getDeviceUserPerInchPixelHeight();
        $userinfo['postDeviceType']= $entity->getDeviceType();
        $userinfo['preDeviceType']= $entity->getDeviceType();
        return $userinfo;
    }
#------------------------------------------------------------------------------------
    public function fillMeasurementArray($measurement) {
        
        $userinfo = array();
        if ($measurement) {
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
        } else {
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
        if (!$userinfo['back']) {
            $userinfo['back'] = 15.5;
        }
        if (!$userinfo['iphone_shoulder_height']) {
            $userinfo['iphone_shoulder_height'] = 150;
        }
        if (!$userinfo['iphone_outseam']) {
            $userinfo['iphone_outseam'] = 260;
        }
        return $userinfo;
    }

#------------------Set User Array----------------------------------------------#

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
        return $measurement;
    }

    #------------------------user Image upload ------------------------#

    public function uploadFittingRoomImage($entity) {
        $image_path = "";

        if ($entity->getImage()) {
            $image_path = $entity->uploadTempImage();
        } else {
            $entity->upload();
            $this->saveUser($entity);
            $image_path = $entity->getWebPath();
        }
        return $image_path;
    }

//---------------Pagination List Method---------------------------------
    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];
        $entity = $this->repo->findAllUsers($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllUserRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('users' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'femaleUsers' => $this->countByGender('f'),
            'maleUsers' => $this->countByGender('m'),
        );
    }

}