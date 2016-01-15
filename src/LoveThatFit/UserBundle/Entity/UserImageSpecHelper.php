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

class UserImageSpecHelper {

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
       $image_spec = new $class();
       $image_spec->setUser($user);       
       $image_spec->setCreatedAt(new \DateTime('now'));    
       $image_spec->setUpdatedAt(new \DateTime('now'));            
        
       return $image_spec;
     }
     
    #----------------------------------------------------------------------------
     public function saveNew($user,$image_spec) {
        $image_spec->setCreatedAt(new \DateTime('now'));    
        $image_spec->setUpdatedAt(new \DateTime('now'));            
        $image_spec->setUser($user);       
        $this->save($image_spec);
    }  
    #----------------------------------------------------------------------------
     public function updateWithParam($spec_array, $user) {
         $specs_obj = $user->getUserImageSpec();
         if (!$specs_obj){
             $specs_obj = $this->createNew($user);
         }
         $this->setArray($spec_array, $specs_obj);
         $this->save($specs_obj);
    }  
    #----------------------------------------------------------------------------
    public function save($image_spec) {
        $image_spec->setUpdatedAt(new \DateTime('now'));            
        $this->em->persist($image_spec);
        $this->em->flush();      
    }
       
    #----------------------------------------------------------------------------
    public function setArray($specs_array,$specs_obj){
        if(is_array($specs_array)){
        if(array_key_exists('camera_angle', $specs_array) && $specs_array['camera_angle']){$specs_obj->setCameraAngle($specs_array['camera_angle']);}
        if(array_key_exists('camera_x', $specs_array) && $specs_array['camera_x']){$specs_obj->setCameraX($specs_array['camera_x']);}
        if(array_key_exists('displacement_x', $specs_array) && $specs_array['displacement_x']){$specs_obj->setDisplacementX($specs_array['displacement_x']);}
        if(array_key_exists('displacement_y', $specs_array) && $specs_array['displacement_y']){$specs_obj->setDisplacementY($specs_array['displacement_y']);}
        if(array_key_exists('rotation', $specs_array) && $specs_array['rotation']){$specs_obj->setRotation($specs_array['rotation']);}
        #if from the mask marker js
        if(array_key_exists('move_up_down', $specs_array) && $specs_array['move_up_down']){$specs_obj->setDisplacementY($specs_array['move_up_down']);}
        if(array_key_exists('move_left_right', $specs_array) && $specs_array['move_left_right']){$specs_obj->setDisplacementX($specs_array['move_left_right']);}
        if(array_key_exists('img_rotate', $specs_array) && $specs_array['img_rotate']){$specs_obj->setRotation($specs_array['img_rotate']);}
        }
        return $specs_obj;
        }
       #----------------------------------------------------------------------------
   public function getArray($image_spec) {
       $specs_array['camera_angle']=$image_spec->getCameraAngle();
       $specs_array['camera_x']=$image_spec->getCameraX();
       $specs_array['displacement_x']=$image_spec->getDisplacementX();
       $specs_array['displacement_y']=$image_spec->getDisplacementY();
       $specs_array['rotation']=$image_spec->getRotation();
       return ($specs_array);
   }
    
    public function findByUser($user){
     return $this->repo->findByUser($user);
    }
    #----------------------------------------------------------------------------
    public function getByUser($user){
       $image_spec = $this->repo->findByUser($user);
       if ($image_spec){
           return $image_spec;
       }else{
           return $this->createNew($user);
       }
    }
    
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    } 
    
    
}
    
?>