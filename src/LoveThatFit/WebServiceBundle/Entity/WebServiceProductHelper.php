<?php

namespace LoveThatFit\WebServiceBundle\Entity;
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
use LoveThatFit\SiteBundle\Comparison;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;

class WebServiceProductHelper{

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
    
#------------------------------------------------------------------------------#
public function find($id) {
        return $this->repo->find($id);
    }
#------------------------------------------------------------------------------#
    public function findByGender($gender, $page_number, $limit){
      return $this->repo->findByGender($gender, $page_number, $limit);  
    }
#------------------------------------------------------------------------------#
 public function findOneByName($brand){
    return $this->repo->findOneByName($brand);
}   

#--------------------------------For Love/Unlove Item--------------------------#
public function loveItem($request_array){
    
    $user_id = $request_array['userId'];
    $product_item_id = $request_array['productItemId'];
    $user_helper = $this->container->get('user.helper.user');
    $product_item_helper = $this->container->get('admin.helper.productitem');
      
       if ($user_id && $product_item_id) {
        
        if ($request_array['like']==trim('like')) {
                
                $entity=$this->countMyCloset($user_id);
                $rec_count = count($entity);

                if ($rec_count >= 25) {
                        return array('Message' => 'Please delete some products (limit exceeds)');
                } else {
                    
                    $user =  $user_helper->find($user_id);
                    $product_item = $product_item_helper->getProductItemById($product_item_id);
                    $product_item->addUser($user);
                    $user->addProductItem($product_item);
                    $product_item_helper->save($product_item);
                    $user_helper->saveUser($user);
                    //$em->persist($product_item);
                    //$em->persist($user);
                    //$em->flush();
                    return array('Message' => 'Item has been successfully liked!');
                }
            } else {

                $user=$user_helper->find($user_id);
                $product_item = $product_item_helper->getProductItemById($product_item_id);
                $product_item->removeUser($user);
                $user->removeProductItem($product_item);
                $product_item_helper->save($product_item);
                $user_helper->saveUser($user);
               // $em->flush();
                return array('Message' => 'Item has been successfully unliked!');
            }
        } else {
            return array('Message' => 'User/Item Missing');
        }
}
#----------------Check the recommended Item -----------------------------------#
public function chkRecomendedItem($feedback){
    //return $feedback;
   if($feedback){       
       foreach($feedback['feedback'] as $fb){
           if(isset($fb['recommended']) && $fb['recommended']){
               return $fb['id'];
           }else{
               if(min($feedback['feedback'])){
                   return $fb['id'];
               }
           }
       }    
   }else{
       return false;
   }
}
#------------------------------------------------------------------------------#
// chk recomend itm
// smallest item default color 
    public function getDefaultFittingAlerts($request_array) {
        if ($request_array['productId']) {
            $user = $this->container->get('user.helper.user')->find($request_array['userId']);
            $product = $this->find($request_array['productId']);
            $algo = new FitAlgorithm2($user, $product);
            $fb = $algo->getFeedBack();         
            $stripped_fb = $algo->stripFeedBack($fb);            
            $default_size_fb=array();
            $default_size_fb['feedback'] = FitAlgorithm2::getDefaultSizeFeedback($fb);
            
            if (!$default_size_fb) {
                return (array('Message' => ' Product Can not find'));
            }
    
            $product_item = $product->displayProductColor->getItemBySizeId($default_size_fb['feedback']['id']);
            $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user, $product->getId(), $product_item, $default_size_fb);

            $data = array();

            $data['data'] = $stripped_fb;
            $data['productId'] = $request_array['productId'];            
            return $data;
        } else {
            return (array('Message' => ' Product Can not find'));
        }
    }

    public function _getDefaultFittingAlerts($request_array)
{      
          if ($request_array['productId']) { 
              $user=$this->container->get('user.helper.user')->find($request_array['userId']);
              $product=$this->find($request_array['productId']);
              $fit = new AvgAlgorithm($user, $product);
                  //return $fit->getStrippedFeedBack();
              $product_item_id=$this->chkRecomendedItem($fit->getStrippedFeedBack());
              //return $product_item_id;
              if(!$product_item_id){
                 return (array('Message' => ' Product Can not find'));
              }
              $product_size = $this->container->get('admin.helper.productsizes')->find($product_item_id);
              $productItem = $product->displayProductColor->getItemBySizeId($product_item_id);
              
              #$productItem = $this->container->get('admin.helper.productitem')->getProductItemById($product_item_id);
              #$product_size = $productItem->getProductSize();
              
              $comp = new AvgAlgorithm($user,$product);
              $fb=$comp->getSizeFeedBack($product_size);
     $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $fb);    
          $data = array();
         
          $data['data'] = $fit->getStrippedFeedBack();
          $data['productId']=$request_array['productId'];
          return $data;
       }
            else {
                return (array('Message' => ' Product Can not find'));
            }
        
    } 
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
    
    $data_value['fitting_room_path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
    $data_value['path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone_list/';
    return $data_value;}else{
        return $data['Message']="No such products found";
    }
}    

#--------------Web Service for product listing --------------------------------#
 public function productListWebService($request,$request_array){
        
        //$id = $request_array['id'];
        $type = $request_array['type'];
       // $gender = $request_array['gender'];
     
        if($request_array['authTokenWebService']){
           
        $user=$this->container->get('user.helper.user')->findByAuthToken($request_array['authTokenWebService']);
       // $device_path=$this->getDeviceTypeByUser($user->getId());   
        }
        $gender=$user->getGender();
      /* $brandId=5;
       $type='brand';
       $gender='F';*/
        
        $products = Null;
        if ($type == "brand") {
            $brandId=$request_array['brandId'];
            $products = $this->repo->findProductByBrandWebService($brandId, $gender);
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
                    $data['data'][$product_id]['productId'] = $ind_product['id'];
                   // $data['data'][$product_id]['name'] = $ind_product['name'];
                   // $data['data'][$product_id]['description'] = $ind_product['description'];
                   // $data['data'][$product_id]['target'] = $ind_product['target'];
                   // $data['data'][$product_id]['product_image'] = $ind_product['product_image'];
                    //$item = $p->getDefaultItem();
                    //if ($item) {
                      //  $data['data'][$product_id]['fitting_room_image'] = $item->getImage();
                    //}
                }
            }   
           
            //$data[] = $products;
           
            return $data;
        } else {
            return array('Message' => 'We cannot find Product');
        }
     
 }
 
 
 
 #--------------------Product list with  Detail data Web Service --------------------------------#
 public function newproductListingWebService($request,$request_array,$date_format=Null){
       // $id = $request_array['id'];
       
        if($request_array['authTokenWebService']){
        $user=$this->container->get('user.helper.user')->findByAuthToken($request_array['authTokenWebService']);
        $device_path=$this->getDeviceTypeByUser($user->getId());   
        }
        
     
      /* $id=1;
       $type='brand';
       $gender='F';*/
         //$gender='F';
        if(!$user){
           return array('Message' => 'We cannot find User');  
            
        }
        $gender = $user->getGender();
        if(isset($request_array['date'])){
          $date_format=$this->returnFormattedTime($request_array);
        }
       // return $date_format;
       $products = $this->repo->newproductListingWebService($gender,$date_format);
      
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
                    $data['data'][$product_id]['productId'] = $ind_product['id'];
                    $data['data'][$product_id]['name'] = $ind_product['name'];
                    $data['data'][$product_id]['description'] = $ind_product['description'];
                    $data['data'][$product_id]['target'] = $ind_product['target'];
                    $data['data'][$product_id]['clothingType'] = $ind_product['clothing_type'];
                    $data['data'][$product_id]['productImage'] = $ind_product['product_image'];
                    $data['data'][$product_id]['brandName'] = $ind_product['brand_name'];
                    $data['data'][$product_id]['brandId'] = $ind_product['brandId'];
                    $data['data'][$product_id]['retailer'] =$this->container->get('admin.helper.brand')->getRetailerTitleByBrandId($ind_product['brandId']);
                    
                    #This has been changed Temporarly to support (get the feedback using old algo) old algorithm for the device
                    #$item = $p->getDefaultItem($user);
                    $item = $p->getDefaultItemForDevice($user);
                      if (isset($item)) {
                        $data['data'][$product_id]['fittingRoomImage'] = $item->getImage();
                        $data['data'][$product_id]['sizeId'] = $item->getProductSize()->getId();
                        $data['data'][$product_id]['colorId'] = $item->getProductColor()->getId();
                        $data['data'][$product_id]['sizeTitle'] = $item->getProductSize()->getTitle();
                        $data['data'][$product_id]['colorTitle'] = $item->getProductColor()->getTitle();
                        }
                     
                    
 
               
                
                }
            }   
           
          
  return $data;
        } else {
            return array('Message' => 'We cannot find Product');
        }
     
}

 public function productListingForUserSync($request,$request_array,$date_format=Null){
       // $id = $request_array['id'];
       
        if($request_array['authTokenWebService']){
        $user=$this->container->get('user.helper.user')->findByAuthToken($request_array['authTokenWebService']);
        $device_path=$this->getDeviceTypeByUser($user->getId());   
        }
        
       if(!$user){
           return array('Message' => 'We cannot find User');  
            
        }
        $gender = $user->getGender();
        if(isset($request_array['date'])){
          $date_format=$this->returnFormattedTime($request_array);
        }
       // return $date_format;
       $products = $this->repo->newproductListingWebService($gender,$date_format);
      
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
                    $data['data'][$product_id]['productId'] = $ind_product['id'];
                    $data['data'][$product_id]['target'] = $ind_product['target'];
                    $item = $p->getDefaultItem($user);
                      if (isset($item)) {
                        $data['data'][$product_id]['fittingRoomImage'] = $item->getImage();
                        $data['data'][$product_id]['sizeId'] = $item->getProductSize()->getId();
                        $data['data'][$product_id]['colorId'] = $item->getProductColor()->getId();
                        $data['data'][$product_id]['sizeTitle'] = $item->getProductSize()->getTitle();
                        }
                }
            }   
           
          
  return $data;
        } else {
            return array('Message' => 'We cannot find Product');
        }
     
}



#----------This is used for saving db all product id with append of detail 
public function newproductDetailWebService($request,$request_array,$date_format=Null){
       
        if($request_array['authTokenWebService']){
        $user=$this->container->get('user.helper.user')->findByAuthToken($request_array['authTokenWebService']);
         $user_id=$user->getId();
        }
      if(!$user_id){
            return  array('Message' => 'User Missing');
       }    
        $user_helper = $this->container->get('user.helper.user');
        $product_color_helper = $this->container->get('admin.helper.productcolor');
        $productdetail = array();
        $gender = $user->getGender();
          $data=array();
        if(isset($request_array['date'])){
          $date_format=$this->returnFormattedTime($request_array);
        }
       
        $data['data'] = $this->repo->newproductDetailDBStructureWebService($gender,$date_format);
       if($data['data']){
        return $data;
        
        }else{
            return array('Message' => 'We cannot find Product'); 
        }
    /*   $product_detail = array();
        $user = $user_helper->find($user_id);
        $user_re = new User();
        $count_rec = count($products);
        $product_color_array = array();
        if ($products) {
          foreach ($products as $ind_product) {
                $product_id = $ind_product['id'];
            if ($product_id) {
                 
                 $p = $this->find($product_id);
                 $product_colors = $p->getProductColors();
                 $product_size_id = null;
                 $size_id = null;
                    foreach ($product_colors as $product_color_value) {
                      $product_color_id = $product_color_value->getId();
                      $color_sizes = $product_color_helper->getSizeItemImageUrlArray($product_color_id);
                      $color_size_array = array();
                      $counter=1;
                     
                       foreach ($color_sizes as $cs) {
                            $color_size_array[$cs['sizeId']]= $cs;
                            $like_status['likeStatus']=$user->getMyClosetListArray($cs['sizeId']);
                            array_push($color_size_array [$cs['sizeId']],$like_status);
                              $counter++;
                            }
                            
                  $product_color_array[$ind_product['name']] = array(
                            'colorId' => $product_color_value->getId(),
                            'image' => $product_color_value->getImage(),
                            'pattern' => $product_color_value->getPattern(),
                            'title' => $product_color_value->getTitle(),
                            'sizes' => $color_size_array,
                        );
                      
                    }
                        
                 
           }
                
            }  
            
            } 
      
            $device_path=$this->getDeviceTypeByUser($user_id);   
           // $productdetail['data'] = $product_color_array;
            $data = array();
            $data['data'] = $product_color_array;
         //   $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
          //  $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
          //  $pattern = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';

            //$data['productColorPath'] = $baseurl;
            //$data['fittingRoomPath'] = $fitting_room;
            //$data['patternPath'] = $pattern;

            return  $data;*/
        
}

#--------------------Product Detail Web Service --------------------------------#
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
                $color_size_array[$cs['id']]= $cs;
              // $color_size_array[$cs['body_type']]= $cs;
                //$color_size_array[$cs['body_type']] = $cs['body_type'];
                $like_status['like_status']=$user->getMyClosetListArray($cs['id']);
                   // $item_id['item_id']=$cs['item_id'];
                    array_push($color_size_array [$cs['id']],$like_status);
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
            //Fetching the Device type 
           $device_path=$this->getDeviceTypeByUser($user_id);   
            $productdetail['product_color'] = $product_color_array;
            $data = array();
            $data['data'] = $productdetail;
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
           // $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
            $pattern = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';

            $data['product_color_path'] = $baseurl;
            $data['fitting_room_path'] = $fitting_room;
            $data['pattern_path'] = $pattern;

            return  $data;
        } else {
            return array('Message' => 'Record Not Found');
        }
}
#--------------User Try History Web Service------------------------------------#
public function getUserTryHistoryWebService($request,$user_id){
        if($user_id)
        {
        
        $entity = $this->repo->tryOnHistoryWebService($user_id);
        $device_path=$this->getDeviceTypeByUser($user_id);   
        if(!$device_path)
        $data=array();
       // $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
        $data['data']=$entity;
        //$data['image_path']=$fitting_room;
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

#------------------------Private Methods---------------------------------------#
#-------------------------------------Count My Closet--------------------------#
private function countMyCloset($user_id){
    return $this->repo->countMyCloset($user_id);
}

#------------------Method For Returning Device Type of Current User------------# 
 private function getDeviceTypeByUser($user_id){
       //  $user_helper = $this->container->get('user.helper.user');
       //  $user=$user_helper->find($user_id);
       //  return $user_device_type=$user->getDeviceType();
 }
#--------------------------Get JSON FEILD--------------------------------------#
 private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
    
 public function imagesUrl($request,$request_array){
     $deviceType=$request_array['deviceType'];
     $data=array();
     if($deviceType=='ipad' ||$deviceType=='ipad_retina'){
     /* 
      $data['brandPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/ipad/';
      $data['retailerPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/retailers/ipad/';
      $data['productDashboardPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/ipad_product_list/';
      $data['productFittingRoomPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/ipad_ftting_room_list/';
     */
        $data['brandPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/iphone/';
        $data['retailerPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/retailers/iphone/';
        $data['productDashboardPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone4s_product_list/';
        $data['productFittingRoomPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone4s_ftting_room_list/';
       } 
      
     if($deviceType=='iphone4s' ||$deviceType=='iPhone4S'||$deviceType=='4s'){
      $data['brandPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/iphone/';
      $data['retailerPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/retailers/iphone/';
      $data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';
      $data['productPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
      $data['productDashboardPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone4s_product_list/';
      $data['productFittingRoomPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone4s_ftting_room_list/';
      
     }
      if($deviceType=='iphone5' ||$deviceType=='iPhone5'||$deviceType=='5'){
      $data['brandPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/iphone/';
      $data['retailerPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/retailers/iphone/';
      $data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';
      $data['productPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
      $data['productDashboardPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone5_product_list/';
      $data['productFittingRoomPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iPhone5_ftting_room_list/';
      
     }
     if($deviceType=="ipad"){
         //$data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/ipad/';
         $data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';
     }
     elseif($deviceType=="ipad_retina"){
         //$data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/ipad_retina/';
         $data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/ipad_retina/';
     }else{
         $data['patternPath'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/iphone/';
     }
     if($deviceType=="ipad"){
         $data['fittingRoomImage'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/iphone4s/';
     }else{
         $data['fittingRoomImage'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$deviceType.'/';
     }
     
     
   //  $data['ipadRetina'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/ipad_retina/';
   //  $data['iphone4s'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/iphone4s/';
   //  $data['iphone5'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/iphone5/';

    return $data;
  } 
  
#-------------GEt Fitting Constant Status--------------------------------------#
  public function getFittingStatus(){
       
        return Comparison::getStatusArray();
          
  }
  
  #----- Set Time Zone Base On User Authenticated Toke -------------------------#
    public function returnFormattedTime($request_array){
         if (array_key_exists('date', $request_array)) {
            $date=$request_array['date'];
            $d = new \DateTime(); 
            $d->setTimestamp($date);
            return $d->format("Y-m-d H:i:s");
        } else{
            return false;
        }
           
            //$product=$this->container->get('webservice.helper.product')->newproductListingWebService($request,$request_array,$format_date);
          //  return json_encode($product);
    }
}
