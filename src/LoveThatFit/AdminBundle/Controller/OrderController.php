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
        //-----------------------Display Single order Detail by Id-----------------------------------------------------------------

    public function showAction($id)
    {
        $entity = $this->get('cart.helper.order')->find($id);
        $order_limit = $this->get('cart.helper.order')->getRecordsCountWithCurrentOrderLimit($id);

        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($order_limit[0]['id']));
        $page_number=$page_number==0?1:$page_number;
        if(!$entity) {
            $this->get('session')->setFlash('warning', 'Order not found!');
        }
	    $user_order=$this->container->get('cart.helper.order')->find($id);
        return $this->render('LoveThatFitAdminBundle:Order:show.html.twig', array(
                'order' => $entity,
                'order_id' => $id,
                'page_number' => $page_number,
                'user_order' => $user_order
            )
        );
    }

    //----------------------------------Update Order Status--------------------------------------------------------
    public function updateAction(Request $request, $id)
    {
        $decoded  = $request->request->all();
        $order_status = $decoded["order_status"];
        $order_id = $decoded["order_id"];
        $entity = $this->get('cart.helper.order')->updateOrderStatus( $decoded["order_status"],$order_id);

        $this->get('session')->setFlash('success', 'Order status has been updated');
        return $this->redirect($this->generateUrl('admin_order'));
    }

    public function exportAction(Request $request)
    {
        $orders = $this->get('cart.helper.order')->findOrderList();

        if (!empty($orders)) {
            header('Content-Type: application/csv');
            //header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachement; filename="orders.csv";');
            $output = fopen('php://output', 'w');
           
            fputcsv($output, array(
                'Order No',
                'Customer Name',
                'Order Date',
                'Order Total Amount',
                'Credit Card No',
                'Shipping Address',
                'Brand name',
                'Item Price',
                'Item Description',
                'Style ID',
                )
            );
            $csvArray = array();
            foreach ($orders as $order) {
                $orderDetail = $this->get('cart.helper.orderDetail')->findByOrderIDExport($order['id']);
                $firstIteration = 1;
                foreach ($orderDetail as $detail) {

                    $csvSingle['order_number'] = "";
                    $csvSingle['user_name']    = "";
                    $csvSingle['order_date']   = "";
                    $csvSingle['order_amount'] = "";
                    $csvSingle['credit_card']  = "";
                    $csvSingle['shipping_add'] = "";
                    
                    if ($firstIteration == 1) {
                        $add = "";
                        if ($order["shipping_address1"] != "") {
                            $add .= $order["shipping_address1"]. ", ";
                        }
                        if ($order["shipping_city"] != "") {
                            $add .= $order["shipping_city"]. ", ";
                        }
                        if ($order["shipping_state"] != "") {
                            $add .= $order["shipping_state"]. ", ";
                        }
                        if ($order["shipping_country"] != "") {
                            $add .= $order["shipping_country"]. ", ";
                        }
                        if ($order["shipping_postcode"] != "") {
                            $add .= $order["shipping_postcode"]. ", ";
                        }
                        
                        $csvSingle['order_number'] = $order["order_number"];
                        $csvSingle['user_name']    = ($order["billing_first_name"]. " ".$order["billing_last_name"]);
                        $csvSingle['order_date']   = ($order["order_date"]->format('d-m-Y'));
                        $csvSingle['order_amount'] = "$" . number_format((float)$order["order_amount"], 2, '.', '');
                        $csvSingle['credit_card']  = "xxxx-xxxx-xxxx-".json_decode($order['payment_json'])
                            ->transaction->_attributes->creditCard->last4;
                        $csvSingle['shipping_add'] = rtrim($add,', ');
                        
                        $firstIteration = 0;
                    }

                    $csvSingle['brand_name']       = $detail["brand_name"];
                    $csvSingle['amount']           = $detail["amount"];
                    $csvSingle['item_description'] = $detail["item_description"];
                    $csvSingle['control_number']   = $detail["control_number"];

                    fputcsv($output, $csvSingle);
                }
            }
            # Close the stream off
            fclose($output);
            return new Response('');
        } else {
            $this->get('session')->setFlash('warning', 'No Record Found!');
            return $this->render('LoveThatFitAdminBundle:Order:index.html.twig');
        }
    }
}