<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumProductTestlType;

class CartController extends Controller {

    //------------------------------------------------------------------------------------------
################################################################
#Cart Index
################################################################

    public function CartIndexAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());
        return $this->render('LoveThatFitSupportBundle:Cart:index.html.twig', array(
                    'userForm' => $userForm->createView(),
                    'productForm' => $productForm->createView(),
                    'user' => '',
                ));
    }

//------------------------------------------------------------------------------------------
//show Cart
  public function showAction($id) {
    $user = $this->get('user.helper.user')->find($id);
    $grand_total = $this->get('cart.helper.cart')->getCart($user);
    $getCounterResult = $this->get('cart.helper.cart')->countCartItems($user);
    return $this->render('LoveThatFitSupportBundle:Cart:show.html.twig', array(
        'cart' =>  $user->getCart(),
        'grand_total' => $grand_total,
        'itemscounter' => $getCounterResult["counter"]
    ));
  }

//------------------------------------------------------------------------------------------
//delete Cart
  public function deleteAction($id,$user_id) {
    $message_array = $this->get('cart.helper.cart')->delete($id);
    $user = $this->get('user.helper.user')->find($user_id);
    $getCounterResult = $this->get('cart.helper.cart')->countCartItems($user);
    return new Response($getCounterResult["counter"]);
  }

  //------------------------------------------------------------------------------------------Recommended Items
  public function addToCartAction(Request $request){
	  $decoded  = $request->request->all();	  
	  $user = $this->get('user.helper.user')->find($decoded["user_id"]);
          $product = $this->get('admin.helper.product')->find($decoded["product_id"]);
          $item=$product->getDefaultItem($user);
	  $this->get('cart.helper.cart')->fillCart($item->getId(),$user,1);
	  return new Response("1");
	}
  //------------------------------------------------------------------------------------------
//get product sizes and colors
  public function getSizeColorAction($product_id) {
    $product = $this->get('admin.helper.product')->find($product_id);
    $sizes=$product->getIdsArray($product);
    return new Response(json_encode($sizes));
    //print_r($sizes["size"]);die;
//    foreach($sizes["size"] as $size){
//      echo $size["title"];
//    }
//    die;
//    return $this->render('LoveThatFitSupportBundle:Cart:show.html.twig', array(
//
//    ));
  }

  //------------------------------------------------------------------------------------------Add Items to cart
  public function addToCartItemAction(Request $request){
    $decoded  = $request->request->all();
    $user = $this->get('user.helper.user')->find($decoded["user_id"]);
    $this->get('cart.helper.cart')->fillCart($decoded["item_id"],$user,1);
    return new Response("1");
  }

}
