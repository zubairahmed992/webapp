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

class SelfieshareFeedbackHelper {

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
     public function createNew($selfieshare=null){
       $class = $this->class;
       $ssfeedback = new $class();
       $ssfeedback->setSelfieshare($selfieshare);       
       $ssfeedback->setUpdatedAt(new \DateTime('now'));    
       $ssfeedback->setRef(uniqid());    
       return $ssfeedback;
     }
  
      #----------------------------------------------------------------------------
     public function createWithArray($friends, $selfieshare) {        
         foreach ($friends as $k => $v) {             
             $ssfb =  $this->setWithParam($v, $this->createNew($selfieshare));        
             $this->save($ssfb);           
         }
    }  
   #----------------------------------------------------------------------------
     public function createWithParam($ra, $selfieshare) {
        $ssfb =  $this->setWithParam($ra, $this->createNew($selfieshare));
        $this->save($ssfb);           
    }  
    
    #----------------------------------------------------------------------------
     private function setWithParam($ra, $ss) {
        if(array_key_exists('comments', $ra) && $ra['comments']){$ss->setComments($ra['comments']);}
        if(array_key_exists('rating', $ra) && $ra['rating']){$ss->setRating($ra['rating']);}
        if(array_key_exists('favourite', $ra)){
            if($ra["favourite"]==1){
                $ss->setFavourite(1);
            }else{
                $ss->setFavourite(0);
            }
            //$ss->setFavourite($ra['favourite']);
        }
        if(array_key_exists('name', $ra) && $ra['name']){$ss->setName($ra['name']);}
        if(array_key_exists('email', $ra) && $ra['email']){$ss->setEmail($ra['email']);}
        if(array_key_exists('phone', $ra) && $ra['phone']){$ss->setPhone($ra['phone']);}
        if(array_key_exists('ref', $ra) && $ra['ref']){$ss->setRef($ra['ref']);}
        return $ss;
    }  
    #----------------------------------------------------------------------------
    public function save($ss) {
        $ss->setUpdatedAt(new \DateTime('now'));    
        $this->em->persist($ss);
        $this->em->flush();      
    }
       
   
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    }

    #----------------------------
    public function findByRef($ref) {
        return $this->repo->findByRef(array('ref' => $ref));
    }
    
}
    
?>