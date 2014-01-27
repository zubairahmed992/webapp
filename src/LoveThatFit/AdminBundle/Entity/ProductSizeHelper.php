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
   
    protected $conf;
   
    
    
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        
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
    public function getFitPointMeasurementArray($id, $fit_point) {
        return $this->repo->getProductFitPointMeasurementArray($id, $fit_point);
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
       if($sizes['body_type']=='Waist'){
           $sizes_bodyType['Waist'][]= $body_type['title']; 
       }
       }
       
       return $sizes_bodyType;
       
    }
    
    
    
    
    
#------------------ Save the index of Size Base on the size title --------------#
    private function getSizeIndexValue($productSizeTitleType,$gender,$allSizes,$product){
#------------------------------------Gender Female------------------------------#
         if($gender=='F'){
           if($productSizeTitleType=='numbers'){
               $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['women']['numbers'];
           }
           if($productSizeTitleType=='letters'){
               $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['women']['letter'];
           }
           if($productSizeTitleType=='Waist'){
               $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['women']['waist'];
           }
               foreach($productSizesRevampWomanNumbers as $womanNumbers){
                    if($womanNumbers['title']==$allSizes){
                        return $womanNumbers['index']; 
                      //  $p_size->setIndexValue($womanNumbers['index']);
                    }
               }
           }
           
        
        
       #---------------------For Male-----------------------------------------#
       if($gender=='M'){
           
           if($productSizeTitleType=='numbers'){
               
                if($product->getClothingType()->getTarget()=='Top'){
                $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['man']['number']['top'];
                }
                if($product->getClothingType()->getTarget()=='Bottom'){
                $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['man']['number']['bottom'];
                }
             }
            
        if($productSizeTitleType=='letter'){
          $productSizesRevampWomanNumbers= $this->conf['constants']['size_title_revamp']['man']['letter'];
        }
       
       
       foreach($productSizesRevampWomanNumbers as $womanNumbers){
                     if($womanNumbers['title']==$allSizes){
                         return $womanNumbers['index']; 
                       //  $p_size->setIndexValue($womanNumbers['index']);
                     }
                }
       }  
 }
    
    
#----------------Product Create Sizes------------------------------------------#    
 public function createSizeItemForBodyTypes($product, $p_color, $all_sizes){
  if($all_sizes->getPetiteSizes()!=NULL){
        $sizesForPetiteBodyType=$all_sizes->getPetiteSizes();
        $bodyTypePetite="Petite";
#-------------------- For Petite BodyType--------------------------------------#
        foreach ($sizesForPetiteBodyType as $s) {
               $p_size = $product->getSizeByTitleBaseBodyType($s,$bodyTypePetite);
               $indexValue=$this->getSizeIndexValue($product->getSizeTitleType(),$product->getGender(),$s,$product);
             //  return $indexValue;
            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypePetite);
                $p_size->setIndexValue($indexValue);
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
             $indexValue=$this->getSizeIndexValue($product->getSizeTitleType(),$product->getGender(),$s,$product);
            
            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypeRegular);
                $p_size->setIndexValue($indexValue);
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
             $indexValue=$this->getSizeIndexValue($product->getSizeTitleType(),$product->getGender(),$s,$product);
            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypeTall);
                $p_size->setIndexValue($indexValue);
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
    
     
     if($all_sizes->getwomenWaistSizes()!=NULL){
        $sizesForWaistBodyType=$all_sizes->getwomenWaistSizes();
        $bodyTypePetite="Waist";
#-------------------- For Petite BodyType--------------------------------------#
        foreach ($sizesForWaistBodyType as $s) {
               $p_size = $product->getSizeByTitleBaseBodyType($s,$bodyTypePetite);
                $indexValue=$this->getSizeIndexValue($product->getSizeTitleType(),$product->getGender(),$s,$product);

            if (!$p_size) {
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $p_size->setbodyType($bodyTypePetite);
                $p_size->setIndexValue($indexValue);
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
 }
  
}