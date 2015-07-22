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
	$get_user_address = $this->get('cart.helper.order')->getUserBillingAddress($user);

	if(count($get_total) == 0)
	{
	  $grand_total=0;
	}else{
	  $grand_total = array_sum($get_total["total"]);
	}

	Braintree_Configuration::environment('sandbox');
	Braintree_Configuration::merchantId('kd89skdzjcfz7m8r');
	Braintree_Configuration::publicKey('n5d4yjgb57379mq3');
	Braintree_Configuration::privateKey('5684c67cd5d7c71a6ed393a38ec140bd');

	$clientToken = Braintree_ClientToken::generate();

	return $this->render('LoveThatFitCartBundle:Payment:index.html.twig', array(
	  'cart' => $cart,
	  'grand_total' => $grand_total,
	  'name' => $get_user_address["shipping_first_name"].$get_user_address["shipping_last_name"],
	  'country' => $get_user_address["shipping_country"],
	  'city' => $get_user_address["shipping_city"],
	  'state' => $get_user_address["shipping_state"],
	  'postalcode' => $get_user_address["shipping_postcode"],
	  'address' => $get_user_address["shipping_address1"],
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
	  $order_id = $session->get('order_id');
	  $user = $this->get('security.context')->getToken()->getUser();
	  $user_id = $this->get('security.context')->getToken()->getUser()->getId();
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
	  $save_transaction = $this->get('cart.helper.order')->updateUserTransaction($order_id,$transaction_id,$transaction_status,$payment_method,$payment_json);
	  $remove_user_car = $this->get('cart.helper.cart')->removeUserCart($user);
	  return $this->render('LoveThatFitCartBundle:Payment:success.html.twig', array(
		'transaction_id' => $transaction_id,
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
