<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;
class OrderController extends Controller
{
    public function indexAction()
    {
	  $session = $this->getRequest()->getSession();
	  $order_amount = $session->get('order_amount');
	  $billing_shipping_info = $session->get('billing_shipping_info');
	  $entity = $this->get('cart.helper.order')->createNew();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $billing_user_addresses = $this->get('cart.helper.userAddresses')->getUserAddresses($user);
	  $shipping_user_addresses = $this->get('cart.helper.userAddresses')->getUserAddresses($user,0);
	  //echo "<pre>";
	  //print_r($user_addresses);
	  //die;
	  $form = $this->createForm(new BillingShippingType($billing_shipping_info), $entity);
	  return $this->render('LoveThatFitCartBundle:Orders:billing_and_shipping.html.twig', array(
		'order_amount' => $order_amount,
		'user_addresses' => $billing_user_addresses,
		'shipping_addresses' => $shipping_user_addresses,
		'form' => $form->createView()));
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
	public function getAddressAction($address_id)
	{
	  $user_addresses = $this->get('cart.helper.userAddresses')->find($address_id);
	  return new Response(json_encode($user_addresses->toArray()));
	}
}
