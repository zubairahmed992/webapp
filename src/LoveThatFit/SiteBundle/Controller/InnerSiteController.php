<?php
namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Dumper;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\SiteBundle\Cart;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\SiteBundle\Entity\UserItemTryHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\SiteBundle\AvgAlgorithm;

class InnerSiteController extends Controller {
      var $compare = array();
#-------------------------------------------------------------------------------
public function indexAction($list_type) {
    
        return $this->render('LoveThatFitSiteBundle:InnerSite:index.html.twig', array(
            'list_type'=>$list_type,
           ));
 }
 
#-------------------------------------------------------------------------------
public function shopifyIndexAction($sku=null,$user_id=null,$retailer=null) {
        $user = $this->get('security.context')->getToken()->getUser();
        $session = $this->get("session");
        if ($sku == null && $session->has('shopify_user')) {                  
                  $sku = $session->get('sku');                  
              } 
        $itemBySku=$this->get('admin.helper.productitem')->findItemBySku($sku); 
        
          if ($itemBySku == null || empty($itemBySku)|| !isset($itemBySku)){
            $retailer=null;
            }else{
                
            }
            $fitting_room_items=$this->get('site.helper.userfittingroomitem')->add($user,$itemBySku);
        //$retailer=  is_object($itemBySku)?$itemBySku->getProduct()->getRetailer():null;
        return $this->render('LoveThatFitSiteBundle:InnerSite:shopify_index.html.twig', array(
            'item'=>$itemBySku,
            'retailer'=>$retailer,
            'list_type'=>null,            
            'items'=>$fitting_room_items,
           ));
 }
 #-------------------------------------------------------------------------------
public function shopifyAfterLoginAction($sku=null,$user_id=null) {
        
        $user = $this->get('security.context')->getToken()->getUser();
        $session = $this->get("session");
        if ($session->has('shopify_user')) {
                  $shopify_user = $session->get('shopify_user');                  
                  $sku = $session->get('sku');
                  $retailer = $this->get('admin.helper.retailer')->find($shopify_user['retailer_id']);
                  $this->get('admin.helper.retailer.site.user')->addNew($user, $shopify_user['site_user_id'],$retailer);
                  
              } 
        $itemBySku=$this->get('admin.helper.productitem')->findItemBySku($sku);              
        $retailer=  is_object($itemBySku)?$itemBySku->getProduct()->getRetailer():null;
        $fitting_room_items=$this->get('site.helper.userfittingroomitem')->add($user,$itemBySku);
        return $this->render('LoveThatFitSiteBundle:InnerSite:shopify_index.html.twig', array(
            'item'=>$itemBySku,
            'retailer'=>$retailer,
            'items'=>$fitting_room_items,
            'list_type'=>null,            
           ));
 }
#-------------------------------------------------------------------------------
 public function homeAction($page_number = 0, $limit = 0) {
       $gender= $this->get('security.context')->getToken()->getUser()->getGender();
       $user_id= $this->get('security.context')->getToken()->getUser()->getId();
       $latest = $this->get('admin.helper.product')->listByType(array('limit'=>5, 'list_type'=>'latest'));
        if (count($this->get('admin.helper.product')->listByType(array('limit' => 3, 'list_type' => 'most_faviourite'))) > 0) {
            $favourite = $this->get('admin.helper.product')->listByType(array('limit' => 3, 'list_type' => 'most_faviourite'));
        } else {
            $favourite = $this->get('admin.helper.product')->findByGenderRandom('F', 3);
        }
        if (count($this->get('admin.helper.product')->listByType(array('limit' => 3, 'list_type' => 'most_tried_on'))) > 0) {
            $tried_on =$this->get('admin.helper.product')->listByType(array('limit' => 3, 'list_type' => 'most_tried_on'));
        } else {
            $tried_on =$this->get('admin.helper.product')->findByGenderRandom('F', 3);
        }
       $recomended = $this->get('admin.helper.product')->findByGenderBrandName($gender,'H&M' ,$page_number, $limit);        
      // var_dump($favourite);
    //   return new response($favourite);
       return $this->render('LoveThatFitSiteBundle:InnerSite:home.html.twig', array(
            'latest'=>$latest,
            'tried_on'=>$tried_on,
            'favourite'=>$favourite,
            'recomended'=>$recomended,            
           ));
    }
#------------------------------Product Slider-----------------------------------
    public function productsByTypeAction($list_type='latest', $page_number = 0, $limit = 0) {       
        if ($list_type == 'ltf_recommendation' || $list_type == 'most_faviourite') {
            $entity = null;
            return $this->renderProductTemplate($entity, $page_number, $limit, 'Coming Soon');       
        } else  {
            $user_id = $this->get('security.context')->getToken()->getUser()->getId();
            $gender = $this->get('security.context')->getToken()->getUser()->getGender();
            $options = array('gender' => $gender, 'user_id' => $user_id, 'list_type' => $list_type, 'page_number' => $page_number, 'limit' => $limit);
            $entity = $this->get('admin.helper.product')->listByType($options);
            return $this->renderProductTemplate($entity, $page_number, $limit);
        }
    }
    
#-------------------------------------------------------------------------------

    public function fittingRoomProductsListAction($list_type='recently_tried_on', $page_number = 0, $limit = 0)
    {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $gender = $this->get('security.context')->getToken()->getUser()->getGender();
        $options = array('gender' => $gender, 'user_id' => $user_id, 'list_type' => $list_type, 'page_number' => $page_number, 'limit' => $limit);
        $entity = $this->get('admin.helper.product')->listByType($options);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products_short.html.twig', array('products' => $entity, 'page_number' => $page_number, 'limit' => $limit, 'row_count' => count($entity)));
    }


#-------------------------------------------------------------------------------
    public function productsAction($gender, $page_number = 0, $limit = 0) {
        $entity=$this->get('admin.helper.product')->findByGender($gender, $page_number, $limit); 
         return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#----------------------------------- Whats New----------------------------------
    public function productsLatestAction($gender, $page_number = 0, $limit = 0) {
         $entity=$this->get('admin.helper.product')->findByGenderLatest($gender, $page_number, $limit); 
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#------------------------------------------------------------------------------- 
    public function productsHotestAction($gender, $page_number = 0, $limit = 0) {
        $entity = $this->get('admin.helper.product')->findMostTriedOnByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#------------------------------------------------------------------------------- 
    public function productsRecomendedAction($gender, $page_number = 0, $limit = 0) {
        $entity = $this->get('admin.helper.product')->findMostTriedOnByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#------------------------------------------------------------------------------- 
    public function productsRecentlyTriedOnByUserAction($page_number = 0, $limit = 0) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.product')->findRecentlyTriedOnByUser($user_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#--------- Shifted to external ---------------------------------------------------------------------- 
    public function productsRecentlyTriedOnByUserForRetailerAction($retailer_id, $page_number = 0, $limit = 0) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.product')->findRecentlyTriedOnByUserForRetailer($retailer_id, $user_id);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }    
#-------------------------------------------------------------------------------  
    
    public function userFittingRoomItemRemoveAction($user_id, $item_id) {
        $this->get('site.helper.userfittingroomitem')->deleteByUserItem($user_id, $item_id);
        return new Response('true');
    }        
    
    #-------------------------------------------------------------------------------
    public function ajaxAction($id=0) {
        $user= $this->get('security.context')->getToken()->getUser();
        $fris  = $this->get('site.helper.userfittingroomitem')->add($user, $id);
        return new Response(json_encode($fris));
    }

#-------------------------------------------------------------------------------  
    public function productsMostFavoriteAction($gender, $page_number = 0, $limit = 0) {
        $user_id= $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.productitem')->findProductItemByUser($user_id,$gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    
        //$user= $this->get('security.context')->getToken()->getUser();
        //$entity = $this->get('admin.helper.product')->findMostFavoriteProducts($user->getGender(), $page_number, $limit);
        //return $this->renderProductTemplate($entity, $page_number, $limit);
    }    
    
#------------------------------------------------------------------------------- 
    public function productsLTFRecommendationAction($gender, $page_number = 0, $limit = 0) {
        $entity = null;
        return $this->renderProductTemplate($entity, $page_number, $limit, 'Coming Soon');
    }
#----------------------------------- by Brand-----------------------------------
    public function productsByBrandAction($gender, $brand_id, $page_number = 0, $limit = 0) {
        $entity = $this->get('admin.helper.product')->findByGenderBrand($gender, $brand_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#----------------------------------- By Clothing Type---------------------------
    public function productsByClothingTypeAction($gender, $clothing_type_id, $page_number = 0, $limit = 0) {
        $entity = $this->get('admin.helper.product')->findByGenderClothingType($gender, $clothing_type_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
#------------------------------------------- render method ---------------------
    private function renderProductTemplate($entity, $page_number, $limit, $status=null) {
            if (count($entity)==0) return new Response('Products are currently not available');
            return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity, 'page_number' => $page_number, 'limit' => $limit, 'row_count' => count($entity), 'functionality_status' => $status));
    }
#----------------------------------- Sample Clothing Type-----------------------
    public function productsClothingTypeAction($gender) {
        $entity = $this->get('admin.helper.product')->findSampleClothingTypeGender($gender);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }
#--------------------------------------- List Clothing Types--------------------
    public function clothingTypesAction() {
        $entity = $this->get('admin.helper.clothingtype')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_clothingTypes.html.twig', array('clothing_types' => $entity));
    }
#---------------------- List Brands---------------------------------------------
    public function brandsAction() {
        $entity =  $this->get('admin.helper.brand')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_brands.html.twig', array('brands' => $entity));
    }
#----------------------------------- Product Detail-----------------------------
    public function productDetailAction($id, $product_color_id, $product_size_id) {
        $product_detail= $this->get('admin.helper.product')->productDetail($id, $product_color_id, $product_size_id);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_new_product_detail.html.twig', array('product' => $product_detail['product'],
                    'productColor' => $product_detail['product_color'],
                    'productSize' => $product_detail['product_size'],
                    'productItem' => $product_detail['product_item'],
        ));
  }
#-------------------------------------------------------------------------------
/*    public function productsMostLikedAction($page_number = 0, $limit = 0) {       
        $entity =  $this->get('admin.helper.product')->findMostLikedProducts($page_number, $limit);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_most_liked_products.html.twig', array('product' => $entity));
    }
 * 
 */
#-------------------------------------------------------------------------------
    public function productsByMyClosetAction($page_number = 0, $limit = 0) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.product')->findProductItemByUser($user_id, $page_number, $limit);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
    }
#-------------------------------------------------------------------------------    
    public function productFriendsFavouritesAction($page_number = 0, $limit = 0){
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity =  $entity = $this->get('admin.helper.product')->findProductItemByUser($user_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);        
    }
#-------------------------------------------------------------------------------
    public function countMyColosetAction() {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get('admin.helper.product')->countMyCloset($user_id);
        $rec_count = count($this->get('admin.helper.product')->countMyCloset($user_id));
        return new Response($rec_count);
    }

//------------------------------------------------------------------------------
    public function deleteMyClosetAction($id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $product_item = $this->get('admin.helper.productitem')->getProductItemById($id);
        $em = $this->getDoctrine()->getManager();
        $product_item->removeUser($user);
        $user->removeProductItem($product_item);
        $em->persist($product_item);
        $em->persist($user);
        $em->flush();
        $this->get('session')->setFlash('success', 'Product Item Successfully Removed.');
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity =$this->get('admin.helper.product')->findProductItemByUser($user_id,$page_number=0,$limit = 0);
       return $this->redirect($this->generateUrl('ajax_products_by_my_closet',array('product' => $entity)));
        //return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
    }
 #-----------------------------Delete My Closet at For Ajax---------------------
    public function deleteMyClosetAjaxAction($product_item_id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $product_item = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        $em = $this->getDoctrine()->getManager();
        $product_item->removeUser($user);
        $user->removeProductItem($product_item);
        $em->persist($product_item);
        $em->persist($user);
        $em->flush();
       return new response('success');   
    }
 //-----------------------------------------------------------------------------
    public function emailAction($id) {
        $product = $this->get('admin.helper.product')->find($id);
         $session = $this->get("session");
    }
#-------------------------------------------------------------------------------
    
    public function getFeedBackJSONAction($user_id, $product_item_id, $type=null) {
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser())) return new Response("User Not found, Log in required!");
        if (!$productItem) return new Response("Product not found!");
        $product_size = $productItem->getProductSize();
        $product=$productItem->getProduct();
        
        if ($type==null || $type=='low-high'){
            $comp = new Comparison($user,$product);
            $fb=$comp->getSizeFeedBack($product_size);
        }elseif ($type=='avg'){
            $comp = new AvgAlgorithm($user,$product);
            $fb=$comp->getSizeFeedBack($product_size);
        }
        
        #return new Response(json_encode($fb));  
        $fits=$fb['feedback']['fits'];        
        $json_feedback=  json_encode($fb['feedback']);
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $json_feedback, $fits);    
        #$this->get('site.helper.userfittingroomitem')->createUserFittingRoomItem($user,$productItem);    
        $this->get('site.helper.userfittingroomitem')->add($user,$productItem);    
//$this->get('site.helper.userfittingroomitem')->createNew();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $fb));
        
    }
    #--------------------------------------------------------
    //
    public function removeFittingRoomItemAction($user_id, $item_id){
      $t =  $this->get('site.helper.userfittingroomitem')->deleteFittingRoomItem($user_id,$item_id);    
      return new Response($t);
    }
    #--------------------------------------------------------
    //
    public function getFittingRoomItemIdsAction($user_id){
        $user =  $this->get('user.helper.user')->find($user_id);    
      $t =  $this->get('site.helper.userfittingroomitem')->getItemIdsArrayByUser($user);    
      return new Response(json_encode($t));
    }
#-------------------------------------------------------------------------------
    /*
    public function getFeedBackListAction($product_item_id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser()))
            return new Response("User Not found, Log in required!");
        if (!$productItem)
            return new Response("Product not found!");
        $fit = new Algorithm($user, $productItem);
        $json_feedback = $fit->getFeedBackJson();
        $fits = $fit->fit();
        $product_id=$this->get('admin.helper.productitem')->getProductByItemId($productItem);
        $product_id=$product_id[0]['id'];        
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product_id, $productItem, $json_feedback, $fits);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', array('product' => $productItem->getProduct(), 'product_item' => $productItem, 'data' => $fit->getFeedBackArray()));
    }
    
    public function getFeedBackListAction($product_item_id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser())) return new Response("User Not found, Log in required!");
        if (!$productItem) return new Response("Product not found!");
        
        $fit = new FitEngine($user,$productItem);
        $bfb = $fit->getBasicFeedback();
        
        $product=$productItem->getProduct();
        $fits=$bfb['fits'];        
        $json_feedback=  json_encode($bfb);
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $json_feedback, $fits);    
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $bfb));
    }*/
    
     public function getFeedBackListAction($product_item_id) {         
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser())) return new Response("User Not found, Log in required!");
        if (!$productItem) return new Response("Product not found!");
        
        
        $product_size = $productItem->getProductSize();
        $product=$productItem->getProduct();
        #$comp = new Comparison($user,$product);
        #$fb=$comp->getSizeFeedBack($product_size);
        $comp = new AvgAlgorithm($user,$product);
        $fb=$comp->getSizeFeedBack($product_size);
        #return new Response(json_encode($fb['feedback']['fits']));  
        
                
        $fits=$fb['feedback']['fits'];        
        $json_feedback=  json_encode($fb['feedback']);
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $json_feedback, $fits);    
        #$this->get('site.helper.userfittingroomitem')->createUserFittingRoomItem($user,$productItem);    
        $this->get('site.helper.userfittingroomitem')->add($user,$productItem);    
        // $this->get('site.helper.userfittingroomitem')->createNew();    
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $fb));
    }
#-------------------------------------------------------------------------------
    public function addToCloestAction($product_item_id) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity =  $this->get('admin.helper.product')->countMyCloset($user_id);
        $rec_count = count($entity );
        if ($rec_count >= 25) {
            $this->get('session')->setFlash('warning', 'Please Remove Some Like You can not like more than 25.');
            return new response(0);
        } else {
            $user = $this->get('security.context')->getToken()->getUser();
            $product_item = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
            $em = $this->getDoctrine()->getManager();
            $product_item->addUser($user);
            $user->addProductItem($product_item);
            $em->persist($product_item);
            $em->persist($user);
            $em->flush();
            return new response('success');
        }
    }
    
    public function userMannequinAction()
    {
        $user = $this->get('security.context')->getToken()->getUser(); 
        $manequin_size=$this->get('admin.helper.user.mannequin')->userMannequin($user);        
        return new Response(json_encode($manequin_size));
    }

#------------------------------------------Compare Product -------------------#
   public function compareProductAction($item_id){
       $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);
       $product=$productItem->getProduct();
      
       $totalPro=count($this->addNewProducts($product));
      
       if($totalPro<=3){
        return $this->render('LoveThatFitSiteBundle:InnerSite:compareProduct.html.twig',
        array('product' => $this->addNewProducts($product)));
     }else{
           $this->removeSession();
           return $this->render('LoveThatFitSiteBundle:InnerSite:compareProduct.html.twig',
           array('product' => $this->addNewProducts($product),));
        } 
   }
   
   //---------------------------------------------------------------------
    private function addNewProducts($product) {
     $session = $this->get("session");
     $user = $this->get('security.context')->getToken()->getUser();
     $compare=array();
     if($session->has('product')){
         $compare=$session->get("product");
     }  
     
     $fe = new AvgAlgorithm($user, $product);
     $compare[$product->getId()]=array(
            'product'=>$product->getDetailArray()+$fe->getFeedBack()
    );
    
    $session->set("product", $compare);
    return $compare;
    
     
    }
   #-------------------- Get Session of product ----------------------------#
     private function getProductSession() {
          $compare=array();
        $session = $this->get("session");
        if ($session->has('product')) {
            $product = $compare;
        } else{
            $product = null;
        }
        return $product;
    }
    
  #-----------------------Remove Session -------------------------------#
  public function removeSession(){
      $session = $this->get("session");
      $session->remove('product');
      return  array('status'=>true);
  }
    
 
}
?>

