<?php

namespace LoveThatFit\SupportBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;


class SupportTaskLogHelper {

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
    
   public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container){
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $supportTaskLog = new $class();
        return $supportTaskLog;
    }   
//-------------------------------------------------------    
    public function fill($stl_obj, $stl){
        array_key_exists('member_email', $stl)?$stl_obj->setMemberEmail($stl['member_email']):'';
        array_key_exists('duration', $stl)?$stl_obj->setDuration($stl['duration']):'';
        array_key_exists('log_type', $stl)?$stl_obj->setLogType($stl['log_type']):'';
        array_key_exists('start_time', $stl)?$stl_obj->setStartTime($stl['start_time']):'';
        array_key_exists('end_time', $stl)?$stl_obj->getStartTime($stl['end_time']):'';
        return $stl_obj;
    }
   
//-------------------------------------------------------
    public function saveAsNew($stl_array) {        
        $entity=$this->fill($this->createNew(), $stl_array);
        $entity->setCreatedAt(new \DateTime('now'));        
        $this->save($entity);            
    }
//-------------------------------------------------------
    
    public function save($entity) {        
            $this->em->persist($entity);
            $this->em->flush();
    }

   

    
    
}