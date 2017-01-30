<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;

class OrderController extends Controller {

    //----------------All Orders Display List 
    public function indexAction()
    {
        return $this->render('LoveThatFitAdminBundle:Order:index.html.twig');
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('cart.helper.order')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }

    /*    
    public function indexAction($page_number, $sort = 'id') {
        $orders_with_pagination = $this->get('cart.helper.order')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Order:index.html.twig', $orders_with_pagination);
    }
    */

//-----------------------Display Single order Detail by Id-----------------------------------------------------------------

    public function showAction($id) {
        $entity = $this->get('cart.helper.order')->find($id);
        $order_limit = $this->get('cart.helper.order')->getRecordsCountWithCurrentOrderLimit($id);

        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($order_limit[0]['id']));
        $page_number=$page_number==0?1:$page_number;
        if(!$entity){        
            $this->get('session')->setFlash('warning', 'Order not found!');
        }
	    $user_order=$this->container->get('cart.helper.order')->find($id);

        // echo "<pre>";
        // print_r($user_order);
        // die();
        return $this->render('LoveThatFitAdminBundle:Order:show.html.twig', array(
                    'order' => $entity,
                    'order_id' => $id,
                    'page_number' => $page_number,
                    'user_order' => $user_order
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
