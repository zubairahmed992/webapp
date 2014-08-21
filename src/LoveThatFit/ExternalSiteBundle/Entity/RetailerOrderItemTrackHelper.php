<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack;

class RetailerOrderItemTrackHelper {

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

   public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }

    //---------------------------------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $retailer_order_item_track = new $class();
        return $retailer_order_item_track;
    }

//-------------------------------------------------------

    
    
    public function save() {
        
    }

    //-------------------------------------------------------

    public function update() {

    }

    
    public function delete($id) {

    
    }
    
    
  

//-------------------------------------------------------

 public function find($id) {
        return $this->repo->find($id);
    }
   #-----------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
#---------------------------------------------------------
   

   
   
}