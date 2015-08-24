<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Parser;
class PaymentController extends Controller
{
  public function indexAction(Request $request){
	$user = $this->get('security.context')->getToken()->getUser();
	$user_id = $this->get('security.context')->getToken()->getUser()->getId();
	$cart=$user->getCart();
	$get_total = $this->get('cart.helper.cart')->getCart($user);
	$session = $this->getRequest()->getSession();
	$billing_shipping_info = $session->get('billing_shipping_info');
	if(count($get_total) == 0)
	{
	  $grand_total=0;
	}else{
	  $grand_total = array_sum($get_total["total"]);
	}
	$yaml = new Parser();
	$parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
	Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
	Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
	Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
	Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);
	$clientToken = Braintree_ClientToken::generate();
	return $this->render('LoveThatFitCartBundle:Payment:index.html.twig', array(
	  'cart' => $cart,
	  'grand_total' => $grand_total,
	  'billing_shipping_info' => $billing_shipping_info,
	  'token' => $clientToken
	));
  }

   	public function payAction(){
	  $yaml = new Parser();
	  $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
	  Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
	  Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
	  Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
	  Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);
	  $session = $this->getRequest()->getSession();
	  //$order_id = $session->get('order_id');
	  $user = $this->get('security.context')->getToken()->getUser();
	  $user_id = $this->get('security.context')->getToken()->getUser()->getId();
	  $billing_shipping_info = $session->get('billing_shipping_info');
//	  print_r($billing_shipping_info);
//	  die;
	  ######### Mail Code ###########
	  //$result_order = $this->get('cart.helper.order')->find('46');
	 // echo $result_order->getuserOrderDetail();
//	  foreach($result_order->getUserOrderDetail() as $val){
//		echo $val->getQty();
//		die;
//	  }

	  //$val = $this->get('mail_helper')->sendOrderConfirmationEmail($user,$result_order);
	  ########## End Mail Code ########


	  //print_r($val);
	  //die;
	  $result = Braintree_Transaction::sale(array(
		  "amount" => $_POST['order_amount'],
		  "paymentMethodNonce" => $_POST['payment_method_nonce'],
	  ));
	  $payment_json = json_encode($result);

	  if ($result->success) {
		$transaction_id = $result->transaction->id;
		//$transaction_status = 'Success';
		$transaction_status = $result->transaction->status;
		//print_r("success!: " . $result->transaction->id);
		$bill_info = $session->get('bill_info');
		$ship_info = $session->get('ship_info');
		$entity = $this->get('cart.helper.order')->saveBillingShipping($billing_shipping_info,$user);
		$this->get('cart.helper.userAddresses')->saveAddress($billing_shipping_info,$user,$bill_info,$ship_info);
		$order_id = $entity->getId();
		$user_cart = $this->get('cart.helper.cart')->getFormattedCart($user);
		$response = $this->get('cart.helper.orderDetail')->saveOrderDetail($user_cart,$order_id);
	  } else if ($result->transaction) {
		print_r("Error processing transaction:");
		print_r("\n  code: " . $result->transaction->processorResponseCode);
		print_r("\n  text: " . $result->transaction->processorResponseText);
		//$transaction_status = 'Error processing transaction';
		$transaction_status = $result->transaction->status;
	  } else {
		//print_r("Validation errors: \n");
		//print_r($result->errors->deepAll());
		$transaction_status = $result->transaction->status;
	  }
	  $payment_method = $result->transaction->paymentInstrumentType;
	  $order_number = $order_id.rand(100,100000);
	  $save_transaction = $this->get('cart.helper.order')->updateUserTransaction($order_id,$transaction_id,$transaction_status,$payment_method,$payment_json,$order_number);
	  $remove_user_car = $this->get('cart.helper.cart')->removeUserCart($user);
	  ######### Mail Code ###########
	  $entity = $this->get('cart.helper.order')->find($order_id);
	  //$this->get('mail_helper')->sendOrderConfirmationEmail($user,$entity);
	  ########## End Mail Code ########
	  $session->set('billing_shipping_info', '');
	  $session->set('order_amount', '');
	  $session->set('bill_info', '');
	  $session->set('ship_info', '');
	  return $this->render('LoveThatFitCartBundle:Payment:success.html.twig', array(
		'order_number' => $order_number,
		'transaction_status' => $transaction_status
	  ));

//	  $status = $result->success;
//	  $transaction = $result->transaction;
//	  echo $transaction."<br>";
//	  echo $status;
//	  die;
//	  echo "<pre>";
//	  print_r($result);
//	  echo "</pre>";
//	die;
	}

  
}
