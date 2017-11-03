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
use LoveThatFit\UserBundle\Entity\MaskMarker;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Request;

class InviteFriendHelper {

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
   #----------------------------------------------------------------------------
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
       #----------------------------------------------------------------------------
     public function createNew($user=null){
       $class = $this->class;
       $invitefriend = new $class();
       $invitefriend->setUser($user);
       $invitefriend->setCreatedAt(new \DateTime('now'));
       return $invitefriend;
     }
     
   #----------------------------------------------------------------------------
     public function createWithParam($ra, $user) {
        $invitefriend=  $this->createNew($user);
        if(array_key_exists('email', $ra) && $ra['email']){$invitefriend->setEmail($ra['email']);}
        if(array_key_exists('friend_name', $ra) && $ra['friend_name']){$invitefriend->setFriendName($ra['friend_name']);}
        if(array_key_exists('friend_email', $ra) && $ra['friend_email']){$invitefriend->setFriendEmail($ra['friend_email']);}
        $this->em->persist($invitefriend);
        $this->em->flush();    
        return $invitefriend;
    }  


     public function updateWithParam($ra, $user) {
        $invitefriend=  $this->UpdateFriendEmail(array('user'=>$user->getId(),'friend_email' => $ra['friend_email']));
         
         $invitefriend->setCreatedAt(new \DateTime('now'));
        if(array_key_exists('email', $ra) && $ra['email']){$invitefriend->setEmail($ra['email']);}
        if(array_key_exists('friend_name', $ra) && $ra['friend_name']){$invitefriend->setFriendName($ra['friend_name']);}
        if(array_key_exists('friend_email', $ra) && $ra['friend_email']){$invitefriend->setFriendEmail($ra['friend_email']);}
        $this->em->persist($invitefriend);
        $this->em->flush();    
        return $invitefriend;
    }  


    #----------------------------------------------------------------------------
     public function createmaleFriendWithParam($ra) {
        $invitefriend=  $this->createNew();        
        if(array_key_exists('email', $ra) && $ra['email']){$invitefriend->setEmail($ra['email']);}
        if(array_key_exists('friend_name', $ra) && $ra['friend_name']){$invitefriend->setFriendName($ra['friend_name']);}
        if(array_key_exists('friend_email', $ra) && $ra['friend_email']){$invitefriend->setFriendEmail($ra['friend_email']);}
        $this->em->persist($invitefriend);
        $this->em->flush();    
        return $invitefriend;
    }  

    #----------------------------------------------------------------------------
    public function save($invitefriend) {
        $this->em->persist($invitefriend);
        $this->em->flush();      
    }
       
   
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    } 

    public function UpdateFriendEmail($arr) {
        return $this->repo->findOneBy($arr);
    } 
    #----------------------------
    

    public function findByFriendEmail($friend_email,$user_id) {
        return $this->repo->findOneBy(array('friend_email' => $friend_email, 'user' => $user_id));
    } 
    
}
    
?>