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

class SelfieshareHelper {

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
       $selfieshare = new $class();
       $selfieshare->setUser($user);       
       $selfieshare->setCreatedAt(new \DateTime('now'));    
       return $selfieshare;
     }
     
   #----------------------------------------------------------------------------
     public function createWithParam($ra, $user) {
        $selfieshare=  $this->createNew($user);
        if(array_key_exists('device_type', $ra) && $ra['device_type']){$selfieshare->setDeviceType($ra['device_type']);}
        if(array_key_exists('image', $ra) && $ra['image']){$selfieshare->setImage($ra['image']);}  
        $selfieshare->file=$_FILES["image"];
        $selfieshare->upload();
        $this->save($selfieshare);          
        $this->container->get('user.selfiesharefeedback.helper')->createWithArray($ra['friends'], $selfieshare);
    }  
    
    #----------------------------------------------------------------------------
    public function save($selfieshare) {
        $this->em->persist($selfieshare);
        $this->em->flush();      
    }
       
   
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    } 
    
    
}
    
?>