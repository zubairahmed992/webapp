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

class InnerSiteController extends Controller {
#-------------------------------------------------------------------------------
public function indexAction($list_type) {
        return $this->render('LoveThatFitSiteBundle:InnerSite:index.html.twig', array(
            'list_type'=>$list_type,
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
            return $this->renderProductTemplate($entity, $page_number, $limit, 'Comming Soon');
        } else {
            $user_id = $this->get('security.context')->getToken()->getUser()->getId();
            $gender = $this->get('security.context')->getToken()->getUser()->getGender();
            $options = array('gender' => $gender, 'user_id' => $user_id, 'list_type' => $list_type, 'page_number' => $page_number, 'limit' => $limit);
            $entity = $this->get('admin.helper.product')->listByType($options);
            return $this->renderProductTemplate($entity, $page_number, $limit);
        }
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
#-------------------------------------------------------------------------------  
    public function productsMostFavoriteAction($gender, $page_number = 0, $limit = 0) {
        $user_id= $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.product')->findProductItemByUser($user_id,$gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    
        //$user= $this->get('security.context')->getToken()->getUser();
        //$entity = $this->get('admin.helper.product')->findMostFavoriteProducts($user->getGender(), $page_number, $limit);
        //return $this->renderProductTemplate($entity, $page_number, $limit);
    }    
    
#------------------------------------------------------------------------------- 
    public function productsLTFRecommendationAction($gender, $page_number = 0, $limit = 0) {
        $entity = null;
        return $this->renderProductTemplate($entity, $page_number, $limit, 'Comming Soon');
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
        return $this->render('LoveThatFitSiteBundle:InnerSite:_product_detail.html.twig', array('product' => $product_detail['product'],
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
        $entity =$this->get('admin.helper.product')->findProductItemByUser($user_id, $page_number = 0, $limit = 0);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
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
#-------------------------------------------------------------------------------
    public function ajaxAction() {
        $product_item = $this->get('admin.helper.productitem')->getProductItemById(4);
        
        $fe= new FitEngine($this->get('security.context')->getToken()->getUser(),$product_item);
   
        //return new Response(json_encode($fe->fit()));
        //array('item'=>$product_item, 'data' => $fe->fit()
        //
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array('item'=>$product_item, 'data' => $fe->getBasicFeedback(),
        ));
//return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');
    }
 //-----------------------------------------------------------------------------
    public function emailAction($id) {
        $product = $this->get('admin.helper.product')->find($id);
         $session = $this->get("session");
    }
#-------------------------------------------------------------------------------
    public function getFeedBackJSONAction($user_id, $product_item_id) {
        $user = $this->get('user.helper.user')->find($user_id);
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);        
        if (!$user)
            return new Response("User Not found!");
        if (!$productItem)
            return new Response("Product not found!");
        $fit = new Algorithm($user, $productItem);
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array('data' => $fit->getFeedBackJson(),
        ));
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
    */
    public function getFeedBackListAction($product_item_id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser())) return new Response("User Not found, Log in required!");
        if (!$productItem) return new Response("Product not found!");
        
        $fit = new FitEngine($user,$productItem);
        $bfb = $fit->getBasicFeedback();
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $bfb));
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
}
?>

