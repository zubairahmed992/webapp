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

        $productName = $entity->getName();
        $msg_array = $this->validateForCreate($productName);
        if ($msg_array == null) {
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
        } else {
            return $msg_array;
        }
    }
    
#--------------Updated when color,item and sizes created and updated .---------#
     public function updatedAt($entity) {
        //$msg_array =null;
        //$msg_array = ;

        $productName = $entity->getName();
        $msg_array = $this->validateForCreate($productName);
        if ($msg_array == null) {
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
        } else {
            return $msg_array;
        }
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

    public function delete($id) {
        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();

        if ($entity) {
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
      $genders=$this->container->get('admin.helper.utility')->getGenders();
      $target=$this->container->get('admin.helper.utility')->getTargets();
      $bodyType=$this->container->get('admin.helper.utility')->getBodyTypes();
      $category=$this->container->get('admin.helper.clothing_type')->findAll();
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
            'genders'=>$genders,
            'target'=>$target,
            'bodyType'=>$bodyType,
            'category'=>$category,
        );
    }

//Private Methods    
//----------------------------------------------------------
    private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'Product Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

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
 public function findProductByEllieHM($brand,$gender,$page_number, $limit){
     
     return $this->repo->findProductByEllieHM($brand,$gender,$page_number, $limit);
 } 

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
    
    public function findRecentlyTriedOnByUser($user_id, $page_number=0, $limit=0) {
        return $this->repo->findRecentlyTriedOnByUser($user_id, $page_number, $limit);        
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
 $page=1;//$data['page']; 

 #--------Pagination Started-------------------#
 $cur_page = $page;
$page -= 1;
$per_page = 10; // Per page records
$previous_btn = true;
$next_btn = true;
$first_btn = true;
$last_btn = true;
$start = $page * $per_page;

  
        $entity = $this->repo->searchProduct($brand_id,$male,$female,$target,$category_id,$start,$per_page);
        
        
        $countSearchProduct = count($this->repo->countSearchProduct($brand_id,$male,$female,$target,$category_id));
        $countRecord=count($entity);
       
     $no_of_paginations = ceil($countSearchProduct /$per_page);
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
            return $allSizes['women_letter_sizes'];
            break;
       case 'male_letter':
            return   $allSizes['man_letter_sizes'];
            break; 
        case 'female_number':
            return $allSizes['woman_number_sizes'];
            break;
         case 'male_number':
            return   $allSizes['man_number_sizes'];
            break;
        case 'male_waist':
            return $allSizes['man_waist_sizes'];
            break;
        case 'female_waist':
           return  $allSizes['woman_waist_sizes'];
            break;
    }
    
    
       
       
       
       
}
#-------Product Color Add --------------------------------------#
public function productDetailColorAdd($entity){
    $allSizes=$this->container->get('admin.helper.size')->getAllSizes();
    $sizes_letter=$allSizes['women_letter_sizes'];   
    $sizes_number=$allSizes['man_number_sizes'];
    $sizes_top_man_numbers=$allSizes['man_number_sizes'];;
    $sizes_bottom_man_numbers=$allSizes['man_waist_sizes'];;
    $sizes_women_waist=$allSizes['woman_waist_sizes'];
       if(strtolower($entity->getSizeTitleType())=='letters' and strtolower($entity->getGender())=='f')
       {
          
           $sizes['petite'] = $sizes_letter;
           $sizes['regular'] = $sizes_letter;
           $sizes['tall'] = $sizes_letter;
           $sizes['women_waist'] =Null;   
           return $sizes;
       }       
       if($entity->getSizeTitleType()=='numbers' and strtolower($entity->getGender()=='f'))
       {
            $sizes['petite'] = $sizes_number;
           $sizes['regular'] = $sizes_number;
           $sizes['tall'] = $sizes_number;
           $sizes['women_waist'] =Null;           
            return $sizes;
       }
       if(strtolower($entity->getSizeTitleType())=='letters'  and strtolower($entity->getGender())=='m'  and (strtolower($entity->getClothingType()->getTarget())=='top'))
       {
           
           $sizes['petite'] = Null;
           $sizes['regular'] = $sizes_letter;
           $sizes['tall'] = Null;
           $sizes['women_waist'] =Null;         
            return $sizes;
       }
       if($entity->getSizeTitleType()=='letter' and strtolower($entity->getGender())=='m'  and (strtolower($entity->getClothingType()->getTarget())=='bottom') )
       {
           
          $sizes['petite'] = Null;
          $sizes['regular'] = $sizes_number;
          $sizes['tall'] = Null;
          $sizes['women_waist'] =Null;          
           return $sizes;
       } 
       
       if($entity->getSizeTitleType()=='numbers' and (strtolower($entity->getGender())=='m' ) and (strtolower($entity->getClothingType()->getTarget())=='top' ))
       {
          
           $sizes['petite'] =Null;
           $sizes['regular'] = $sizes_top_man_numbers;
           $sizes['tall'] = Null;
           $sizes['women_waist'] =Null;  
            return $sizes;
       }
       if($entity->getSizeTitleType()=='numbers' and (strtolower($entity->getGender())=='m'  ))
       {
           
          $sizes['petite'] = Null;
          $sizes['regular'] = $sizes_bottom_man_numbers;
          $sizes['tall'] = Null;
          $sizes['women_waist'] =Null;        
           return $sizes;
       } 
        if((strtolower($entity->getSizeTitleType())=='waist' )and (strtolower($entity->getGender())=='f'  and (strtolower($entity->getClothingType()->getTarget())=='bottom')))
       {
           
          $sizes['petite'] = $sizes_women_waist;
          $sizes['regular'] = $sizes_women_waist;
          $sizes['tall'] = $sizes_women_waist;
          $sizes['women_waist'] =$sizes_women_waist; 
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
         if(strtolower($product->getSizeTitleType())=='letters' and strtolower($product->getGender())=='f')
         {      
                    if($brandSpecification->getFemaleLetters()!="null"){
                      $female_letters=$this->getArray($brandSpecification->getFemaleLetters());  
                    }else{
                        $female_letters=Null;
                    }
                  return  $this->fillArray(null, $female_letters,'f',$this->foo('female_letter'));                   
               }       
               if($product->getSizeTitleType()=='numbers' and strtolower($product->getGender()=='f'))
               {
                    if($brandSpecification->getFemaleNumbers()!="null"){
                        $female_numbers=$this->getArray($brandSpecification->getFemaleNumbers());
                    }else{
                        $female_numbers=null;
                    }
                 return  $this->fillArray(null, $female_numbers,'f',$this->foo('female_number'));
               }
               if(strtolower($product->getSizeTitleType())=='letters'  and strtolower($product->getGender())=='m'  and (strtolower($product->getClothingType()->getTarget())=='top'))
               {
                    if($brandSpecification->getMaleLetters()!="null"){
                     $male_letters=$this->getArray($brandSpecification->getMaleLetters());   
                    }else{
                     $male_letters=null;   
                    }
                   
                return  $this->fillArray(null, $male_letters,'m',$this->foo('male_letter'));
               }
               if($product->getSizeTitleType()=='letter' and strtolower($product->getGender())=='m'  and (strtolower($product->getClothingType()->getTarget())=='bottom') )
               {
                   if($brandSpecification->getMaleWaists()!="null"){
                       $male_waists=$this->getArray($brandSpecification->getMaleWaists());
                   }else{
                       $male_waists=null;
                   }
                
                return  $this->fillArray(null, $male_waists,'m',$this->foo('male_waist'));
               } 

               if($product->getSizeTitleType()=='numbers' and (strtolower($product->getGender())=='m' ) and (strtolower($product->getClothingType()->getTarget())=='top' ))
               {  
                  if($brandSpecification->getMaleNumbers()!="null"){
                      $male_numbers=$this->getArray($brandSpecification->getMaleNumbers());
                  }else{
                      $male_numbers=null;
                  }
                   
                  return  $this->fillArray(null, $male_numbers,'m',$this->foo('male_number'));
               }
               if($product->getSizeTitleType()=='numbers' and (strtolower($product->getGender())=='m'  ))
               { 
                   if($brandSpecification->getMaleNumbers()!="null"){
                      $male_numbers=$this->getArray($brandSpecification->getMaleNumbers()); 
                   }else{
                      $male_numbers=null; 
                   }
                   
                  return  $this->fillArray(null, $male_numbers,'m',$this->foo('male_number'));
               } 
                if((strtolower($product->getSizeTitleType())=='waist' )and (strtolower($product->getGender())=='f'  and (strtolower($product->getClothingType()->getTarget())=='bottom')))
               {
                  if($brandSpecification->getFemaleWaists()!="null"){
                      $female_waists=$this->getArray($brandSpecification->getFemaleWaists());   
                  }else{
                      $female_waists=null;
                  }
                  
                  return $this->getArray(null,$female_waists,'f',$this->foo('female_waist'));
               } 
       
   }else{
       
       return $this->productDetailColorAdd($product);
   }
        
     }  
        
       
         #------------Get Associative Array------------------#
       public function fillArray($size_type, $array,$gender=Null,$default_array){ #public function fillArray($size_type, $array, $default_array){ 
           $data=array();
           if(empty($array)){
               $array=$default_array;
           }
           if ($size_type==null){
                    if($gender=='m'){
                        $data['regular'] = $array;
                    }else{
                  $data['petite'] = $array;
                  $data['regular'] = $array;
                  $data['tall'] = $array;
                  $data['women_waist'] =$array; 
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
           foreach($arr as $key){
               $data[$key]=$key;
           }
           return $data;
       }
             
              
        
    
  
}
