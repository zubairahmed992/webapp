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




class UserHelper{

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
    
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }
 //---------------------------------------------------------------------   
    #!!!!!!! User Created at and Update At  ----------------!!!#
    public function createNewUser()
{
    $class = $this->class;
    $user = new $class();

    return $user;
}
//-------------------------------------------------------

public function saveUser(User $user)
{
    $this->em->persist($user);
    $this->em->flush();
    
}
//----------------------------------------------------------
public function updateProfile(User $user){
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
        $userEntity=$this->find($id);
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->container->get('security.context')->setToken($token);
        return $token->getUser();
    }
#----------------------------All Find Method -------------------------------------------------------------#    
 //-------------------------------------------------------

public function find($id)
{
    return $this->repo->findOneBy(array('id'=>$id));
}
//---------------------------------------------------------
public function findUserByEmail($email)
{
    return $this->repo->findOneBy(array('email'=>$email));
}
 #---------------------------START WEB SERVICES------- ----------------------------------------#
 
 //-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
    
    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
    }
#---------------------------------------------------------------#
    public function findOneBy($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }
#----------------------------------------------------------------#
    
#--------------------------End Of ALL Find Methods---------------------------------------------------------#    

//-------------------------------------------------------
// only use in website for security context in login
    public function getRegistrationSecurityContext($request) {
        // get the login error if there is one
        $session= $request->getSession();
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
    
//-------------------------------------------------------
public function encodePassword(User $user)
{
    $factory = $this->container->get('security.encoder_factory');
    $encoder = $factory->getEncoder($user);
    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
    return $password;
}

//-------------------------------------------------------
public function matchPassword(User $user, $password)
{
    $factory = $this->container->get('security.encoder_factory');
    $encoder = $factory->getEncoder($user);
    $password = $encoder->encodePassword($password, $user->getSalt());
    
     
    /*if($user->getPassword()==$password){
        //-----------------------
        //------------
    }*/
    return $password;
}

#-----------------------Save User at Registeration  ------------------------------------------------------------------------------#
public function registerUser(User $user){
    
    $user->setCreatedAt(new \DateTime('now'));
    $user->setUpdatedAt(new \DateTime('now'));
    $password=$this->encodePassword($user);
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


#-------------Edit/Update Profile for Web Services----------------#
    public function editProfileServiceHelper($decoded) {
        $email = $decoded['email'];

        if ($email) {
            $user = $this->repo->findOneBy(array('email' => $email));
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user = $this->setObjectWithArray($user, $decoded);
            $this->saveUser($user);
            return true;
        } else {
              return false;
       }
}
 #------------------------Returning Factory---------------------#


#---------------------------------------------Login Web Service -----------------------------------#
    public function loginWebService($request_array,$request) {
        
        $email = $request_array['email'];
        $password = $request_array['password'];
        /* $email ='abcdf@gmail.com';
          $password ='123456'; */

        $entity = $this->findOneBy($email);
            if (count($entity) > 0) {
                $user_db_password = $entity->getPassword();
                $password_old_enc = $this->matchPassword($entity, $password);
                if ($user_db_password == $password_old_enc) {
                    $userinfo = $this->gettingUserDetailArray($entity,$request);
                    return $userinfo;
                } else {
                    return array('Message' => 'Invalid Password');
                }
            } else {
                return array('Message' => 'Invalid Email');
            }
    }

#---------------------------------Web Service For Registration--------------------#
  public function RegistrationWebSerive(Request $request, $request_array)
  {
        $sizeChartHelper = $this->container->get('admin.helper.sizechart');
        $email = $request_array['email'];
        $password = $request_array['password'];
       
        if ($this->isDuplicateEmail(Null, $email)) {
            return array('Message' => 'The Email already exists');
        } else {
            
            $user = new User();
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $factory =$factory =$this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($password);
            $user=$this->setObjectWithArray($user,$request_array);
            $user->generateAuthenticationToken();
           
            $measurement = new Measurement();
    
    
            $measurement=$this->setSizechartInMeasurment($measurement, $request_array);
            $measurement->setUser($user);
            $measurement->setUpdatedAt(new \DateTime('now'));
            $measurement=$this->setMeasurmentObjectWithArray($measurement,$request_array);
            $user->setMeasurement($measurement);
            $this->saveUser($user);
    #---------------------------------------------------Getting Data-----------------------------#
             $userinfo = $this->gettingUserDetailArray($user,$request);
             
             /*$userinfo=array();
             $userinfo=$this->fillUserArray($user);
             $userinfo['authTokenWebService'] = $user->getAuthToken();
             $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$userinfo['id']."/";
             $userinfo['path']=$baseurl;
             $user_measurment=array();
             $user_measurment=$this->fillMeasurementArray($measurement);
             $userinfo=array_merge ($userinfo, $user_measurment);*/
             return $userinfo;
        }
        
        }
#------------------------------------------------Measurement Edit Service--------------------------------------------#
public function measurementEditWebService($id,$request_array){

        $entity = $this->repo->find($id);
        $measurement = $entity->getMeasurement();
        if ($measurement) {

            $measurement->setUpdatedAt(new \DateTime('now'));
            $measurement=$this->setMeasurmentObjectWithArray($measurement, $request_array);
            $entity->setMeasurement($measurement);
            $this->saveUser($entity);
            return array('Message' => 'success');
        } else {
            return array('Message' => 'Sorry We can not find measurment');
        }
    }
 #-----------------------------------------------Edit Shoulder/Outseam--------------------------------------------
 public function shoulderOutseamWebService($request,$request_array){
        
        $email = $request_array['email'];
        $iphone_shoulder_height = $request_array['iphone_shoulder_height'];
        $iphone_outseam = $request_array['iphone_outseam'];


        $entity = $this->repo->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $userinfo = array();
            $userinfo = $this->fillUserArray($entity);
            $userinfo['authTokenWebService'] = $entity->getAuthToken();
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $userinfo['id'] . "/";
            $userinfo['path'] = $baseurl;

            $entity = $this->repo->find($userinfo['id']);
            $measurement = $entity->getMeasurement();
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

            $user_measurment = array();
            $user_measurment = $this->fillMeasurementArray($measurement);
            $userinfo = array_merge($userinfo, $user_measurment);
            return $userinfo;
        } else {
            return array('Message' => 'Invalid Email');
        }
    }
    #---------------------Change Password Action-----------------------------------------------------#  

    public function webServiceChangePassword($request_array) {
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
           
        /*$email='oldnavywomen0@ltf.com';
        $password='12';
        $old_password='123456';*/
    $entity = $this->repo->findOneBy(array('email' => $email));
    if (count($entity) > 0) {

            $user_db_password = $entity->getPassword();
            //$salt_value_old = $entity->getSalt();
            //$factory = $this->container->get('security.encoder_factory');
            //$encoder = $factory->getEncoder($entity);
            $password_old_enc = $this->matchPassword($entity, $old_password);
          //  $password_old_enc = $encoder->encodePassword($old_password, $salt_value_old);
         if ($password_old_enc == $user_db_password) {
                $entity->setUpdatedAt(new \DateTime('now'));
                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($password,$entity->getSalt());
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
    
#-------- Getting the User Info ------------------------------------------------------------#
  private function fillUserArray($entity){
      
                $birth_date=$entity->getBirthDate();
                   $userinfo=array();
                   $userinfo['id']=$entity->getId();
                   $userinfo['first_name']=$entity->getFirstName();
                   $userinfo['last_name']=$entity->getLastName();
                   $userinfo['zipcode']=$entity->getZipcode();
                   $userinfo['gender']=$entity->getGender();
                   $userinfo['email'] = $entity->getEmail();
                  
                   if(isset($birth_date)){
                   $userinfo['birth_date']= $birth_date->format('Y-m-d');
                   }
                   
                   $userinfo['image']=$entity->getImage();
                   $userinfo['avatar']=$entity->getAvatar();
                   $userinfo['iphoneImage']=$entity->getIphoneImage();
                   return $userinfo;
  }
 public function fillMeasurementArray($measurement){
        
     $userinfo=array();
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
 
 private function setObjectWithArray($user,$request_array){
     
      if (array_key_exists('email',$request_array)){ $user->setEmail($request_array['email']);}
      if (array_key_exists('gender',$request_array)){ $user->setGender($request_array['gender']);}
      if (array_key_exists('zipcode',$request_array)){ $user->setZipcode($request_array['zipcode']);}
      if (array_key_exists('firstName',$request_array)){ $user->setFirstName($request_array['firstName']);}
      if (array_key_exists('lastName',$request_array)){ $user->setLastName($request_array['lastName']);}
      if (array_key_exists('dob',$request_array)){ $user->setBirthDate(new \DateTime($request_array['dob']));}
      
     return $user;    
 }
#-----------------Set  Size Chart in Measurment---------------------------------#
private function setSizechartInMeasurment($measurement,$request_array){
    
     $sizeChartHelper = $this->container->get('admin.helper.sizechart');
     $size_chart = new SizeChart();
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
    private  function setMeasurmentObjectWithArray($measurement,$request_array){
        
         if (array_key_exists('weight',$request_array)){ $measurement->setWeight($request_array['weight']);}
         if (array_key_exists('height',$request_array)){ $measurement->setHeight($request_array['height']);}
         if (array_key_exists('waist',$request_array)){ $measurement->setWaist($request_array['waist']);}
         if (array_key_exists('hip',$request_array)){ $measurement->setHip($request_array['hip']);}
         if (array_key_exists('bust',$request_array)){ $measurement->setBust($request_array['bust']);}
         if (array_key_exists('inseam',$request_array)){ $measurement->setInseam($request_array['inseam']);}
         if (array_key_exists('chest',$request_array)){ $measurement->setChest($request_array['chest']);}
         if (array_key_exists('neck',$request_array)){ $measurement->setNeck($request_array['neck']);}
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
  
  public function getArrayByEmail($email)
{
                  $entity= $this->repo->findOneBy(array('email'=>$email));
                   
                   $userinfo=array();
                   $userinfo=$this->fillUserArray($entity);
                   /*$birth_date=$entity->getBirthDate();
                   $userinfo['id']=$entity->getId();
                   $userinfo['email']=$email;
                   $userinfo['first_name']=$entity->getFirstName();
                   $userinfo['last_name']=$entity->getLastName();
                   $userinfo['zipcode']=$entity->getZipcode();
                   $userinfo['gender']=$entity->getGender();
                   if(isset($birth_date)){
                   $userinfo['birth_date']= $birth_date->format('Y-m-d');
                   }
                   
                   $userinfo['image']=$entity->getImage();
                   $userinfo['avatar']=$entity->getAvatar();*/
                 
    return  $userinfo;
}
#--------------------------------User Detail Array -----------------------------------#
    private function gettingUserDetailArray($entity,$request){
           
            $userinfo = array();
            $userinfo = $this->fillUserArray($entity);
            $entity = $this->repo->find($userinfo['id']);
            $measurement = $entity->getMeasurement();
            $user_measurment = array();
            $user_measurment = $this->fillMeasurementArray($measurement);
            $userinfo['path']=$request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$userinfo['id']."/";
             
            $userinfo['authTokenWebService'] = $entity->getAuthToken();
            $userinfo = array_merge($userinfo, $user_measurment);
            return $userinfo;
        
    }
#---------------------------------------------------End Of Detail Array----------------------------------------#

    //---------------ADMIN USER Controller Refractor Methods----------------
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
        return array('users'=>$entity,
			   'rec_count' => $rec_count, 
                           'no_of_pagination' => $no_of_paginations, 
                           'limit' => $cur_page, 
                           'per_page_limit' => $limit,                          
                           'femaleUsers'=>$this->getUserByGender('f'),
                           'maleUsers'=>$this->getUserByGender('m'),
        );
    }
    
    //--------------------------Get User BY Gender
    
    private function getUserByGender($gender)
    {
        $rec_count =count($this->repo->findUserByGender($gender));
        return $rec_count;        
    }

    public function getUsersListById($id)
    {
        $entity=$this->repo->findOneBy(array('id'=>$id));        
        return $entity;
    }
    
    
    public function findWithSpecs($id) {
        $entity = $this->repo->findOneBy(array('id'=>$id));
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

    public function getUserBirthDate($age)
    {
               $agedate = new \DateTime();
               $agedate->sub(new \DateInterval("P" .$age. "Y"));
               return $agedate->format("Y-m-d");
    }
    
    public function getUserByAge($beginDate,$endDate)
    {        
        $entity = $this->repo->findUserByAge($beginDate,$endDate);
        return $entity;
    }
    
    public function getUserSearchListByGender($gender)
    {
        $entity = $this->repo->findUserSearchListByGender($gender);
        return $entity;        
    }
    
    public function getUserSearchListByName($firstname,$lastname)
    {
        $entity = $this->repo->findUserSearchListByName($firstname,$lastname);
        return $entity;         
    }
    
    public function getUserSearchList($firstname,$lastname,$gender)
    {
        $entity = $this->repo->findUserSearchListBy($firstname,$lastname,$gender);
        return $entity;              
    }
    
    public function getUserSearchLists($firstname,$lastname,$gender,$beginDate,$endDate)
    {
        $entity = $this->repo->findUserSearchListsBy($firstname,$lastname,$gender,$beginDate,$endDate);
        return $entity;
    }
}