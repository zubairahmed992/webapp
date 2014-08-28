<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack;


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

    
    
    public function saveValues($retailer_order) {
        
        $retailer = $this->container->get('admin.helper.retailer')->findRetailerBySite($retailer_order['referring_site']);
        if (!$retailer) return 'retiler not found!';
        
        $site_user = $this->container->get('admin.helper.retailer.site.user')->findByReferenceId($retailer_order['customer']['id'], $retailer->getId());
        if (!$site_user) return 'user not found!';
        
        $user = $site_user->getUser(); #????? ['user']['id']
        if (!$user) return 'user not found!';
        
        $entity = new RetailerOrderTrack();
        $entity->setCartToken($retailer_order['cart_token']);
        //$entity->setClosedAt($retailer_order['created_at']); #get from json
        $entity->setToken($retailer_order['token']);   
        $entity->setOrderStatus($retailer_order['fulfillment_status']);
        $entity->setOrderReferenceId($retailer_order['id']);
        $entity->setOrderNumber($retailer_order['order_number']);                
        
        $entity->setRetailer($retailer);
        $entity->setUser($user);
        $this->save($entity);
        $this->container->get('retailer.order.item.track.helper')->saveRetailerOrderItem($entity,$user, $retailer_order);
        //return $this->saveRetailerOrderItemTrack($entity,$user, $retailer_order);
    }
  
   

//-------------------------------------------------------

 public function find($id) {
        return $this->repo->find($id);
    }
    
    
#-----------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
    #-----------------------------------------------------
   public function save($entity){
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));        
        $this->em->persist($entity);
        $this->em->flush();     
        return $entity;
   }
    #-----------------------------------------------------
   public function update($entity){
        $entity->setUpdatedAt(new \DateTime('now'));        
        $this->em->persist($entity);
        $this->em->flush();     
        return $entity;
   }
   

   
}