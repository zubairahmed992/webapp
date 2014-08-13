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

class UserMarkerHelper {

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
    
     public function createNew()
     {
       $class = $this->class;
       $maskMarker = new $class();
       return $maskMarker;
     }
     
     
     public function saveUserMarker($user,$usermarker) {
        //$class = $this->class;
       // $usermarker = new $class();
        $usermarker->setCreatedAt(new \DateTime('now'));    
        $usermarker->setUpdatedAt(new \DateTime('now'));            
        $usermarker->setUser($user);       
        $this->em->persist($usermarker);
        $this->em->flush();      
    }  
    
    public function updateUserMarker($user,$usermarker)
    {
        $usermarker->setCreatedAt(new \DateTime('now'));    
        $usermarker->setUpdatedAt(new \DateTime('now'));            
        $usermarker->setUser($user);               
        $this->em->persist($usermarker);
        $this->em->flush();       
    }
    public function setArray($specs,$user_marker){
        if($specs['svg_path']){$user_marker->setSvgPaths($specs['svg_path']);}
        if($specs['rect_x']){$user_marker->setRectX($specs['rect_x']);}
        if($specs['rect_y']){$user_marker->setRectY($specs['rect_y']);}
        if($specs['rect_height']){$user_marker->setRectHeight($specs['rect_height']);}
        if($specs['rect_width']){$user_marker->setRectWidth($specs['rect_width']);}
    }
   public function getArray($user_marker) {
       $specs['svg_path']=$user_marker->getSvgPaths();
       $specs['rect_x']=$user_marker->getRectX();
       $specs['rect_y']=$user_marker->getRectY();
       $specs['rect_height']=$user_marker->getRectHeight();
       $specs['rect_width']=$user_marker->getRectWidth();
        return ($specs);
   }
   public function fillMarker($user,$usermaker){
        $maskMarker=$this->findMarkerByUser($user);
      
        if(count($maskMarker)>0){
            $this->setArray($usermaker,$maskMarker);
            $this->updateUserMarker($user,$maskMarker);
            return 'updated';
        }else{  
           $maskMarker=$this->createNew();
           $this->setArray($usermaker,$maskMarker);
           $this->saveUserMarker($user,$maskMarker);
            return 'added';
        }
   }
   
   
    public function findMarkerByUser($user)
    {
        return $this->repo->findMarkerByUser($user);
    }


    
    public function findByUser($user)
    {
     return $this->repo->findByUser($user);
    }
    
    
    public function find($id) {
        return $this->repo->find($id);
    } 
    
    
    private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
   
     
    
}
    
?>