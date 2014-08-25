<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack;

class RetailerOrderTrackHelper {

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
        $retailer_order_track = new $class();
        return $retailer_order_track;
    }

//-------------------------------------------------------

    
    
    public function save($retailer_order,$retailer,$user) {
        $entity=new RetailerOrderTrack();
        $entity->setCartToken($retailer_order['cart_token']);
        $entity->setClosedAt(new \DateTime('now'));
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->setOrderNumber($retailer_order['order_number']);
        $entity->setOrderStatus($retailer_order['order_status']);
        $entity->setToken($retailer_order['token']);        
        $entity->setRetailer($retailer);
        $entity->setUser($user);
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

   
}