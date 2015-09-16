<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Parser;
class PaymentController extends Controller
{
  public function indexAction(Request $request){
	$user = $this->get('security.context')->getToken()->getUser();
	$user_id = $this->get('security.context')->getToken()->getUser()->getId();
	$session = $this->getRequest()->getSession();
	if($session->get('order_amount') == ''){
	  return $this->redirect($this->generateUrl('cart_show'));
	}else{
	$cart=$user->getCart();
	$grand_total = $this->get('cart.helper.cart')->getCart($user);
	$clientToken = $this->get('cart.helper.payment')->getClientToken();
	$counter = $this->get('cart.helper.userAddresses')->getUserAddressesCount($user);

	if($counter["counter"] == 0){
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $billing_user_addresses='';
	  $shipping_user_addresses='';
	}else{
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $billing_user_addresses = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,1);
	  $shipping_user_addresses = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,0);
	}

	return $this->render('LoveThatFitCartBundle:Payment:index.html.twig', array(
	  'cart' => $cart,
	  'grand_total' => $grand_total,
	  'billing_shipping_info' => $billing_shipping_info,
	  'token' => $clientToken,
	  'counter' => $counter["counter"],
	  'billing_user_addresses' => $billing_user_addresses,
	  'shipping_user_addresses' => $shipping_user_addresses
	));
	}
  }

   	public function payAction(Request $request){
	  $user = $this->get('security.context')->getToken()->getUser();
	  $decoded  = $request->request->all();
	  $session = $this->getRequest()->getSession();
	  if($session->get('order_amount') == ''){
		return $this->redirect($this->generateUrl('cart_show'));
	  }else{
	  $result = $this->get('cart.helper.payment')->braintreeTransaction($user,$decoded,$session);
	  ######### Mail Code ###########
	  //$entity = $this->get('cart.helper.order')->find($result['order_id']);
	  //$this->get('mail_helper')->sendOrderConfirmationEmail($user,$entity, $user->getCart());
	  ########## End Mail Code ########
	  return $this->render('LoveThatFitCartBundle:Payment:success.html.twig', array(
		'order_number' => $result['order_number'],
		'transaction_status' => $result['transaction_status']
	  ));
	  }

	}

  
}
