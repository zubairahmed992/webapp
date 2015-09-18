<?php

namespace LoveThatFit\CartBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;
class MyOrderController extends Controller
{
  //----------------User Orders Display List --------------------------------------------------------------------------
  public function indexAction($page_number, $sort = 'id') {
	$user = $this->get('security.context')->getToken()->getUser();
	$orders_with_pagination = $this->get('cart.helper.order')->getListWithPaginationByUser($page_number,$sort,$user);
	return $this->render('LoveThatFitCartBundle:MyOrder:index.html.twig',
	  $orders_with_pagination);
  }

  //-----------------------Display Single order Detail by Id-----------------------------------------------------------------

  public function showAction($id) {
	$entity = $this->get('cart.helper.order')->find($id);
	$user = $this->get('security.context')->getToken()->getUser();
	$valid_user = $this->get('cart.helper.order')->HasUserOrder($id,$user);
	if($valid_user["counter"] == 0){
	  $this->get('session')->setFlash('warning', 'You are not Authenticate to view this page');
	  return $this->redirect($this->generateUrl('user_profile_order_list'));
	}
	return $this->render('LoveThatFitCartBundle:MyOrder:show.html.twig', array(
	  'order' => $entity,
	  'order_id' => $id
	));
  }
  //-----------------------Login session bridge for email click order number-----------------------------------------------------------------
  public function previewOrderAction($id) {
	$session = $this->getRequest()->getSession();
	$user = $this->get('security.context')->getToken()->getUser();
	if($user->getId()){
	  return $this->redirect($this->generateUrl('user_profile_order_show', array('id' => $id)));
	}else{
	  $session->set('order_id', $id);
	  return $this->redirect($this->generateUrl('login'));
	}
	die;

  }

}
