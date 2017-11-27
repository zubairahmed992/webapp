<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MorphingController extends Controller {
         
#------------------------/pi/morphing/index

    public function indexAction() {
        $users    = $this->get("user.helper.user")->getActivityLog();
        $products = $this->get("admin.helper.product")->getProductIdName();
        return $this->render('LoveThatFitProductIntakeBundle:Morphing:index.html.twig', array(
                    'users'    => $users,
                    'products' => $products,                   
                ));
    }
#------------------------/pi/morphing/product_detail/{id}    
    public function productDetailAction($id) {
        $product = $this->get("admin.helper.product")->find($id);
        
        $arr=$product->getIdsArray();
        $arr['item_details'] =$product->getItemArray();
        return new Response(json_encode($arr));
    }

}
