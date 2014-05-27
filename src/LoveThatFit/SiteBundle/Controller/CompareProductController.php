<?php

namespace LoveThatFit\SiteBundle\Controller;

use LoveThatFit\SiteBundle\ProductCompareList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompareProductController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
      $compare_list = $this->getCompareList();
      $user = $this->get('security.context')->getToken()->getUser();
       $productItem = $this->get('admin.helper.productitem');
       //return new Response(json_encode($compare_list->getCompareableList($user,$productItem)));
       return $this->render('LoveThatFitSiteBundle:InnerSite:compareProduct.html.twig',
        array('product' => $compare_list->getCompareableList($user,$productItem)));
     
    }

    private function getCompareList() {
        
        $session = $this->get("session");
        if ($session->has('product_compare_list')) {
            $product_compare_list = new ProductCompareList($session->get('product_compare_list'));
        } else {
            #temp
            $list=array(
                5=>array('id'=>1,'itemid'=>1),
                6=>array('id'=>3,'itemid'=>8),
                7=>array('id'=>4,'itemid'=>14),
            /*
                20=>array('id'=>20,'itemid'=>95),
                3=>array('id'=>3,'itemid'=>8),
                7=>array('id'=>7,'itemid'=>36),
             */
                
        
            );
            $product_compare_list = new ProductCompareList($list);
        }
        return $product_compare_list;
    }
    
}
?>

