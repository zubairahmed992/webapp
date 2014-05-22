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
     return new Response(json_encode($compare_list));
    }

    private function getCompareList() {
        $session = $this->get("session");
        if ($session->has('product_compare_list')) {
            $product_compare_list = new ProductCompareList($session->get('product_compare_list'));
        } else {
            #temp
            $list=array(
                5=>array('id'=>5,'itemid'=>24),
            );
            $product_compare_list = new ProductCompareList($list);
        }
        return $product_compare_list;
    }
    
}
?>

