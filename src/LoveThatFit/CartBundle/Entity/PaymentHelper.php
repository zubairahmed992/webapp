<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;

class PaymentHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
	#------------------------------------ Get Braintree Client Token -----------------#
	public function getClientToken(){
	  $yaml = new Parser();
	  $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
	  Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
	  Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
	  Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
	  Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);
	  $clientToken = Braintree_ClientToken::generate();
	  return $clientToken;
	}
#------------------------------------- End of Braintree Client Token ------------#
#------------------------------------ Braintree Transaction -----------------#
	public function braintreeTransaction($user,$decoded,$session){
	  $yaml = new Parser();
	  $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
	  Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
	  Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
	  Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
	  Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);

	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $result = Braintree_Transaction::sale(array(
		"amount" => $decoded['order_amount'],
		"paymentMethodNonce" => $decoded['payment_method_nonce'],
	  ));
	  $payment_json = json_encode($result);

	  /****************** Success and Fail Conditions *********************/
	  if ($result->success) {
		$transaction_id = $result->transaction->id;
		$transaction_status = $result->transaction->status;
		$payment_method = $result->transaction->paymentInstrumentType;
		$bill_info = $session->get('bill_info');
		$ship_info = $session->get('ship_info');
		$order_amount = $session->get('order_amount');
		$billing_address = $session->get('billing_address');
		$shipping_address = $session->get('shipping_address');
		$select_address = $session->get('default');
		if($select_address == 'yes'){
		  //user came from show address screen and previously ordered things so saving just order information and using the previous added billing and shipping address
		  $entity = $this->container->get('cart.helper.order')->saveBillingShippingDefaultAddress($billing_address,$shipping_address,$user,$order_amount);
		  $order_id = $entity->getId();
		}else{
		  //User came here first time so we need to store address here
		  $entity = $this->container->get('cart.helper.order')->saveBillingShipping($billing_shipping_info,$user);
		  $order_id = $entity->getId();
		  $this->container->get('cart.helper.userAddresses')->saveAddress($billing_shipping_info,$user,$bill_info,$ship_info);
		}
		$order_number = $order_id.rand(100,100000);
		$user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
		$response = $this->container->get('cart.helper.orderDetail')->saveOrderDetail($user_cart,$entity);
		$save_transaction = $this->container->get('cart.helper.order')->updateUserTransaction($order_id,$transaction_id,$transaction_status,$payment_method,$payment_json,$order_number);
		//$this->container->get('mail_helper')->sendOrderConfirmationEmail($user,$entity, $user->getCart());
		$data = array();
		$data["order_number"] = $order_number;
		$data["transaction_status"] = $transaction_status;
		$session->set('billing_shipping_info', '');
		$session->set('order_amount', '');
		$session->set('bill_info', '');
		$session->set('ship_info', '');
		$session->set('billing_address', '');
		$session->set('shipping_address', '');
		$session->set('default', '');
		$remove_user_cart = $this->container->get('cart.helper.cart')->removeUserCart($user);
		return $data;
	  }else{
		$transaction_status = $result->transaction->status;
		$data = array();
		$data["order_number"] = '';
		$data["transaction_status"] = $transaction_status;
		$session->set('billing_shipping_info', '');
		$session->set('order_amount', '');
		$session->set('bill_info', '');
		$session->set('ship_info', '');
		$session->set('billing_address', '');
		$session->set('shipping_address', '');
		$session->set('default', '');
		return $data;
	  }
	  /****************** End of Success and Fail Conditions **************/

	}
#------------------------------------- End of Braintree Transaction ------------#
}