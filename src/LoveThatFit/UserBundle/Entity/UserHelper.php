<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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

//-------------------------------------------------------

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


//-------------------------------------------------------

public function find($id)
{
    return $this->repo->findOneBy(array('id'=>$id));
}
 #---------------------------START WEB SERVICES------- ----------------------------------------#
public function findByEmail($email)
{
                  $entity= $this->repo->findOneBy(array('email'=>$email));
                   $birth_date=$entity->getBirthDate();
                   $userinfo=array();
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
                   $userinfo['avatar']=$entity->getAvatar();
                 
    return  $userinfo;
}
#-------------Edit/Update Profile for Web Services----------------#
    public function editProfileServiceHelper($decoded) {
        $email = $decoded['email'];
      
        /*$first_name = $decoded['firstName'];
        $last_name = $decoded['lastName'];
        $birth_date = $decoded['dob'];
        $zipcode = $decoded['zip'];*/
       
        
        if ($email) {

            $user = $this->repo->findOneBy(array('email' => $email));
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));

            if (isset($decoded['firstName'])) {
                $user->setFirstName($decoded['firstName']);
            }
            if (isset($decoded['lastName'])) {
                $user->setLastName($decoded['lastName']);
            }
            if (isset($decoded['dob'])) {
                $user->setBirthDate(new \DateTime($decoded['dob']));
            }
            if (isset($decoded['zip'])) {
                $user->setZipcode($decoded['zip']);
            }
            $this->saveUser($user);
            return true;
        } else {

            return false;
        }
        
    }
 #------------------------Returning Factory---------------------#


#---------------------------------------------Login Web Service -----------------------------------#
    public function loginWebService($entity,$password,$email){
          //$request = $this->getRequest();
                $authTokenWebService=$entity->getAuthToken();
                $user_db_password = $entity->getPassword();
                $salt_value_db = $entity->getSalt();

                $factory =$this->container->get('security.encoder_factory');
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
                    $iphoneImage=$entity->getIphoneImage();
                    
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
                 $userinfo['iphoneImage']=$iphoneImage;
                 
                $entity = $this->repo->find($user_id);
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
                } else {
                     return array('Message'=>'Invalid Password');
                }
            
        
        
    }
    
#---------------------------------Web Service For Registration--------------------#
  public function RegistrationWebSerive(Request $request, $request_array)
  {
//$request = $this->getRequest();
        $sizeChartHelper = $this->container->get('admin.helper.sizechart');
        $email = $request_array['email'];
        $password = $request_array['password'];
        $gender = $request_array['gender'];
        $zipcode = $request_array['zipcode'];

        /* $email ='my_web115115@gmail.com';
         $password ='123456';
         $gender = 'f';
         $zipcode = '123'; 
        */
        
  #-----------------End of Measuremnt data-----------------------# 
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
             $user->setEmail($email);
             $user->setGender($gender);
             $user->setZipcode($zipcode);
            $user->generateAuthenticationToken();
            //  $this->saveUser($user);

    
    
         //  $user= $this->saveUser($user);
            //$user = $this->registerUser($user);

            #----------------------Set Data of Measuremnt -------------------------------#
           // $measurement = $user->getMeasurement();
            $measurement = new Measurement();
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



            $measurement->setUser($user);
            $measurement->setUpdatedAt(new \DateTime('now'));

          
            if (isset($request_array['weight'])) {
                $measurement->setWeight($request_array['weight']);
            

            if (isset($request_array['height'])) {
                $measurement->setHeight($request_array['height']);
            }
            if (isset($request_array['waist'])) {
                $measurement->setWaist($request_array['waist']);
            }
            if (isset($request_array['hip'])) {
                $measurement->setHip($request_array['hip']);
            }
            if (isset($request_array['bust'])) {
                $measurement->setBust($request_array['bust']);
            }


            if (isset($request_array['inseam'])) {
                $measurement->setInseam($request_array['inseam']);
            }

            if (isset($request_array['chest'])) {
                $measurement->setChest($request_array['chest']);
            }

            if (isset($request_array['neck'])) {
                $measurement->setNeck($request_array['neck']);
            }


//       $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
            $user->setMeasurement($measurement);
            $this->saveUser($user);

            //       $this->saveUser($user);
#---------------------------------------------------Getting Data-----------------------------#
            $userinfo = array();
            #--------------------User Info-------------------------------#

            $birth_date = $user->getBirthDate();

            $userinfo['id'] = $user->getId();
            $userinfo['email'] = $user->getEmail();
            $userinfo['first_name'] = $user->getFirstName();
            $userinfo['last_name'] = $user->getLastName();
            $userinfo['zipcode'] = $user->getZipcode();
            $userinfo['gender'] = $user->getGender();
            

            if (isset($birth_date)) {
                $userinfo['birth_date'] = $birth_date->format('Y-m-d');
            }

            $userinfo['image'] = $user->getImage();
            $userinfo['avatar'] = $user->getAvatar();
            $userinfo['authTokenWebService'] = $user->getAuthToken();
             $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$userinfo['id']."/";
             $userinfo['path']=$baseurl;
            #-----------------------Measurement Info--------------------#
            $userinfo['weight'] = $measurement->getWeight();
            $userinfo['height'] = $measurement->getHeight();
            $userinfo['waist'] = $measurement->getWaist();
            $userinfo['hip'] = $measurement->getHip();
            $userinfo['bust'] = $measurement->getBust();
            $userinfo['inseam'] = $measurement->getInseam();
            $userinfo['chest'] = $measurement->getChest();
            $userinfo['sleeve'] = $measurement->getSleeve();
            $userinfo['neck'] = $measurement->getNeck();
            $userinfo['back'] = $measurement->getBack();
            if (!$userinfo['back']) {
                $userinfo['back'] = 15.5;
            }
            
         
            
            #------------------------End of Seting measuremt----------#          
            return $userinfo;
        }
        }
        
        }
#------------------------------------------------Measurement Edit Service--------------------------------------------#
public function measurementEditWebService($id,$request_array){

        $entity = $this->repo->find($id);
        $measurement = $entity->getMeasurement();
        if ($measurement) {

            $measurement->setUpdatedAt(new \DateTime('now'));

            if (isset($request_array['weight'])) {
                $measurement->setWeight($request_array['weight']);
            }
            if (isset($request_array['height'])) {
                $measurement->setHeight($request_array['height']);
            }
            if (isset($request_array['waist'])) {
                $measurement->setWaist($request_array['waist']);
            }
            if (isset($request_array['hip'])) {
                $measurement->setHip($request_array['hip']);
            }
            if (isset($request_array['bust'])) {
                $measurement->setBust($request_array['bust']);
            }
            if (isset($request_array['neck'])) {
                $measurement->setNeck($request_array['neck']);
            }
            if (isset($request_array['inseam'])) {
                $measurement->setInseam($request_array['inseam']);
            }
            if (isset($request_array['chest'])) {
                $measurement->setChest($request_array['chest']);
            }

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
            $userinfo['iphoneImage']=$entity->getIphoneImage();
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user_id . "/";
            $userinfo['path'] = $baseurl;

            $entity = $this->repo->find($user_id);
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

            return $userinfo;
        } else {
            return array('Message' => 'Invalid Email');
        }
     
 }
    #---------------------Change Password Action-----------------------------------------------------#  

    public function webServiceChangePassword($request_array) {

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
        $password='123456';
        $old_password='123456';*/

        $entity = $this->repo->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $user_db_password = $entity->getPassword();
            $salt_value_old = $entity->getSalt();
            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);

            $password_old_enc = $encoder->encodePassword($old_password, $salt_value_old);


            if ($password_old_enc == $user_db_password) {

                $entity->setUpdatedAt(new \DateTime('now'));
                $password = $encoder->encodePassword($password, $salt_value_old);
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
    
    
    
    
    
    //-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
    
    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
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

}