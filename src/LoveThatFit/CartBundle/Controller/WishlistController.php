<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class WishlistController extends Controller
{
  	public function addAction(Request $request){
	  $user = $this->get('security.context')->getToken()->getUser();
	  $decoded  = $request->request->all();
	  $item_id = $decoded["item_id"];
	  $qty = 1;
	  $wishlist=$user->getWishlist();
	  $entity = $this->get('cart.helper.wishlist')->fillWishlist($item_id,$user,$qty);
	  return $this->redirect($this->generateUrl('wishlist_show'));
	}
	public function addajaxAction(Request $request){
	  $decoded  = $request->request->all();
	  $item_id = $decoded["item_id"];
	  $user = $this->get('security.context')->getToken()->getUser();
	  $qty = 1;
	  $wishlist=$user->getWishlist();
	  $entity = $this->get('cart.helper.wishlist')->fillWishlist($item_id,$user,$qty);
	  $getCounterResult = $this->get('cart.helper.wishlist')->countWishlistItemsByQuantity($user);
	  return new Response($getCounterResult["counter"]);
	}
  	public function processCheckoutAction(Request $request){
	  $decoded  = $request->request->all();
	  $order_amount = $decoded["order_amount"];
	  $user = $this->get('security.context')->getToken()->getUser();
		if(isset($decoded['checkout']) == 'checkout'){
		  $session = $this->getRequest()->getSession();
		  $session->set('order_amount', $order_amount);
		  $default_billing_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'1');
		  $default_shipping_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'0');
		  $session->set('billing_address', $default_billing_address);
		  $session->set('shipping_address', $default_shipping_address);
		  return $this->redirect($this->generateUrl('order_default'));
		}
	  else{
		return $this->redirect($this->generateUrl('cart_show'));
	  }
	}
//------------------------------Show Cart------------------------------------------------------------

	public function showAction(){
	  $user = $this->get('security.context')->getToken()->getUser();
	  $wishlist=$user->getWishlist();
	  $grand_total = $this->get('cart.helper.wishlist')->getWishlist($user);
	  $getCounterResult = $this->get('cart.helper.wishlist')->countWishlistItems($user);
		return $this->render('LoveThatFitCartBundle:Wishlist:show.html.twig', array(
			'wishlist' => $wishlist,
			'grand_total' => $grand_total,
		  	'itemscounter' => $getCounterResult["counter"]
		  ));
	  }

	//------------------------------Delete Cart------------------------------------------------------------

	public function deleteAction($id) {
	  try {
		$message_array = $this->get('cart.helper.wishlist')->delete($id);
		$this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
		return $this->redirect($this->generateUrl('wishlist_show'));
	  } catch (\Doctrine\DBAL\DBALException $e) {
		$this->get('session')->setFlash('warning', 'This Item cannot be deleted!');
		return $this->redirect($this->getRequest()->headers->get('referer'));
	  }
	}
  //------------------------------Update Cart Quantity by Ajax ------------------------------------------------------------

  public function updateQtyAjaxAction($id,$qty) {
	$this->get('cart.helper.wishlist')->updateWishlistAjax($id,$qty);
	$user = $this->get('security.context')->getToken()->getUser();
	$grand_total = $this->get('cart.helper.wishlist')->getWishlist($user);
	return new Response($grand_total);
  }
}
