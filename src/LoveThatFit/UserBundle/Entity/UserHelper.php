<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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

public function encodePassword(User $user)
{
    $factory = $this->container->get('security.encoder_factory');
    $encoder = $factory->getEncoder($user);
    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
    return $password;
}
#------------------------Returning Factory---------------------#
public function factoryReturn()
{
    $factory = $this->container->get('security.encoder_factory');
    return $factory;
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
    return $this->repo->find($id);
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
        $first_name = $decoded['firstName'];
        $last_name = $decoded['lastName'];
        $birth_date = $decoded['dob'];
        $zipcode = $decoded['zip'];
       
        
        if ($email) {

            $user = $this->repo->findOneBy(array('email' => $email));
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));

            if (isset($first_name)) {
                $user->setFirstName($first_name);
            }
            if (isset($last_name)) {
                $user->setLastName($last_name);
            }
            if (isset($birth_date)) {
                $user->setBirthDate(new \DateTime($birth_date));
            }
            if (isset($zipcode)) {
                $user->setZipcode($zipcode);
            }
            $this->saveUser($user);
            return true;
        } else {

            return false;
        }
        
    }
#---------------------------------------------Login Web Service -----------------------------------#
    public function loginWebService($entity,$password,$email){
          //$request = $this->getRequest();
                $authTokenWebService=$entity->getAuthToken();
                $user_db_password = $entity->getPassword();
                $salt_value_db = $entity->getSalt();

                $factory = $this->factoryReturn();
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
                        $userinfo['iphone_outseam'] = 400;
                    }
                     return $userinfo;
                } else {
                     return array('Message'=>'Invalid Password');
                }
            
        
        
    }
    
#---------------------------------Web Service For Registration--------------------#
  public function registration($request_array)
  {
      
        $email = $request_array['email'];
        $password = $request_array['password'];
        $gender = $request_array['gender'];
        $zipcode = $request_array['zipcode'];
       if ($this->isDuplicateEmail(Null, $email)) {
          return false;
         }
       else{
                
            $user = new User();
             $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
           //$factory = $this->get('security.encoder_factory');
           //$encoder = $factory->getEncoder($user);
          //  $password = $encoder->encodePassword($password, $user->getSalt());

            $user->setPassword($password);
            $user->setEmail($email);
            $user->setGender($gender);
            $user->setZipcode($zipcode);
            $this->saveUser($user);
            $userinfo=array();
            $userinfo['email']=$user->getEmail();
            $userinfo['gender']=$user->getGender();
            $userinfo['zipcode']=$user->getZipcode();
           return $userinfo;
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
    
            $entity= $this->repo->findOneBy(array('authToken'=>$token));
            if (count($entity) > 0) {
            return array('status'=>True,'Message'=>'Authentication Success');
            }else{
                return array('status'=>False,'Message'=>'Authentication Failure');
            }
        
    }

}