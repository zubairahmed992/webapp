<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Entity\Color;

class ColorHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    //---------------------------------------------------------------------   

     public function save($name) {         
        $msg_array = $this->validateForCreate($name);
        if ($msg_array == null) {
            $entity=new Color();
            $entity->setName($name);
            $this->em->persist($entity);
            $this->em->flush();
            return true;           
        } else {
            return false;
        }

    }

 
//-------------------------------------------------------

 public function find($id) {
        return $this->repo->find($id);
    }
   #-----------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
   
    
 public function findOneByName($name)
 {
     return $this->repo->findOneByName($name);
 }
    
 //-------------------------------------------------------------------------
    
    private function validateForCreate($name) {
        if (count($this->findOneByName($name))>0) {
            return array('message' => 'Color Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
    
    
    
    
}