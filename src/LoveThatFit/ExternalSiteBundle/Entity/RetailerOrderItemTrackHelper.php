<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack;
use LoveThatFit\SiteBundle\AvgAlgorithm;

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

    
    
   Public function saveRetailerOrderItem($entity, $user, $retailer_order) {
        
        $order_item = new RetailerOrderItemTrack();
        foreach ($retailer_order['line_items'] as $retailer_item) {
            $order_item->setSku($retailer_item['sku']);
            
            $product_item = $this->container->get('admin.helper.productitem')->findItemBySku($retailer_item['sku']);
            $product_size = $product_item->getProductSize();
            $product = $product_item->getProduct();
            #------feedback 
            $comp = new AvgAlgorithm($user, $product);
            $fb = $comp->getSizeFeedBack($product_size);
            #------------ recommendation 
            if ($fb && array_key_exists('recommendation', $fb) && $fb['recommendation']) {
                $recommended_size = $fb['recommendation']['title'];
                $recommended_index = $fb['recommendation']['fit_index'];
            } else {
                if ($fb['feedback']['fits']) {
                    $recommended_size = $fb['feedback']['title'];
                    $recommended_index = $fb['feedback']['fit_index'];
                }
            }
            #------------------------------------            
            $order_item->setPurchasedFitIndex($fb['feedback']['fit_index']);
            $order_item->setPurchasedFitSize($fb['feedback']['title']);
            $order_item->setRecommendedFitIndex($recommended_index);
            $order_item->setRecommendedFitSize($recommended_size);
            #-----------------------------------
            $user_tried = $this->container->get('site.helper.usertryitemhistory')->countUserItemTryHistory($user, $product, $product_item);
            if ($user_tried > 0) {
                $order_item->setTriedOn(1);
            } else {
                $order_item->setTriedOn(0);
            }            
            $order_item->setRetailerOrderTrack($entity);
            $order_item->setProductItems($product_item);           
        }
        $this->save($order_item);
    }
  

    
     public function save($entity){
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));        
        $this->em->persist($entity);
        $this->em->flush();     
        return $entity;
   }
    #-----
    
    
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