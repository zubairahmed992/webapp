<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack;
use LoveThatFit\SiteBundle\AvgAlgorithm;

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

    
    
    public function save($retailer_order) {
        $retailer =  $this->getRetailerBySite($retailer_order['referring_site']);        
        $user_id= $this->getUserByReferenceId($retailer_order['customer']['id']);   
        $user=$this->container->get('user.helper.user')->find($user_id);
        $entity=new RetailerOrderTrack();
        $entity->setCartToken($retailer_order['cart_token']);
        $entity->setClosedAt(new \DateTime('now'));
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->setToken($retailer_order['token']);   
        $entity->setOrderStatus($retailer_order['fulfillment_status']);
        $entity->setOrderReferenceId($retailer_order['id']);
        $entity->setOrderNumber($retailer_order['order_number']);                
        $entity->setRetailer($retailer);
        $entity->setUser($user);
        $this->em->persist($entity);
        $this->em->flush();     
        return $this->saveRetailerOrderItemTrack($entity,$retailer_order);
    }

    
  public function getRetailerBySite($retailer_site){
        $retailer = $this->container->get('admin.helper.retailer')->findRetailerBySite($retailer_site);
        if($retailer){
        return $retailer;
        }else{
            return 'No Retaielr Exists';
        }
  }  
  
  public function getUserByReferenceId($referenceId)
  {
      $user=$this->container->get('admin.helper.retailer.site.user')->findUserByReferenceId($referenceId);
        if($user){
        return $user->getUser();
        }else{
            return 'No User Exists';
        }
  }
  
  
   Public function saveRetailerOrderItemTrack($entity,$retailer_order)
   {
       $user_id= $this->getUserByReferenceId($retailer_order['customer']['id']);   
       $user=$this->container->get('user.helper.user')->find($user_id); 
        $order_item = new RetailerOrderItemTrack(); 
       foreach($retailer_order['line_items'] as $retailer_item){
       $order_item->setSku($retailer_item['sku']);       
       $product_item=$this->container->get('admin.helper.productitem')->findItemBySku($retailer_item['sku']);
       $product_size = $product_item->getProductSize();
       $product=$product_item->getProduct();
       $comp = new AvgAlgorithm($user,$product);
       $fb=$comp->getSizeFeedBack($product_size);
        if($fb && array_key_exists('recommendation', $fb) &&  $fb['recommendation']){
          $recommended_size=$fb['recommendation']['title'];
          $recommended_index=$fb['recommendation']['fit_index'];          
      }else
      {
          if($fb['feedback']['fits']){
            $recommended_size=$fb['feedback']['title'];
            $recommended_index=$fb['feedback']['fit_index'];            
          }
      } 
       $user_tried=$this->container->get('site.helper.usertryitemhistory')->countUserItemTryHistory($user,$product,$product_item);
       $order_item->setCreatedAt(new \DateTime('now'));
       $order_item->setUpdatedAt(new \DateTime('now'));       
       $order_item->setPurchasedFitIndex($fb['feedback']['fit_index']);
       $order_item->setPurchasedFitSize($fb['feedback']['title']);  
       $order_item->setRecommendedFitIndex($recommended_index);
       $order_item->setRecommendedFitSize($recommended_size);  
       if($user_tried>0){
         $order_item->setTriedOn(1);    
       }else{
         $order_item->setTriedOn(0);  
       }
       $order_item->setRetailerOrderTrack($entity);
       $order_item->setProductItems($product_item);
       $this->em->persist($order_item);
       $this->em->flush(); 
       }
       return $order_item;       
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