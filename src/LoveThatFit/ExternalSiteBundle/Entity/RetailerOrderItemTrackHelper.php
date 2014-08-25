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

    
    
    public function save($order,$product_item,$retailer_order_item) {
         $entity = new RetailerOrderItemTrack();
         $entity->setPurchasedFitIndex($retailer_order_item['purchased_fit_index']);
         $entity->setPurchasedFitSize($retailer_order_item['purchased_fit_size']);
         $entity->setRecommendedFitIndex($retailer_order_item['recommended_fit_index']);
         $entity->setSku($retailer_order_item['sku']);
         $entity->setTriedOn($retailer_order_item['tried_on']);
         $entity->setCreatedAt(new \DateTime('now'));
         $entity->setUpdatedAt(new \DateTime('now'));
         $entity->setRetailerOrderTrack($order);
         $entity->setProductItems($product_item);
         $this->em->persist($entity);
         $this->em->flush();  
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