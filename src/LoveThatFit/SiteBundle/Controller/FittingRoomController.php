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
                            'data' => $fb,
                    'product'=>$product,
                    'pixel_variance'=>  $this->get_pixel_variance($product),
                    ));
       
    }  
    #--------------------------------------------------------------
    private function get_pixel_variance($product){
        $product_pixel = array ('Arch Logo Zip Hoodie'  => 1,
                        'D624'  => 1,
                        'D568'  => 2,
                        'D522'  => -5,
                        '6646'  => -5,
                        'D586'  => -4,
                        '2882'  => -4,
                        'Mens PJ Top' => -6,
                        'Mens Royal Robe' => -4,
                      // 'Mens Tee'  => -2,
                        'Mens Henley'  => -7,
                      //'Old School Button Down'  => -5,
                      //'Old School Button Down'  => -5,
                      //'Vantage Polo Tee'  => -5,
                      //'Vantage Polo Tee'  => -5,
                      //'Heat Gear Polo'  => -5,
                      //'Heat Gear Polo'  => -5,
                      //'Long Sleeve VA Tee'  => -5,
                      //'One HellUVA School Tee'  => -5,
                      //'Chamion Short Sleeve Shirt'  => -5,
                      //'Under Armor Raglan Short Sleeve Shirt'  => -3,
                      //'JanSport Long Sleeve Shirt'  => -2,
                      //'JanSport Short Sleeve Shirt Crew Neck'  => -3,                        
                      //'Velocity Polo'  => -5,
                        'Vantage Sleeveless Vest'  => -6,
                      //'Colleseum Sleeveless Jacket'  => -6,                        
                        'JanSport Hoodie'  => -6,
                        'Champion Crew Neck Sweathshirt'  => 4,
                       //'Under Armor Zip Pullover'  => -6,                        
                       //'JanSport Pullover Crew Neck'  => -6,
                        'JanSport Zip Pullover'  => -8,
                       //'Under Armor Hoodie'  => -6,
                       //'Champion Hoodie'  => -7,
                        'CAMP DAVID HOODIE'  => -7,
                        'New Agenda Crew-Neck Short Sleeve Shirt'  => -3,
                        'JanSport Long Sleeve Shirt'  => -6,                      
                        'Champion Unisex Hoodie'  => -3,
                        'JansPort Unisex Hoodie'  => -3,
                        'Under Armor Unisex Hoodie'  => -3,
                        'Gear for Sports V-Neck Long Sleeve Shirt'  => -1,
                        'Under Armor Short Sleeve Shirt'  => -3,
                       //'New Agenda V-Neck Short Sleeve Shirt'  => -3,
                       //'MV Sport Hoodie'  => -3,                       
                        'Black Leather Dress'  => -3,
                        'Lux Light Cardigan'  => -2,
                        'Ladies Robe'  => -3,
                        'Ladies Henley'  => -2,
                        'Ladies Tunic'  => -5,
                        'Ladies Tee'  => -2,
                        'Ladies Royal Robe'  => -3,
                       // 'Sleeveless Sweater'  => -6,
                       
                        );
            
            if (array_key_exists($product->getName(), $product_pixel)) {
                return $product_pixel[$product->getName()];
            }else{
                return 0;
            }         
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

