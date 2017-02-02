<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class FitModelMeasurementHelper {

    protected $dispatcher;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

     private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
         $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    //-------------------------

    public function createNew() {
        $class = $this->class;
        return new $class();
    }

//--------------------------Save 

    public function save($entity) {       
        if ($entity != null) {            
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();                        
        } 
        return $entity;
    }

//------------------

    public function delete($id) {
        $entity = $this->repo->find($id);        
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();            
        }         
    }

//----------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------
    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }

    
    //-------------------------
 public function getArray() {
     return  $this->repo->getArray();  
 }
 
    //-------------------------
 public function getTitleArray() {
     $title=array();
     foreach($this->repo->findAll() as $fm){
         $title[$fm->getTitle()]=$fm->getId();
     }
     return $title;     
 }
}