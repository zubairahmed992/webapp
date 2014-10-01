<?php
namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use Symfony\Component\HttpFoundation\Session\Session;

class FittingRoomController extends Controller {
   
#------------------------------------------------------------------------------#    
   public function fittingRoomProductsListAction($list_type='recently_tried_on', $page_number = 0, $limit = 0)
    {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $gender = $this->get('security.context')->getToken()->getUser()->getGender();
        $options = array('gender' => $gender, 'user_id' => $user_id, 'list_type' => $list_type, 'page_number' => $page_number, 'limit' => $limit);
        $entity = $this->get('admin.helper.product')->listByType($options);
        return $this->render('LoveThatFitSiteBundle:FittingRoom:_products_short.html.twig', array('products' => $entity, 'page_number' => $page_number, 'limit' => $limit, 'row_count' => count($entity)));
    }
#------------------------------------------------------------------------------#
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
        
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $fb);    
        $this->get('site.helper.userfittingroomitem')->add($user,$productItem);    

        return $this->render('LoveThatFitSiteBundle:FittingRoom:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $fb));
    }
  #----------------------------------------------------------------------------#
   public function getFeedBackListAction($product_item_id) {         
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
        if (!is_object($this->get('security.context')->getToken()->getUser())) return new Response("User Not found, Log in required!");
        if (!$productItem) return new Response("Product not found!");
        
        
        $product_size = $productItem->getProductSize();
        $product=$productItem->getProduct();
        $comp = new AvgAlgorithm($user,$product);
        $fb=$comp->getSizeFeedBack($product_size);
        $this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $fb);    
        $this->get('site.helper.userfittingroomitem')->add($user,$productItem);    
        return $this->render('LoveThatFitSiteBundle:FittingRoom:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $fb));
       
    }  
#----------------------------Remove Fitting Room -----------------------------#
    public function removeFittingRoomItemAction($user_id, $item_id){
      $t =  $this->get('site.helper.userfittingroomitem')->deleteByUserItem($user_id,$item_id);    
      return new Response(json_encode("prod_removed"));
    }
#-------------------------------------------------------------------------------#
    public function getFittingRoomItemIdsAction($user_id){        
      $t =  $this->get('site.helper.userfittingroomitem')->getItemIdsArrayByUser($user_id);    
      return new Response(json_encode($t));
    }
#-----------Added to closet method---------------------------------------------#
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
    #---------------------------------------------------------------------------#
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
    #--------------------------------------------------------------------------#
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
    
    #-------------------------------------------------------------------------------
    public function countMyColosetAction() {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get('admin.helper.product')->countMyCloset($user_id);
        $rec_count = count($this->get('admin.helper.product')->countMyCloset($user_id));
        return new Response($rec_count);
    }

   #---------------------------User Manquine------------------------------------# 
     public function userMannequinAction()
    {
        $user = $this->get('security.context')->getToken()->getUser(); 
        $manequin_size=$this->get('admin.helper.user.mannequin')->userMannequin($user);        
        return new Response(json_encode($manequin_size));
    }
    
}
?>

