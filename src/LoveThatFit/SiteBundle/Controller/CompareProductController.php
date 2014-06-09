<?php

namespace LoveThatFit\SiteBundle\Controller;

use LoveThatFit\SiteBundle\ProductCompareList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class CompareProductController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
      $compare_list = $this->getCompareList();
      $user = $this->get('security.context')->getToken()->getUser();
      $productItem = $this->get('admin.helper.productitem');
      
      $pl=$compare_list->getCompareableList($user,$productItem);
     // return new Response(json_encode($compare_list->getCompareableList($user,$productItem)));
       return $this->render('LoveThatFitSiteBundle:InnerSite:compareProduct.html.twig',
        array('product' => $pl,
            'product_json' => json_encode($pl)
            ));
     
    }
#-------------------------------------------->
    private function getCompareList() {
        
        $session = $this->get("session");       
        $list=null;
        if ($session->has('product_compare_list')) {
            $list = $session->get('product_compare_list');            
        } 
       
      /* if ($list==null || (is_array($list) && count($list)==0)){
            /*$list=array(
                29=>array('id'=>29,'item_id'=>181, 'product_name'=>'OldSchoolShirtdress', 'image'=>'uploads/ltf/products/display/web/5384769876d8b.png'),
                 30=>array('id'=>30,'item_id'=>230, 'product_name'=>'OldSchoolShirtdress', 'image'=>'uploads/ltf/products/display/web/5384769876d8b.png'),
                 31=>array('id'=>31,'item_id'=>236, 'product_name'=>'OldSchoolShirtdress', 'image'=>'uploads/ltf/products/display/web/5384769876d8b.png'),
              
            );
        }*/
        
        $session->set('product_compare_list', $list);
        $product_compare_list = new ProductCompareList($list);        
        return $product_compare_list;
    }
    
#-------------------------------------------->
    
     public function listAction() {
       $this->getCompareList();
       return $this->render('LoveThatFitSiteBundle:CompareProduct:_productCompareList.html.twig');     
    }
        
#-------------------------------------------->

    public function addAction($item_id) {
       $compare_product = $this->getCompareList();
       $productItem = $this->get('admin.helper.productitem')->find($item_id);
       $list = $compare_product->addItemToList($productItem);
       $session = $this->get("session");
        $session->set('product_compare_list', $list);
       return $this->render('LoveThatFitSiteBundle:CompareProduct:_productCompareList.html.twig');     
    }
    
#-------------------------------------------->

    public function removeAction($product_id) {
       $compare_product = $this->getCompareList();
       $new_list = $compare_product->removeItemFromList($product_id);
       $session = $this->get("session");
        $session->set('product_compare_list', $new_list);
       return $this->render('LoveThatFitSiteBundle:CompareProduct:_productCompareList.html.twig');     
    }
    
    
}
?>

