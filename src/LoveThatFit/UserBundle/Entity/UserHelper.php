<?php

namespace LoveThatFit\UserBundle\Entity;

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
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
    //$this->dispatcher->dispatch('foo_bundle.post.comment_added', new CommentEvent($post, $comment));
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

    private function isDuplicateEmail($id, $email) {
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

     public function getToken($email) {
    
            $entity= $this->repo->findOneBy(array('email'=>$email));
            if (count($entity) > 0) {
            return $entity->getAuthTokenWebService();
            }
            
             
        
    }

}