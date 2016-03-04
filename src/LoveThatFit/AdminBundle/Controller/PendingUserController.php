<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PendingUserController extends Controller {

    //----------------All Pending User Display List --------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $orders_with_pagination = $this->get('user.helper.userarchives')->getListWithPagination($page_number, $sort);
	 // print_r($orders_with_pagination);die;
        return $this->render('LoveThatFitAdminBundle:PendingUser:index.html.twig', $orders_with_pagination);
    }

//-----------------------Display Single order Detail by Id-----------------------------------------------------------------

    public function showAction($id) {
        return $this->render('LoveThatFitAdminBundle:PendingUser:show.html.twig', array(

        ));
    }

    //----------------------------------Update Order Status--------------------------------------------------------
    public function updateAction(Request $request, $id) {

	  $decoded  = $request->request->all();
	  $order_status = $decoded["order_status"];
	  $order_id = $decoded["order_id"];
	  $entity = $this->get('cart.helper.order')->updateOrderStatus( $decoded["order_status"],$order_id);
	  $this->get('session')->setFlash('success', 'Order status has been updated');
	  return $this->redirect($this->generateUrl('admin_order'));
    }

}
