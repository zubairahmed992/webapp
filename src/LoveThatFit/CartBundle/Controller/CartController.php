<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Braintree_Configuration;
//use Braintree_ClientToken;
//use Braintree_Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
//    public function indexAction()
//    {
//	  Braintree_Configuration::environment('sandbox');
//	  Braintree_Configuration::merchantId('kd89skdzjcfz7m8r');
//	  Braintree_Configuration::publicKey('n5d4yjgb57379mq3');
//	  Braintree_Configuration::privateKey('5684c67cd5d7c71a6ed393a38ec140bd');
//
//	  $clientToken = Braintree_ClientToken::generate();
//	  //echo $clientToken;
//	   //die;
//		$name = 'ovais';
//		return $this->render('LoveThatFitCartBundle:Cart:index.html.twig', array('token' => $clientToken,'name' => $name));
//    }
//
//  	public function addAction(){
//	  Braintree_Configuration::environment('sandbox');
//	  Braintree_Configuration::merchantId('kd89skdzjcfz7m8r');
//	  Braintree_Configuration::publicKey('n5d4yjgb57379mq3');
//	  Braintree_Configuration::privateKey('5684c67cd5d7c71a6ed393a38ec140bd');
//
//	  $result = Braintree_Transaction::sale(array(
//		  "amount" => '11',
//		  "paymentMethodNonce" => $_POST['payment_method_nonce'],
//	  ));
//	  echo "<pre>";
//	  print_r($result);
//	  echo "</pre>";
//	}

  	public function basketAction(Request $request){
	  $user = $this->get('security.context')->getToken()->getUser();
	  $qty = 1;
	  $cart=$user->getCart();
	  $entity = $this->get('cart.helper.cart')->fillCart($request->get('item_id'),$user,$qty);
	  $get_total = $this->get('cart.helper.cart')->getCart($user);
	  return $this->render('LoveThatFitCartBundle:Cart:show.html.twig', array(
		'cart' => $cart,
		'grand_total' => array_sum($get_total["total"])
	  ));
	}
  	public function basketupdateAction(Request $request){
	  $decoded  = $request->request->all();
	  $user = $this->get('security.context')->getToken()->getUser();
	  if(isset($decoded['update']) == 'update'){
	  	$entity = $this->get('cart.helper.cart')->updateCart($decoded);
		$cart=$user->getCart();
	  }elseif(isset($decoded['checkout']) == 'checkout'){
		 $this->redirect($this->generateUrl('checkout'));
	  }else{
		$cart=$user->getCart();
	  }
	  $get_total = $this->get('cart.helper.cart')->getCart($user);
	  return $this->render('LoveThatFitCartBundle:Cart:show.html.twig', array(
		'cart' => $cart,
		'grand_total' => array_sum($get_total["total"])
	  ));
	}
//------------------------------Show Cart------------------------------------------------------------

	public function showAction(){
		$user = $this->get('security.context')->getToken()->getUser();
		$cart=$user->getCart();
		$get_total = $this->get('cart.helper.cart')->getCart($user);
		if(count($get_total) == 0)
		{
		  $grand_total=0;
		}else{
		  $grand_total = array_sum($get_total["total"]);
		}
		return $this->render('LoveThatFitCartBundle:Cart:show.html.twig', array(
			'cart' => $cart,
			'grand_total' => $grand_total
		  ));
	  }

	//------------------------------Delete Cart------------------------------------------------------------

	public function deleteAction($id) {
	  try {

		$message_array = $this->get('cart.helper.cart')->delete($id);
		$this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
		return $this->redirect($this->generateUrl('cart_show'));
		//return $this->redirect($this->generateUrl('cart_delete'));
	  } catch (\Doctrine\DBAL\DBALException $e) {

		$this->get('session')->setFlash('warning', 'This Item cannot be deleted!');
		return $this->redirect($this->getRequest()->headers->get('referer'));
	  }
	}
}
