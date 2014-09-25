<?php
namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SiteBundle\AvgAlgorithm;

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
}
?>

