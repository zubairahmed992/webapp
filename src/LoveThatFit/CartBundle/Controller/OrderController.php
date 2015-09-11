<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;
use LoveThatFit\CartBundle\Form\Type\Address;
class OrderController extends Controller
{
    public function indexAction()
    {
	  $session = $this->getRequest()->getSession();
	  $order_amount = $session->get('order_amount');
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $entity = $this->get('cart.helper.order')->createNew();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $counter = $this->get('cart.helper.userAddresses')->getUserAddressesCount($user);
	  if($counter["counter"] == 0){
		$session->set('default', "no"); //User came first time
		$form = $this->createForm(new BillingShippingType($billing_shipping_info,$user), $entity);
		return $this->render('LoveThatFitCartBundle:Orders:billing_and_shipping.html.twig', array(
		  'order_amount' => $order_amount,
		  'form' => $form->createView()));
	  }else{
		$billing_address = $session->get('billing_address');
		$shipping_address = $session->get('shipping_address');
		$session->set('default', "yes"); //User didn't came first time and previously ordered
		return $this->render('LoveThatFitCartBundle:Orders:show_user_address.html.twig', array(
		  'order_amount' => $order_amount,
		  'billing_addresses' => $billing_address,
		  'shipping_addresses' => $shipping_address));
	  }
	}

	public function changeAddressAction($bill)
	{
	  $session = $this->getRequest()->getSession();
	  $order_amount = $session->get('order_amount');
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $entity = $this->get('cart.helper.userAddresses')->createNew();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $counter = $this->get('cart.helper.userAddresses')->getUserAddressesCount($user);
	  if($counter["counter"] == 0){
		return $this->redirect($this->generateUrl('order_default'));
	  }
	  $user_addresses = $this->get('cart.helper.userAddresses')->getAllAddresses($user);

	  $default_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,$bill);


	  $form = $this->createForm(new Address($default_address,$user), $entity);
	   return $this->render('LoveThatFitCartBundle:Orders:change_address.html.twig', array(
		'form' => $form->createView(),
	  	'user_addresses' => $user_addresses,
		'default_address' => $default_address,
		 'is_bill' => $bill,
		 'address_counter' => $counter["counter"]
	  ));
	}

  	public function savebillingAction(Request $request){
	  $decoded  = $request->request->all();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $session = $this->getRequest()->getSession();
	  $session->set('billing_shipping_info', $decoded);
	  $session->set('bill_info', $decoded["bill_info"]);
	  $session->set('ship_info', $decoded["ship_info"]);
	  return $this->redirect($this->generateUrl('payment_default'));
	}
	/* update or add or save address condition from change address */
	public function saveAddressAction(Request $request){
	  $decoded  = $request->request->all();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $session = $this->getRequest()->getSession();

	  //if hidden value update_info is 1 then only it will update the address
	  if($decoded["update_info"]== 1){
		$this->get('cart.helper.userAddresses')->updateUserAddresses($user,$decoded);
		$default_billing_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'1');
		$default_shipping_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'0');
		$session->set('billing_address', $default_billing_address);
		$session->set('shipping_address', $default_shipping_address);

		//$session->get('order_amount', $decoded["order_amount"]); //making that session active so it can be check
		return $this->redirect($this->generateUrl('order_default'));
	  }else{
		$this->get('cart.helper.userAddresses')->AddUserAddresses($user,$decoded);
		$default_billing_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'1');
		$default_shipping_address = $this->get('cart.helper.userAddresses')->getUserDefaultAddresses($user,'0');
		$session->set('billing_address', $default_billing_address);
		$session->set('shipping_address', $default_shipping_address);
		$session->set('bill_info', "2"); //making that session active so it can be check
		$session->set('ship_info', "2"); //making that session active so it can be check

		return $this->redirect($this->generateUrl('order_default'));
	  }

	  $session->set('billing_shipping_info', $decoded);
	  $session->set('bill_info', $decoded["bill_info"]);
	  $session->set('ship_info', $decoded["ship_info"]);
	  return $this->redirect($this->generateUrl('payment_default'));
	}
	public function getAddressAction($address_id)
	{
	  $user_addresses = $this->get('cart.helper.userAddresses')->find($address_id);
	  return new Response(json_encode($user_addresses->toArray()));
	}

	public function previewAddressAction(){
	  return $this->redirect($this->generateUrl('payment_default'));
	}
}
