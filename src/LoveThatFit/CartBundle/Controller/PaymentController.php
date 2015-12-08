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
	  $destination_zip=$billing_shipping_info["billing"]["shipping_postcode"];
	  $destination_city=$billing_shipping_info["billing"]["shipping_city"];
	  $destination_state=$billing_shipping_info["billing"]["shipping_state"];
	}else{
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $billing_user_addresses = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,1);
	  $shipping_user_addresses = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,0);
	  $destination_zip = $shipping_user_addresses['postcode'];
	  $destination_city = $shipping_user_addresses['city'];
	  $destination_state = $shipping_user_addresses['state'];
	}
	  $date = date("Ymd");
	  $strDestinationZip = $destination_zip;
	  $strMethodShortName = 'GND';
	  $strPackageLength = '13';
	  $strPackageWidth = '10';
	  $strPackageHeight = '1';
	  $strPackageWeight = '2';
	  $boolReturnPriceOnly = true;
	  $result = $this->get('cart.helper.shipping')->GetShippingRate(					$strDestinationZip,
		$strMethodShortName,
		$strPackageLength,
		$strPackageWidth,
		$strPackageHeight,
		$strPackageWeight,
		$boolReturnPriceOnly);
	  $transit_days = $this->get('cart.helper.shipping')->getTimeInTransitInformation($destination_city,$destination_state,$destination_zip,$date);
	  //echo $transit_days;
	return $this->render('LoveThatFitCartBundle:Payment:index.html.twig', array(
	  'cart' => $cart,
	  'grand_total' => $grand_total,
	  'billing_shipping_info' => $billing_shipping_info,
	  'token' => $clientToken,
	  'counter' => $counter["counter"],
	  'shipping_charges' => $result,
	  'billing_user_addresses' => $billing_user_addresses,
	  'transit_days' => $transit_days,
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
	  return $this->render('LoveThatFitCartBundle:Payment:success.html.twig', array(
		'order_number' => $result['order_number'],
		'transaction_status' => $result['transaction_status'],
		'response_code' => $result['response_code']
	  ));
	  }

	}

  
}
