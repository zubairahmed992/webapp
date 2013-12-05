<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ProductSizeEvent;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\AdminBundle\Entity\ProductSize;
class ProductSizeHelper {

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

    public function findSizeByProductTitle($title, $productid) {
        return $this->repo->findSizeByProductTitle($title, $productid);
    }
     public function find($id) {
        return $this->repo->find($id);
    }
       public function findMeasurementArray($id) {
        return $this->repo->getSizeMeasurementArray($id);
    }
     public function save($entity) {
            $this->em->persist($entity);
            $this->em->flush();
    }
    public function checkAttributes($attributes, $size_measurements) {
        $all_size_measurements = array();
        foreach ($attributes as $key => $value) {
            $all_size_measurements[$key] =  array('exists' => false, 'measurement' => null);
            foreach ($size_measurements as $sm) {
                if($sm['title']==$key){
                $all_size_measurements[$key] =  array('exists' => true, 'measurement' => $sm);
                }
        }
        
            
        }
        return $all_size_measurements;
    }
#--------------------Get Size Array Base On Product--------------------------#
    public function getSizeArrayBaseOnProduct($product_id){
        $sizeArray=$this->repo->getSizeArrayBaseOnProduct($product_id);
        $sizes=array();
        $sizes_bodyType=null;
       foreach($sizeArray as $body_type){
          
          $sizes['body_type']= $body_type['body_type']   ;
          $sizes[]= $body_type['title']   ;
       if($sizes['body_type']=='Petite'){
           $sizes_bodyType['Petite'][]= $body_type['title']; 
       }
       if($sizes['body_type']=='Regular'){
           $sizes_bodyType['Regular'][]= $body_type['title']; 
       }
        if($sizes['body_type']=='Tall'){
           $sizes_bodyType['Tall'][]= $body_type['title']; 
       }
       }
       
       return $sizes_bodyType;
       
    }
#----------------Product Create Sizes------------------------------------------#    
 public function createSizeItemForBodyTypes($product, $p_color, $all_sizes){
  if($all_sizes->getPetiteSizes()!=NULL){
        $sizesForPetiteBodyType=$all_sizes->getPetiteSizes();
        $bodyTypePetite="Petite";
#-------------------- For Petite BodyType--------------------------------------#
        foreach ($sizesForPetiteBodyType as $s) {
               $p_size = $product->getSizeByTitleBaseBodyType($s,$bodyTypePetite);

            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypePetite);
                $this->em->persist($p_size);
                $this->em->flush();
              $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);  
            } else {
                $p_item = $product->getThisItem($p_color, $p_size);
                 if (!$p_item) {
                $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);        
                }
            }
        }
}   
#-------------------End of Petite Size-----------------------------------------# 
    if($all_sizes->getRegularSizes()!=Null){
       $sizesForRegularBodyType=$all_sizes->getRegularSizes();
        $bodyTypeRegular="Regular";
 #---------------- For Regular BodyType--------------------------------------#
        foreach ($sizesForRegularBodyType as $s) {
            $p_size = $product->getSizeByTitleBaseBodyType($s,$bodyTypeRegular);
            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypeRegular);
                $this->em->persist($p_size);
                $this->em->flush();
                $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);  
            } else {
                $p_item = $product->getThisItem($p_color, $p_size);
                if (!$p_item) {
                     $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);  
                }
            }
        }
    }   
#-----------------End of Regular Size Size-------------------------------------# 
     if($all_sizes->getTallSizes()!=Null){
    $sizesForTallBodyType=$all_sizes->getTallSizes();
    $bodyTypeTall="Tall";
 #---------------- For Tall BodyType--------------------------------------#
        foreach ($sizesForTallBodyType as $s) {
 #-----------The Method used for matching size title and body type ------------#
            $p_size = $product->getSizeByTitleBaseBodyType($s,$bodyTypeTall);
            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypeTall);
                $this->em->persist($p_size);
                $this->em->flush();
                $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);  
            } else {
                $p_item = $product->getThisItem($p_color, $p_size);
                if (!$p_item) {
                     $this->container->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);  
                }
            }
        }
     }
 #-----------------End of Tall  Size Size-----------------------------------------#      
        
 }
  
}