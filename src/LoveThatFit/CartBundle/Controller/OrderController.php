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
	  $entity = $this->get('cart.helper.order')->createNew();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $form = $this->createForm(new BillingShippingType('add'), $entity);
	  return $this->render('LoveThatFitCartBundle:Orders:billing_and_shipping.html.twig', array(
		'order_amount' => $order_amount,
		'form' => $form->createView()));
	}

  	public function savebillingAction(Request $request){
	  $decoded  = $request->request->all();
	  $user = $this->get('security.context')->getToken()->getUser();
	  $entity = $this->get('cart.helper.order')->saveBillingShipping($decoded,$user);
	  $order_id = $entity->getId();
	  $session = $this->getRequest()->getSession();
	  $session->set('order_id', $order_id);
	  $user_cart = $this->get('cart.helper.cart')->getFormattedCart($user);
	  $response = $this->get('cart.helper.orderDetail')->saveOrderDetail($user_cart,$order_id);
	  if($response == 'saved'){
		//echo "Order detail has been saved";
		//die;
		return $this->redirect($this->generateUrl('payment_default'));
	  }
	}

}
