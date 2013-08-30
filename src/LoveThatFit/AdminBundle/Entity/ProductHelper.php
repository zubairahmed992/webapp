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

            $entity->upload();
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
        return array('products' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'femaleProduct'=>  $this->countProductsByGender('f'),
            'maleProduct'=>  $this->countProductsByGender('m'),
            'topProduct'=>$this->countProductsByType('Top'),
            'bottomProduct'=>$this->countProductsByType('Bottom'),
            'dressProduct'=>$this->countProductsByType('Dress')
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
    private function countProductsByGender($gender)
    {
        return count($this->repo->findPrductByGender($gender));           
    }

    
    #---------------------------------------------------
    
    private function countProductsByType($target)
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
public function findOneByName($brand){
    return $this->repo->findOneByName($brand);
    
}   
#-----------------------------------------------------------------
public function findProductByItemUser($gender,$page_number, $limit){
    
    return $this->repo->findProductByItemUser($gender,$page_number, $limit);
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
public function findMostLikedProducts($page_number, $limit){
    return $this->repo->findMostLikedProducts($page_number, $limit);
}
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
            $options['limit']=10;
        }
        if(!array_key_exists ('list_type', $options)){
            $options['list_type']="latest";
        }
        
                
            switch ($options['list_type'])
        {
        case "latest":        
        $list = $this->findByGenderLatest($options['gender']);
        break;
    
        case "tried":        
        $list = $this->findMostTriedOnByGender($options['gender']);
        break;
    
        case "recently_tried_on":        
        $list = $this->findRecentlyTriedOnByUser($options['user_id']);
        break;
    
        case "faviourite":        
        $list = $this->findMostLikedByGender($options['gender']);
        break ;
        
        
            
        default:
        $list = $this->findByGenderLatest($options['gender']);
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
    
   public function findMostLikedByGender($gender='F', $page_number=0, $limit=0) {
        return $this->repo->findByGenderMostLiked($gender, $page_number, $limit);        
   } 
    
    #---------------------------------------------------
    #---------WROK WILL BE DONE I FUTURE!!!!!!!!!!!!!!!!!!!!!!!!!!!----#
    public function getDefaultFittingAlerts($product_id)
    {       
        // find product
        $product = $this->repo->find($product_id);        
      if($product){
        $product_color = $product->getDisplayProductColor();
        $product_color_id = $product_color->getId();        
         //get color size array, sizes that are available in this color         
        $color_sizes_array = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductColor')
                ->getSizeArray($product_color_id);
        $size_id = null;        
         // find size id is not in param gets the first size id for this color      
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
       $product_size_id = $size_id;
        $product_size = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($product_size_id);
        //2) color & size can get an item
        if ($product_size && $product_color) {
            $product_item = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductItem')
                    ->findByColorSize($product_color->getId(), $product_size->getId());
            
          return  $item_id=$product_item->getId();
        }     
        }//End of If   
    }         
#-----------------------------Web Service-------------------------------------------------------------------------#
#------------------User Favourite List-----------------------------------------#
public function favouriteByUser($user_id,$request){
       
    if(count($this->repo->favouriteByUser($user_id))>0){
    $device_path=$this->getDeviceTypeByUser($user_id);   
   
    $data=$this->repo->favouriteByUser($user_id);
    $count=1;
    foreach($data as $ind){
        $data_value['data'][$count]['id']=$ind['product_id'];
        $data_value['data'][$count]['item_id']=$ind['id'];
        $data_value['data'][$count]['name']=$ind['name'];
        $data_value['data'][$count]['target']=$ind['target'];
        $data_value['data'][$count]['product_image']=$ind['product_image'];
        $data_value['data'][$count]['title']=$ind['title'];
        $data_value['data'][$count]['fitting_room_image']=$ind['fitting_room_image'];
        $data_value['data'][$count]['description']=$ind['description'];
        
    $count++;    
    }
    
    $data_value['fitting_room_path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path;
    $data_value['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
    return $data_value;}else{
        return $data['message']="There is no Favourite list ";
    }
}    

#---------Web Service for product listing -----------#
 public function productListWebService($request,$request_array){
        
        $id = $request_array['id'];
        $type = $request_array['type'];
        $gender = $request_array['gender'];
        
       /*$id=6;
       $type='brand';
       $gender='F';*/
        
        $products = Null;
        if ($type == "brand") {
            $products = $this->repo->findProductByBrandWebService($id, $gender);
        }
        if ($type == "clothing_type") {
            $products = $this->repo->findProductByClothingTypeWebService($id, $gender);
        }
        if ($type == "hot") {
            $products = $this->repo->findhottestProductWebService($gender);
        }
        if ($type == "new") {
            $products = $this->repo->findLattestProductWebService($gender);
        }
        $data = array();
       
        #-------Fetching The Path------------#
        if ($products) {
         
         $product_color_array = array();
          $count=1;
          //$product_helper =  $this->get('admin.helper.product');
          foreach ($products as $ind_product) {
                $product_id = $ind_product['id'];
                if ($product_id) {
                    $p = $this->find($product_id);
                    $data['data'][$product_id]['id'] = $ind_product['id'];
                    $data['data'][$product_id]['name'] = $ind_product['name'];
                    $data['data'][$product_id]['description'] = $ind_product['description'];
                    $data['data'][$product_id]['target'] = $ind_product['target'];
                    $data['data'][$product_id]['product_image'] = $ind_product['product_image'];
                    $item = $p->getDefaultItem();
                    if ($item) {
                        $data['data'][$product_id]['fitting_room_image'] = $item->getImage();
                    }
                }
            }   
           
            //$data[] = $products;
           
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/';
            $data['fitting_room_path'] = $fitting_room;
            $total_record = count($products);

            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $data['path'] = $baseurl;
            return $data;
        } else {
            return array('Message' => 'We can not find Product');
        }
     
 }
 #--------------------Product Detail Web Service -----------------------------------------------------------#
 public function productDetailWebService($request,$request_array){
       $product_id = $request_array['id'];
        $user_id= $request_array['user_id'];
      /*$user_id=1;
       $product_id=3;*/
       if(!$user_id)
       {
            return  array('Message' => 'User Missing');
       }    
        
        $productdetail = array();
        $products = $this->repo->productDetail($product_id);
        
        $product = $this->repo->find($product_id);
        
        $user_helper = $this->container->get('user.helper.user');
        $product_color_helper = $this->container->get('admin.helper.productcolor');
         
        $user = $user_helper->find($user_id);
        $user_re = new User();
        
        
        $count_rec = count($products);
        $productdetail['product'] = $products;
        
        $product_color_array = array();

        #-- FOR COLORS AND SIZE----------#
        if ($count_rec > 0) {

            $product_colors = $product->getProductColors();
            $product_size_id = null;
            $size_id = null;
            foreach ($product_colors as $product_color_value) {
                $product_color_id = $product_color_value->getId();

                $color_sizes = $product_color_helper->getSizeItemImageUrlArray($product_color_id);

                $color_size_array = array();
                $counter=1;
                foreach ($color_sizes as $cs) {
                    $color_size_array [$cs['title']] = $cs;
                    $like_status['like_status']=$user->getMyClosetListArray($cs['id']);
                   // $item_id['item_id']=$cs['item_id'];
                    array_push($color_size_array [$cs['title']],$like_status);
                    $counter++;
                }
                

     
                $product_color_array[$product_color_value->gettitle()] = array(
                    'id' => $product_color_value->getId(),
                    'image' => $product_color_value->getImage(),
                    'pattern' => $product_color_value->getPattern(),
                    'title' => $product_color_value->getTitle(),
                    'sizes' => $color_size_array,
                );
            }
            $productdetail['product_color'] = $product_color_array;
            $data = array();
            $data['data'] = $productdetail;
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/';
            $pattern = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/';

            $data['product_color_path'] = $baseurl;
            $data['fitting_room_path'] = $fitting_room;
            $data['pattern_path'] = $pattern;

            return  $data;
        } else {
            return array('Message' => 'Record Not Found');
        }
}
#--------------User Try History Web Service--------------------------------------------------#
public function getUserTryHistoryWebService($request,$user_id){
        if($user_id)
        {
        
        $entity = $this->repo->tryOnHistoryWebService($user_id);
        $data=array();
        $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/';
        $data['data']=$entity;
        $data['image_path']=$fitting_room;
        $count_rec=count($entity);
        if($count_rec>0)
        {
            return $data;
        }else{
            return array('Message'=>'History not found.');
        }
        
        }else{
           return array('Message' => 'User Missing'); 
        }
}

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
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
        } else {
// if gets the size id in params,  check if this size is available in this color, if not get the first one
            foreach ($color_sizes_array as $csa) {
                if ($csa['id'] == $product_size_id) {
                    $size_id = $csa['id'];
                }
            }
            if ($size_id == null) {
// gets the first size id for this color
                $psize = array_shift($color_sizes_array);
                $size_id = $psize['id'];
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

}