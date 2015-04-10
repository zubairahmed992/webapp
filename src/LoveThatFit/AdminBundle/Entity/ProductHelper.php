<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ProductEvent;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\AdminBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\SiteBundle\Algorithm;
use ZipArchive;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\SiteBundle\AvgAlgorithm;

class ProductHelper{

     /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;
     /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
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
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $product = new $class();
        return $product;
    }
   
    //-------------------------------------------------------

    public function save($entity) {
        //$msg_array =null;
        //$msg_array = ;

      //  $productName = $entity->getName();
       // $msg_array = $this->validateForCreate($productName);
       // if ($msg_array == null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            
            //$entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Product succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
       // } else {
        //    return $msg_array;
       // }
    }
    
#--------------Updated when color,item and sizes created and updated .---------#
     public function updatedAt($entity) {
        //$msg_array =null;
        //$msg_array = ;

      //  $productName = $entity->getName();
       // $msg_array = $this->validateForCreate($productName);
      //  if ($msg_array == null) {
           // $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            //$entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Product succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
      ///  } else {
          //  return $msg_array;
       // }
    }

    //-------------------------------------------------------

    public function update($entity) {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));

            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Product ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }
  //-------------------------------------------------------

    public function updateDisplayColor($product, $productColor) {
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setDisplayProductColor($productColor);
        $this->em->persist($product);
        $this->em->flush();

     
    }
//-------------------------------------------------------

    public function delete($id) {
        $entity = $this->repo->find($id);
        $entity_name = '';

        if ($entity) {
            $entity_name = $entity->getName();
            $this->em->remove($entity);
            $this->em->flush();

            return array('product' => $entity,
                'message' => 'The Product ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('product' => $entity,
                'message' => 'Product not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }
//-------------------------------------------------------
    
    public function findWithSpecs($id) {
        $entity = $this->repo->find($id);

        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Product not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Product found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }
    
    public function removeBrand() {
        return $this->repo->removeBrand();
    }

    //-------------------------------------------------------

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllProduct($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
         
      #---------------------Start Searching---------------------#
      $brandList=$this->container->get('admin.helper.brand')->findAll();
     // $genders=$this->container->get('admin.helper.utility')->getGenders();
    //  $target=$this->container->get('admin.helper.utility')->getTargets();
     // $bodyType=$this->container->get('admin.helper.utility')->getBodyTypes();
      $sizeSpecs=$this->container->get('admin.helper.size')->getDefaultArray();
     $category=$this->container->get('admin.helper.clothing_type')->getArray();
      
      #-------------End Of Searching------------------------------#
     
        return array('products' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'femaleProduct'=>  $this->countProductsByGender('f'),
            'maleProduct'=>  $this->countProductsByGender('m'),
            'topProduct'=>$this->countProductsByType('Top'),
            'bottomProduct'=>$this->countProductsByType('Bottom'),
            'dressProduct'=>$this->countProductsByType('Dress'),
            'sort'=>$sort,
            'brandList'=>$brandList,
           //'genders'=>$genders,
          //'target'=>$target,
          //'bodyType'=>$bodyType,
            'category'=>$category,
            'size_specs'=>$sizeSpecs,
        );
    }

//Private Methods    
//----------------------------------------------------------
   /* private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'Product Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }*/

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $brand = $this->findOneByName($entity->getName());

        if ($brand && $brand->getId() != $entity->getId()) {
            return array('message' => 'Product Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
    
#------------------------------------------------------
public function find($id) {
        return $this->repo->find($id);
    }


#---------------------------------------------------
    public function findProductByTitle($name) {
        return $this->repo->findProductByTitle($name);
    }
    
#---------------------------------------------------    
    public function countProductsByGender($gender)
    {
        return count($this->repo->findPrductByGender($gender));           
    }

    
    #---------------------------------------------------
    
    public function countProductsByType($target)
    {
        
        return count($this->repo->findPrductByType($target));           
    }
#-------------------------------------------------------------------------------
    public function findByGender($gender, $page_number, $limit){
        
      return $this->repo->findByGender($gender, $page_number, $limit);  
    }
 #------------------------------------------------------------------------------
/* public function findProductByEllieHM($brand,$gender,$page_number, $limit){
     
     return $this->repo->findProductByEllieHM($brand,$gender,$page_number, $limit);
 }*/ 

#---------------------------------------------------------------------------------
 public function findOneByName($brand){
    return $this->repo->findOneByName($brand);
}   
#---------------------------------------------------------------------------------
public function findByGenderBrand($gender, $brand_id, $page_number, $limit){
    return $this->repo->findByGenderBrand($gender, $brand_id, $page_number, $limit);
    
}
#-------------------------------------------------------------------------------
public function findByGenderClothingType($gender, $clothing_type_id, $page_number, $limit){
    return $this->repo->findByGenderClothingType($gender, $clothing_type_id, $page_number, $limit);
}
#-------------------------------------------------------------------------------
public function findSampleClothingTypeGender($gender){
    return $this->repo->findSampleClothingTypeGender($gender);
}
#-------------------------------------------------------------------------------
public function findMostFavoriteProducts($gender, $page_number=0, $limit=0){
    return $this->repo->findMostFavoriteByGender($gender, $page_number, $limit);
}
#-------------------------------------------------------------------------------
//public function findMostLikedProducts($page_number, $limit){
 //   return $this->repo->findMostLikedProducts($page_number, $limit);
//}
public function findProductItemByUser($user_id, $page_number, $limit){
    return $this->repo->findProductItemByUser($user_id, $page_number, $limit);
}

    #---------------------------------------------------
    //               Methods Product listing on index page
    #---------------------------------------------------
    
    public function listByType($options) {        
        $list="";
        
        if (!array_key_exists ('gender', $options)){
            $options['gender']='F';
        }
        
        if(!array_key_exists ('limit', $options)){
            $options['limit']=0;
        }
        if(!array_key_exists ('list_type', $options)){
            $options['list_type']="latest";
        }
         if(!array_key_exists ('page_number', $options)){
            $options['page_number']=0;
        }
                
            switch ($options['list_type'])
        {
        case "latest":        
        $list = $this->findByGenderLatest($options['gender'],$options['page_number'],$options['limit']);
        break;
    
        case "most_tried_on":        
        $list = $this->findMostTriedOnByGender($options['gender']);
        break;
    
        case "recently_tried_on":        
        $list = $this->findRecentlyTriedOnByUser($options['user_id']);
        break;    
        
        case "most_faviourite":        
        $list = $this->findMostFavoriteProducts($options['gender']);        
        break ;      
        
        case "recently_tried_on_for_retailer":                
        $list = $this->findRecentlyTriedOnByUserForRetailer($options['retailer_id'],$options['user_id']);            
        break;            
            
        default:
       $list = $this->findByGenderLatest($options['gender'],$options['page_number'],$options['limit']);
        break ;
        }
        if ($list && count($list)==0){
            $list = $this->findByGenderRandom($options['gender'], $options['limit']);
        }
        return $list;
        
   }

#---------------------------------------------------    
    public function findByGenderBrandName($gender, $brand, $page_number=0, $limit=0){
        return $this->repo->findByGenderBrandName($gender, $brand, $page_number=0, $limit=0);           
    }

#---------------------------------------------------
   
   
#---------------------------------------------------    
    public function findByGenderRandom($gender, $limit){
        return $this->repo->findByGenderRandom($gender, $limit);           
    }

#---------------------------------------------------
    
    public function findByGenderLatest($gender='F', $page_number=0, $limit=0) {
        return $this->repo->findByGenderLatest($gender, $page_number, $limit);
   }
    #---------------------------------------------------
    
    public function findRecentlyTriedOnByUser($user_id, $page_number=1, $limit=10) {
        return $this->repo->findRecentlyTriedOnByUser($user_id, $page_number, $limit);        
   }
   #---------------------------------------------------
    public function findRecentlyTriedOnByUserForRetailer($retailer_id, $user_id, $page_number=0, $limit=0) {       
        return $this->repo->findRecentlyTriedOnByUserForRetailer($retailer_id, $user_id, $page_number, $limit);        
   }
   #---------------------------------------------------
    
   public function findMostTriedOnByGender($gender='F', $page_number=0, $limit=0) {
        return $this->repo->findMostTriedOnByGender($gender, $page_number, $limit);        
   }
    
   #---------------------------------------------------
    
   public function findLTFRecomendedByGender($gender='F', $page_number=0, $limit=0) {
        return $this->repo->findMostTriedOnByGender($gender, $page_number, $limit);                                    
   }
   #---------------------------------------------------

#-------------------------------------Count My Closet-------------------------------------------------#
public function countMyCloset($user_id){
    
    return $this->repo->countMyCloset($user_id);
}
 
#-----------------Product Detail For Site --------------------------------------------------------------#
public function productDetail($id, $product_color_id, $product_size_id){
    
        
        $product_color = null;
        $product_size = null;
        $product_item = null;
// find product
        $product = $this->repo->find($id);

//Calling of Helper 
        $user_helper = $this->container->get('user.helper.user');
        $product_color_helper = $this->container->get('admin.helper.productcolor');
        $product_item_helper = $this->container->get('admin.helper.productitem');
        $product_size_helper = $this->container->get('admin.helper.productsizes');
// find product color if get color id param
        if ($product_color_id) {
            $product_color = $product_color_helper->find($product_color_id);
        } else {// find default product color if not params for color id
            $product_color = $product->getDisplayProductColor();
            $product_color_id = $product_color->getId();
        }

//get color size array, sizes that are available in this color 
        $color_sizes_array = $product_color_helper->getSizeArray($product_color_id);
        $size_id = null;
// find size id is not in param gets the first size id for this color
        if (!$product_size_id) {
            //$psize = array_shift($color_sizes_array);
            //$size_id = $psize['id'];
            $size_id = $product_color->getSmallestAvailableSizeId();
            
        } else {
// if gets the size id in params,  check if this size is available in this color, if not get the first one
            foreach ($color_sizes_array as $csa) {
                if ($csa['id'] == $product_size_id) {
                    $size_id = $csa['id'];
                }
            }
            if ($size_id == null) {
// gets the first size id for this color
                //$psize =array_shift($color_sizes_array);
                //$size_id = $psize['id'];
            $size_id = $product_color->getSmallestAvailableSizeId();
                
            }
        }
        $product_size_id = $size_id;
        $product_size = $product_size_helper->find($product_size_id);

//2) color & size can get an item
        if ($product_size && $product_color) {
            $product_item = $product_item_helper->findByColorSize($product_color->getId(), $product_size->getId());
        }

        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        return array('product' => $product,
            'product_color' => $product_color,
            'product_size' => $product_size,
            'product_item' => $product_item);
    
    }
#------------------Method For Returning Device Type of Current User------------# 
    public function getDeviceTypeByUser($user_id){
        
         $user_helper = $this->container->get('user.helper.user');
         $user=$user_helper->find($user_id);
         return $user_device_type=$user->getDeviceType();
         //$product_item_helper = $this->container->get('admin.helper.productitem');
         //return $product_item_helper->productItem($user_id);
    }
  
#-------------------------User Try History for Website---------------------------#
    public function userTryProducts($user_id,$page_number){
         
        
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->findTryProductHistory($user_id, $page_number, $limit);
        $rec_count = count($this->repo->findTryProductHistory($user_id, 0, 0));
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('productItem' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
        );
       
    }
    
    
    public function userProfileTryProducts($user_id,$page_number){
         
        
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->findTryProfileProductHistory($user_id, $page_number, $limit);
        $rec_count = count($this->repo->findTryProfileProductHistory($user_id, 0, 0));
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('productItem' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
        );
       
    }
    
    
#------------------------------------------------------------------------------#
 #-----------------------------------------Zip Downloading------------------#
    public function zipDownload($id){
    
    $product = $this->repo->find($id);
    $images=$product->getColorImagesPaths();
    $itemImages=$product->getItemImagesPaths();
    $archive_file_name =$product->getName().$product->getId().'.zip';
   $product=$this->zipFilesAndDownload($images,$archive_file_name,$itemImages);
   if($product['status']=='1'){
       return array('status'=>'1');
   }
  
}
#------------------------------------------------------------------------------#
public function zipMultipleDownload($data){


    $product = $this->repo->find(3);
    $images=$product->getColorImagesPaths();
    $itemImages=$product->getItemImagesPaths();
    $archive_file_name =$product->getName().$product->getId().'.zip';
    return new response(json_encode($this->zipFilesAndDownload($images,$archive_file_name,$itemImages)));
   
}
// #-------------Function for dowloading images in zip format--------------------#
 public function zipFilesAndDownload($file_names,$archive_file_name,$itemImages)
    {
        $zip = new ZipArchive();

       	if ($zip->open($archive_file_name, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) === TRUE) {
            foreach ($file_names as $files) {
                $zip->addFile($files['web'], $files['web']);
                $zip->addFile($files['iphone'], $files['iphone']);
                
            }
            foreach ($itemImages as $itemfiles) {
               $zip->addFile($itemfiles['web'], $itemfiles['web']);
               $zip->addFile($itemfiles['iphone4s'], $itemfiles['iphone4s']);
                $zip->addFile($itemfiles['iphone5'], $itemfiles['iphone5']);
            }
          
         
        if($zip->status){
         $zip->close();
        $response = new Response();
        //then send the headers to foce download the zip file
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($archive_file_name) . '"');
        $response->headers->set('Pragma', "no-cache");
        $response->headers->set('Expires', "0");
        $response->headers->set('Content-Transfer-Encoding', "binary");
        $response->sendHeaders();
        $response->setContent(readfile($archive_file_name));
        return $response; 
         }else{
             return array('msg'=>'Images not found','status'=>'1');
         }
         
         
        }
         
    }
    
    
    
    

#------------------------------------------------------------------------------#
#----------------Get Count All Record With Current Product Limit---------------#
public function getRecordsCountWithCurrentProductLimit($product_id){
    
    return $this->repo->getRecordsCountWithCurrentProductLimit($product_id);
}
    
#----------------Getting Record for Searching--------------------------------#
public function searchProduct($data){
        
    if (isset($data['brand'])) {
            $brand_id = $data['brand'];
        } else {
            $brand_id = null;
        }

        if (isset($data['category'])) {
            $category_id = $data['category'];
        } else {

            $category_id = null;
        }
        if (isset($data['genders'])) {

            $genders = $data['genders'];
        } else {
            $genders = null;
        }


        if (isset($genders['0'])) {
            $male = $genders['0'];
        } else {
            $male = null;
        }
        if (isset($genders['1'])) {
            $female = $genders['1'];
        } else {
            $female = null;
        }
        if (isset($data['target'])) {
            $target = $data['target'];
        } else {
            $target = null;
        }
        
         if (isset($data['page'])) {
            $page = $data['page'];
        } else {
            $page = 1;
        }
       
// $page=1;//$data['page']; 

#--------Pagination Started-------------------#
$cur_page = $page;
$page -= 1;
$per_page =20; // Per page records
$previous_btn = true;
$next_btn = true;
$first_btn = true;
$last_btn = true;
$start = $page * $per_page;

$entity = $this->repo->searchProduct($brand_id,$male,$female,$target,$category_id,$start,$per_page);
//$countSearchProduct = count($this->repo->countSearchProduct($brand_id,$male,$female,$target,$category_id));
$countRecord=count($entity);
//return $countSearchProduct;
     $no_of_paginations = ceil($countRecord /$per_page);
  
  if ($cur_page >= 7) {
    $start_loop = $cur_page - 3;
    if ($no_of_paginations > $cur_page + 3)
        $end_loop = $cur_page + 3;
    else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
        $start_loop = $no_of_paginations - 6;
        $end_loop = $no_of_paginations;
    } else {
        $end_loop = $no_of_paginations;
    }
} else {
    $start_loop = 1;
    if ($no_of_paginations > 7)
        $end_loop = 7;
    else
        $end_loop = $no_of_paginations;
}
return array('productResult'=>$entity,'countRecord'=>$countRecord,'first_btn'=>$first_btn,'cur_page'=>$cur_page,'previous_btn'=>$previous_btn,'last_btn'=>$last_btn,'start_loop'=>$start_loop,'end_loop'=>$end_loop,'next_btn'=>$next_btn,'no_of_paginations'=>$no_of_paginations);    
        
   // return $this->repo->searchProduct($brand_id,$male,$female,$target,$category_id);
}
public function searchCategory($target){
    return $this->repo->searchCategory($target);
    
}

#------------------------------------------------------------------------------#
#-------------------lattest Product Specificatiom-------------------#
public function productDetailArray($data,$entity){
            // $data=$request->request->all();
            if(isset($data['product']['styling_type'])){$entity->setStylingType($data['product']['styling_type']);}
            if(isset($data['product']['hem_length'])){$entity->setHemLength($data['product']['hem_length']);}
            if(isset($data['product']['neckline'])){$entity->setNeckLine($data['product']['neckline']);}
            if(isset($data['product']['sleeve_styling'])){$entity->setSleeveStyling($data['product']['sleeve_styling']);}
            if(isset($data['product']['rise'])){$entity->setRise($data['product']['rise']);}            
            if(isset($data['fit_pirority'])){$entity->setFitPriority($this->getJsonForFields($data['fit_pirority']));}
            if(isset($data['fabric_content'])){$entity->setFabricContent($this->getJsonForFields($data['fabric_content']));}
            if(isset($data['garment_detail'])){$entity->setGarmentDetail($this->getJsonForFields($data['garment_detail']));}
             return $this->save($entity);
           
    
}
#----------------------Product Cloting type attribute ------------------------#
public function productClothingTypeAttribute($target_array){
    $clothing_type_id = $target_array['clothing_type'];
    $gender=$target_array['gender'];
    if($gender=="F"){$gender="women";}
    else{$gender="man";}
    $clothing_type=$this->container->get('admin.helper.clothingtype')->findById($clothing_type_id);
    $clothing_type_array=strtolower($clothing_type['target']);
    $clothingTypeAttributes=array();
    if($gender=="man") 
    {    if($clothing_type_array=="top" ){
        $clothingTypeAttributes['fitting_priority']=$this->container->get('admin.helper.product.specification')->gettingTopManFittingPriority($clothing_type_array);  
        }
        if($clothing_type_array=="bottom" ){
        $clothingTypeAttributes['fitting_priority']=$this->container->get('admin.helper.product.specification')->gettingBottomManFittingPriority($clothing_type_array);  
        }
    }
    if($gender=="women") 
    {   
      if ($clothing_type_array=="top" ){
        $clothingTypeAttributes['fitting_priority']=$this->container->get('admin.helper.product.specification')->gettingTopWomenFittingPriority($clothing_type_array);  
        }
        if($clothing_type_array=="bottom" ){
        $clothingTypeAttributes['fitting_priority']=$this->container->get('admin.helper.product.specification')->gettingBottomWomenFittingPriority($clothing_type_array);  
        }
        if($clothing_type_array=="dress" ){
        $clothingTypeAttributes['fitting_priority']=$this->container->get('admin.helper.product.specification')->gettingDressWomenFittingPriority($clothing_type_array);  
        }
    }   
   
     $clothingTypeAttributes['fabric_content']=$this->container->get('admin.helper.product.specification')->getFabricContent();  
     $clothingTypeAttributes['garment_detail']=$this->container->get('admin.helper.product.specification')->getGarmentDetail();  
    
  return $clothingTypeAttributes;  
}
#---------------Delete Product------------------------------------------------#
public function productDelete($id){
  return $this->delete($id);
}

private function foo($size_type){
$allSizes=$this->container->get('admin.helper.size')->getAllSizes();
    switch ($size_type){
        case 'female_letter':
            return $allSizes['woman_letter_sizes'];
            break;
        
        case 'female_number':
            return $allSizes['woman_number_sizes'];
            break;
        case 'female_waist':
            return $allSizes['woman_waist_sizes'];
            break;
        case 'female_bra':
            return $allSizes['woman_bra_sizes'];
            break;
        
        case 'male_letter':
            return   $allSizes['man_letter_sizes'];
            break; 
       case 'male_chest':
            return   $allSizes['man_chest_sizes'];
            break;
       case 'male_waist':
            return $allSizes['man_waist_sizes'];
            break;
        case 'male_shirt':
           return  $allSizes['man_shirt_sizes'];
            break;
       case 'male_neck':
            return $allSizes['man_neck_sizes'];
            break;
         
    }
    
    
       
       
       
       
}
#-------Product Color Add --------------------------------------#
public function productDetailColorAdd($entity){
    $allSizes=$this->container->get('admin.helper.size')->getAllSizes();
    $fitType=$this->container->get('admin.helper.size')->getAllFitType();
    // Male Sizes 
    $sizes_chest_man=$allSizes['man_chest_sizes'];
    $sizes_shirt_man=$allSizes['man_shirt_sizes'];
    $sizes_letter_man=$allSizes['man_letter_sizes'];
    $sizes_waist_man=$allSizes['man_waist_sizes'];
    $sizes_neck_man=$allSizes['man_neck_sizes'];
    
    // Female Sizes
    $sizes_letter_woman=$allSizes['woman_letter_sizes'];   
    $sizes_number_woman=$allSizes['woman_number_sizes'];   
    $sizes_waist_woman=$allSizes['woman_waist_sizes'];   
    $sizes_bra_woman=$allSizes['woman_bra_sizes'];   
    
    
    
       if(strtolower($entity->getSizeTitleType())=='letter' and strtolower($entity->getGender())=='f')
       {
           foreach($fitType['woman'] as $femaleFitType){
            $sizes[$femaleFitType] = $sizes_letter_woman;
           }  
          return $sizes;
       }       
       if($entity->getSizeTitleType()=='number' and strtolower($entity->getGender()=='f'))
       {
           foreach($fitType['woman'] as $femaleFitType){
            $sizes[$femaleFitType] = $sizes_number_woman;
           }  
          return $sizes;
       }
       if($entity->getSizeTitleType()=='waist' and strtolower($entity->getGender()=='f'))
       {
           foreach($fitType['woman'] as $femaleFitType){
            $sizes[$femaleFitType] = $sizes_waist_woman;
           }  
          return $sizes;
       }
       if($entity->getSizeTitleType()=='bra' and strtolower($entity->getGender()=='f'))
       {
           foreach($fitType['woman'] as $femaleFitType){
            $sizes[$femaleFitType] = $sizes_bra_woman;
           }  
          return $sizes;
       }
       if(strtolower($entity->getSizeTitleType())=='letter'  and strtolower($entity->getGender())=='m')
       {
           
           foreach($fitType['man'] as $maleFitType){
            $sizes[$maleFitType] = $sizes_letter_man;
           }  
          return $sizes;
       }
      
       
       if($entity->getSizeTitleType()=='chest' and (strtolower($entity->getGender())=='m' ))
       {
          foreach($fitType['man'] as $maleFitType){
            $sizes[$maleFitType] = $sizes_chest_man;
           }  
          return $sizes;
       }
       if($entity->getSizeTitleType()=='shirt' and (strtolower($entity->getGender())=='m'  ))
       {
            foreach($fitType['man'] as $maleFitType){
            $sizes[$maleFitType] = $sizes_shirt_man;
           }  
          return $sizes;
       } 
        if((strtolower($entity->getSizeTitleType())=='waist' )and (strtolower($entity->getGender())=='m' ))
       {
           foreach($fitType['man'] as $maleFitType){
            $sizes[$maleFitType] = $sizes_waist_man;
           }  
          return $sizes;
       } 
     if((strtolower($entity->getSizeTitleType())=='neck' )and (strtolower($entity->getGender())=='m' ))
       {
           foreach($fitType['man'] as $maleFitType){
            $sizes[$maleFitType] = $sizes_neck_man;
           }  
          return $sizes;
       }   
       
}


#-----Get JSON FEILD-----------------------------#
 private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
#-------------------Get Brand Specification base on Product--------------------#
    public function getBrandSpecifications($product){
        
        $brandSpecification=$product->getBrand()->getBrandspecification();
      
        if(!is_null($brandSpecification)){
           
           
        #-----------Get Gender From Brand Specification -----------------------#
        //$fitType=json_decode($brandSpecification->getFitType());
        //$fitArray[]=$this->fillArray($fitType,$male_numbers);
        //return $fitArray;
               #---------------------Returning Sizes----------------------------------#
         if(strtolower($product->getSizeTitleType())=='letter' and strtolower($product->getGender())=='f')
         { 
            if($brandSpecification->getFemaleLetter()!=null){
              $female_letter=$this->getArray($brandSpecification->getFemaleLetter());  
             }else{
              $female_letter=Null;}
             return  $this->fillArray(null, $female_letter,'f',$this->foo('female_letter'));                   
        }       
               if($product->getSizeTitleType()=='number' and strtolower($product->getGender()=='f'))
               {
                  
                    if($brandSpecification->getFemaleNumber()!=null){
                        $female_number=$this->getArray($brandSpecification->getFemaleNumber());
                    }else{
                        $female_number=null;
                    }
                 return  $this->fillArray(null, $female_number,'f',$this->foo('female_number'));
               }
           
         if($product->getSizeTitleType()=='waist' and strtolower($product->getGender()=='f'))
               {
                    if($brandSpecification->getFemaleWaist()!=null){
                        $female_waist=$this->getArray($brandSpecification->getFemaleWaist());
                    }else{
                        $female_waist=null;
                    }
                 return  $this->fillArray(null, $female_waist,'f',$this->foo('female_waist'));
               }   
      if($product->getSizeTitleType()=='bra' and strtolower($product->getGender()=='f'))
               {
           if($brandSpecification->getFemaleBra()!=null and $brandSpecification->getFemaleBra()!="null"){
                       
                        $female_bra=$this->getArray($brandSpecification->getFemaleBra());
                    }else{
                        $female_bra=null;
                    }
                 return  $this->fillArray(null, $female_bra,'f',$this->foo('female_bra'));
               }             
               
      if(strtolower($product->getSizeTitleType())=='letter'  and strtolower($product->getGender())=='m'  and (strtolower($product->getClothingType()->getTarget())=='top' or strtolower($product->getClothingType()->getTarget())=='dress'))
      {
       if($brandSpecification->getMaleLetter()!=null){
         $male_letter=$this->getArray($brandSpecification->getMaleLetter());   
         }else{
         $male_letter=null;   
         }
                   
      return  $this->fillArray(null, $male_letter,'m',$this->foo('male_letter'));
      }
       if($product->getSizeTitleType()=='letter' and strtolower($product->getGender())=='m'  and (strtolower($product->getClothingType()->getTarget())=='bottom') )
       {
        if($brandSpecification->getMaleLetter()!=null){
         $male_letter=$this->getArray($brandSpecification->getMaleLetter());
         }else{
           $male_letter=null;
         }
                
        return  $this->fillArray(null, $male_letter,'m',$this->foo('male_letter'));
        } 

     if($product->getSizeTitleType()=='chest' and (strtolower($product->getGender())=='m' ) and (strtolower($product->getClothingType()->getTarget())=='top' or strtolower($product->getClothingType()->getTarget())=='dress' ))
     {  
        if($brandSpecification->getMaleChest()!=null){
        $male_chest=$this->getArray($brandSpecification->getMaleChest());
         }else{
         $male_chest=null;
        }
                   
        return  $this->fillArray(null, $male_chest,'m',$this->foo('male_chest'));
      }
      if($product->getSizeTitleType()=='shirt' and (strtolower($product->getGender())=='m'  ))
         { 
          if($brandSpecification->getMaleShirt()!=null){
             $male_shirt=$this->getArray($brandSpecification->getMaleShirt()); 
           }else{
              $male_shirt=null; 
           }
                   
        return  $this->fillArray(null, $male_shirt,'m',$this->foo('male_shirt'));
       } 
     if((strtolower($product->getSizeTitleType())=='waist' )and (strtolower($product->getGender())=='m'))
        {
          if($brandSpecification->getMaleWaist()!=null){
          $male_waist=$this->getArray($brandSpecification->getMaleWaist());   
           }else{
           $male_waist=null;
          }
         return $this->fillArray(null,$male_waist,'m',$this->foo('male_waist'));
        } 
              
        if((strtolower($product->getSizeTitleType())=='neck' )and (strtolower($product->getGender())=='m'))
         {  if($brandSpecification->getMaleNeck()!=null){
                      $male_neck=$this->getArray($brandSpecification->getMaleNeck());   
                  }else{
            $male_neck=null;
          }
         return $this->fillArray(null,$male_neck,'m',$this->foo('male_neck'));
        } 
       
   }else{
        return $this->productDetailColorAdd($product);
   }
        
     }  
#------------------------------#
    public function getSizeArray($product) {
        if ($product && $product->getBrand()->getBrandSpecification()) {
            return $product->getBrand()->getBrandSpecification()->getSizeArray($product->getGender(), $product->getSizeTitleType());
        } else {
            return $this->container->get('admin.helper.size')->getSizeArray($product->getGender(), $product->getSizeTitleType());
        }        
    }

    #------------Get Associative Array------------------#
       public function fillArray($size_type, $array,$gender=Null,$default_array){ #public function fillArray($size_type, $array, $default_array){ 
          $data=array();
          $fitType=$this->container->get('admin.helper.size')->getAllFitType();
           if(empty($array)){
               $array=$default_array;
           }
           if ($size_type==null){
                    if($gender=='m'){
                        foreach($fitType['man'] as $maleFitType)
                        {
                         $data[$maleFitType] = $array;
                        }    
                        
                    }else{
                  foreach($fitType['woman'] as $femaleFitType)
                        {
                         $data[$femaleFitType] = $array;
                        } 
              } 
           }else{
           foreach($size_type as $key=>$v){
               $data[$v]=$array;
           }
           
           }
           return $data;
       }
             
  #------------Get Associative Array------------------#
       public function getArray($array){
           $arr=json_decode($array);
           $data=array();
            if (is_array($arr)){
           foreach($arr as $key){
               $data[$key]=$key;
           }
            }
           return $data;
       }
#------------------------------------------------------------------------
public function findProductColorSizeItemViewByTitle($product_name_array){
    return $this->repo->findProductColorSizeItemViewByTitle($product_name_array);
}
#------------------------------------------------------------------------
public function breakFileName($request_array,$product_id){
    #Format: Regular_XL_Darl-Gray_Front-Open.png
    #last bit, view is optional
    $request_array=  strtolower($request_array);
    $_exploded = explode("_",$request_array);    
    $a=array('product_id'=>$product_id);
    $type=Array(1 => 'jpg', 2 => 'jpeg', 3 => 'png', 4 => 'gif'); 

        # file name/ext with/without view name       
       if (count($_exploded)==3){
           $last_bits = explode(".",$_exploded[2]);           
           $a['color_title'] = $last_bits[0];
       }elseif (count($_exploded)==4){
           $last_bits = explode(".",$_exploded[3]);
           $a['color_title'] = $_exploded[2];
           $a['view_title'] = $last_bits[0];           
       }else{
           return array('message' => 'Invalid Format!', 'success'=> 'false');
       }
       #validate file format 
       if(count($last_bits)!=2 || (count($last_bits)==2 && !(in_array($last_bits[1],$type)))){
           return array('message' => 'Invalid Format!', 'success'=> 'false');
       }       
       # no/invalid body type given then regular 
       $a['body_type'] = !($this->container->get('admin.helper.utility')->isBodyType($_exploded[0])) ? "regular" : $_exploded[0];    
       $a['file_name'] = 'item_image.' . $last_bits[1];
       $a['size_title'] = $_exploded[1];
       $a['message'] = 'Done';
       $a['success'] = 'true';
       return $a;
}       
       
              
  #------------------------Find Item for Multiple Images Uploading--------------#
 public function findItemMultipleImpagesUploading($request_array,$product_id){
   // $request_array='Regular_00_baby_blue.jpg';
   $explode_array=explode("_",$request_array);
   
   #---------------------------------------------------------------------------#
    $count=count($explode_array);
   #-----------Check Body Type is Availbe or Not-------------------------------#
   
  if(!($this->container->get('admin.helper.utility')->isBodyType($explode_array[0]))){
           $request_arrays['body_type'] = "Regular";
   }
       if (isset($explode_array[0])) {
            $request_arrays['body_type'] = $explode_array[0];
        }
       if (isset($explode_array[1])) {
            $request_arrays['size_title'] =$explode_array[1];
        } else{
            $request_arrays['size_title'] =NULL;
        }
         
       if (isset($explode_array[2])) {
           $sizeTitle=explode(".",$explode_array[2]);
           $request_arrays['color_title'] = $sizeTitle[0];
        }else{
               $request_arrays['color_title'] =NULL;
        }
        
   
   if($count>3){
        if (isset($explode_array[2])) {
           $sizeTitle=explode(".",$explode_array[2]);
           $sizeTitleSingle=explode("_",$sizeTitle[0]);
           $request_arrays['color_title'] = $sizeTitleSingle[0];
        }
   }
   if (isset($request_array)) {
            $request_arrays['product_id'] =$product_id;
        }
       
       // return $request_arrays;
 // $request_array=array('product_id'=>1,'color_title'=>'Black','body_type'=>'Tall','size_title'=>'00');
        $return_value= $this->repo->findItemMultipleImpagesUploading($request_arrays);
        if($return_value){
            return $return_value;
        }else{
            return false;
        }
 }      
  #------------------------------------------------------------------------------#
    public function getProductFittingDetail($product_id,$user_id){
       $user=$this->container->get('user.helper.user')->find($user_id);
       $product=$this->find($product_id);
       return $product->getDefaultItem($user);
       
             
    }  
  
}
