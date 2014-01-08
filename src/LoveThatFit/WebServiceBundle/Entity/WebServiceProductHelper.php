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
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\ImageHelper;

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
    
    $user_id = $request_array['user_id'];
    $product_item_id = $request_array['product_item_id'];
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
#------------------------------------------------------------------------------#
public function getDefaultFittingAlerts($request_array)
{       $user_id = $request_array['user_id'];
        $product_id = $request_array['product_id'];
        //Calling of Helper 
        $user_helper = $this->container->get('user.helper.user');
        $product_color_helper = $this->container->get('admin.helper.productcolor');
        $product_item_helper = $this->container->get('admin.helper.productitem');
        $product_size_helper = $this->container->get('admin.helper.productsizes');
        $user_try_history_helper=$this->container->get('site.helper.usertryitemhistory');
        // find product
        if ($product_id) {
            $product = $this->repo->find($product_id);
            $product_color = $product->getDisplayProductColor();
            $product_color_id = $product_color->getId();

            //get color size array, sizes that are available in this color 
            $color_sizes_array = $product_color_helper->getSizeArray($product_color_id);
            $size_id = null;
            // find size id is not in param gets the first size id for this color
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
            $product_size_id = $size_id;
            $product_size = $product_size_helper->find($product_size_id);

            //2) color & size can get an item

            if ($product_size && $product_color) {
                $product_item = $product_item_helper->findByColorSize($product_color->getId(), $product_size->getId());
                $product_item_id = $product_item->getId();
            }

            if ($user_id && $product_item_id) {

                $user = $user_helper->find($user_id);
                $productItem = $product_item_helper->getProductItemById($product_item_id);

                if (!$user)
                    return array('Message' => 'User not found');

                if (!$productItem)
                    return array('Message' => 'Product not found');

                $fit = new Algorithm($user, $productItem);
                $data = array();
                $data['data'] = $fit->getFeedBackArray();
                    $json_feedback = $fit->getFeedBackJson();
                $fits = $fit->fit();
                $product_id=$product_item_helper->getProductByItemId($productItem);
                $product_id=$product_id[0]['id'];        
                $user_try_history_helper->createUserItemTryHistory($user,$product_id, $productItem, $json_feedback, $fits);
                return ($data);
            }
            else {
                return (array('Message' => 'Missing User/Item'));
            }
        }//End of If      
        else {
            return json_encode(array('Message' => 'Can not find'));
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
        
        $id = $request_array['id'];
        $type = $request_array['type'];
        $gender = $request_array['gender'];
     
        if($request_array['authTokenWebService']){
           
        $user=$this->container->get('user.helper.user')->findByAuthToken($request_array['authTokenWebService']);
        $device_path=$this->getDeviceTypeByUser($user->getId());   
        }
      /* $id=1;
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
           
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
            $data['fitting_room_path'] = $fitting_room;
            $total_record = count($products);

            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/display/iphone/';
            $data['path'] = $baseurl;
            return $data;
        } else {
            return array('Message' => 'We cannot find Product');
        }
     
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
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
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
        $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/'.$device_path.'/';
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

#------------------------Private Methods---------------------------------------#
#-------------------------------------Count My Closet--------------------------#
private function countMyCloset($user_id){
    return $this->repo->countMyCloset($user_id);
}

#------------------Method For Returning Device Type of Current User------------# 
 private function getDeviceTypeByUser($user_id){
         $user_helper = $this->container->get('user.helper.user');
         $user=$user_helper->find($user_id);
         return $user_device_type=$user->getDeviceType();
 }
#--------------------------Get JSON FEILD--------------------------------------#
 private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
}
