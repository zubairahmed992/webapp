<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productcolorEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ProductItemHelper {

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

     public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }

    public function find($id) {
        return $this->repo->find($id);
    }
   
    public function findByColorSize($product_color_id, $product_size_id){
       return $this->repo->findByColorSize($product_color_id, $product_size_id);
       
   }
   #-------------------------------------------------------------------------
    public function getProductItemById($id) {
        $product_item = $this->repo->find($id);
        return $product_item;
    }
   #--------------------------------------------------------------------------\
    public function getProductByItemId($productItem) {
        $entity = $this->repo->findProductByItemId($productItem);
        return $entity;
    }


    //-------------------------------------------------------

    public function save($entity) {
            $this->em->persist($entity);
            $this->em->flush();
    }
    
    #-------Raw image download functionlity-----------------------------------------#
public function rawImageDownload($item_id){
    
   $entity_item = $this->repo->find($item_id);
   $path=$entity_item->getRawImageAbsolutePath();
    $response =new Response();
   
  // $response->headers->set('Content-Type','image/jpeg');
   $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path));        
   $response->headers->set('Pragma', "no-cache");
   $response->headers->set('Expires', "0");
   $response->headers->set('Content-Transfer-Encoding', "binary");
   $response->sendHeaders();
   $response->setContent(readfile($path));
   return $response;
}
#---------------------Add Item For product--------------------------------------#
public function addItem($product, $p_color, $p_size) {
        $p_item = new ProductItem();
        $p_item->setProduct($product);
        $p_item->setProductSize($p_size);
        $p_item->setProductColor($p_color);
        $this->save($p_item);
    }
#-------------Get Item base on product-----------------------------------------#
    public function getAllItemBaseProduct($product_id){
        return $this->repo->getAllItemBaseProduct($product_id);
        
    }
#------------------------------Find item by SKU--------------------------------#    
    public function findItemBySku($sku){
        return $this->repo->findItemBySku($sku);
        
    }
}