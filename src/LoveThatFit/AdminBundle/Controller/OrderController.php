<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\CartBundle\Utils\Stamps;
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
        $tracking_number = $postages_link = "";

        $entity = $this->get('cart.helper.order')->find($id);
        $order_limit = $this->get('cart.helper.order')->getRecordsCountWithCurrentOrderLimit($id);

        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($order_limit[0]['id']));
        $page_number=$page_number==0?1:$page_number;
        if(!$entity) {
            $this->get('session')->setFlash('warning', 'Order not found!');
        }
	    $user_order=$this->container->get('cart.helper.order')->find($id);

        $shipping_information = ($entity->getShipmentJson() != null ? json_decode($entity->getShipmentJson()) : "");

        if($entity->getShipmentJson() != null)
        {
            $postages_link = $shipping_information->URL;
            $tracking_number = $shipping_information->TrackingNumber;
        }

        $sales_tax = ($entity->getSalesTax()) ? $entity->getSalesTax() : 0;

        return $this->render('LoveThatFitAdminBundle:Order:show.html.twig', array(
                'order' => $entity,
                'trackingNumber' => $tracking_number,
                'link'      => $postages_link,
                'order_id' => $id,
                'page_number' => $page_number,
                'user_order' => $user_order,
                'sales_tax' => $sales_tax
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

                        if(is_object($order['user_order_date']))
                            $csvSingle['order_date'] = $order['user_order_date']->format('Y-m-d H:i:s');
                        else
                            $csvSingle['order_date'] = $order['order_date']->format('Y-m-d H:i:s');

                        /*$csvSingle['order_date']   = ($order["order_date"]->format('d-m-Y'));*/
                        
                        $csvSingle['order_amount'] = "$" . number_format((float)$order["order_amount"], 2, '.', '');
                        $csvSingle['credit_card']  = "xxxx-xxxx-xxxx-".json_decode($order['payment_json'])
                            ->transaction->_attributes->creditCard->last4;
                        $csvSingle['shipping_add'] = rtrim($add,', ');
                        
                        $firstIteration = 0;
                    }

                    $csvSingle['brand_name']       = $detail["brand_name"];
                    $csvSingle['amount']           = "$".number_format((float)$detail["amount"], 2, '.', '');
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

    public function updateBraintreeTransactionUpdateAction( $id )
    {
        $entity = $this->get('cart.helper.order')->find($id);
        $transactionId = ( $entity->getTransactionId() != null ? $entity->getTransactionId() : null);
        $transactionStatus = ( $entity->getTransactionStatus() != null ? $entity->getTransactionStatus() : null);

        $transaction_status = $this->get('cart.helper.payment')->getTransactionStatus($transactionId, $transactionStatus);
        $this->get('cart.helper.order')->updateTransactionStatus( $entity ,$transaction_status['transaction_status']);

        return new Response(json_encode($transaction_status), 200, ['Content-Type' => 'application/json']);
    }

    public function updateShippingStatusAction( $id ){
        $stamps = new Stamps();
        $entity = $this->get('cart.helper.order')->find($id);
        $rate_json = ( $entity->getRateJson() != null ? json_decode($entity->getRateJson()) : null);
        $transactionStatus = ( $entity->getTransactionStatus() != null ? $entity->getTransactionStatus() : null);

        if ($transactionStatus != 'settled'){
            return new Response(json_encode("failed"), 300, ['Content-Type' => 'application/json']);
        }

        try{
            $billingAddress = $this->container->get('cart.helper.userAddresses')->findAddressById($rate_json->billing_id);
            $shippingAddress = $this->container->get('cart.helper.userAddresses')->findByCriteria(array('id' => $rate_json->shipping_id));

            $stamps_response = $stamps->createPostages($billingAddress, $shippingAddress, $rate_json);
            var_dump($stamps_response); die();
            if($shippingAddress == "")
                return new Response(json_encode(array('shipping_status' => "pending")), 200, ['Content-Type' => 'application/json']);


            $this->get('cart.helper.order')->updateWithShippingData( $entity ,json_encode($stamps_response));
            $stampsTxID = $stamps_response->StampsTxID;

            $ship_status = $stamps->getShippingStatusByTrackingNumber( $stampsTxID );

            /*********************** send emai user to order shipped start here*********************/            
            $shipping_information = ($entity->getShipmentJson() != null ? json_decode($entity->getShipmentJson()) : "");

            $tracking_number = "";
            $postages_link = "";
            if($entity->getShipmentJson() != null)
            {
                $postages_link = $shipping_information->URL;
                $tracking_number = $shipping_information->TrackingNumber;
            }
            
            if(!empty($tracking_number)) {
                $user_order=$this->container->get('cart.helper.order')->find($id);
                
                $sales_tax = ($entity->getSalesTax()) ? number_format((float)$entity->getSalesTax(), 2, '.', '') : "0.00"; 
                $user = $entity->getUser();

                $data_order = array(
                                    'order' => $entity,
                                    'trackingNumber' => $tracking_number,
                                    'link'      => $postages_link,
                                    'order_id' => $id,
                                    'user_order' => $user_order,
                                    'sales_tax' => $sales_tax,
                                    'email' => $user->getEmail()
                                );

                $this->sendEmailToUserOrderShipped($data_order);
            }
            /*********************** send emai user to order shipped end here*********************/

            return new Response(json_encode(array('shipping_status' => $ship_status)), 200, ['Content-Type' => 'application/json']);
        }catch (\ErrorException $exception)
        {
            return new Response(json_encode(array('shipping_status' => "pending")), 200, ['Content-Type' => 'application/json']);
        }
    }

    private function sendEmailToUserOrderShipped($dataArray)
    {
        $this->get('mail_helper')->sendUserOrderShippedEmail($dataArray);
        return;
    }
}