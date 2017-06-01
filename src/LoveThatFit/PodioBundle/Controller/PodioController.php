<?php

namespace LoveThatFit\PodioBundle\Controller;

use LoveThatFit\PodioBundle\Entity\PodioOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class PodioController extends Controller {

	private function process_request(){
      $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
      $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
      return $decoded;        
  }

  public function ordersAction() {
    $decoded  = $this->process_request(); 
    $status = [0,2]; //podio order status 0=pending , 2=failure
    $podio_orders = $this->get('order.helper.podio')->findOrdersByStatus($status); //get order list which is pending 
    if($podio_orders) {
      //if order list exists
      $total_podio_orders = count($podio_orders);
      foreach ($podio_orders as $orders) {
          //get order detail list
          $order_details = $this->get('cart.helper.orderDetail')->findByOrderID($orders['order_id']);                
          $id = $orders['id']; 
          if($order_details) {
            //if order detail list exists
            $count_order_detail = 0;
            $total_order_details = count($order_details);
            foreach ($order_details as $order_detail) {
              $style_id = '';
              if( isset($order_detail['control_number']) ) {
                if( isset($order_detail['brand_name']) ) {
                  $style_id = $order_detail['brand_name'].' - '.$order_detail['control_number'];
                } else {
                  $style_id = $order_detail['control_number'];
                }
              } 
              //podio orders data
              $order_podio = array(
                   'order_id' => $orders['order_id'],
                   'order_number' => $orders['order_number'],
                   'order_date' => ($orders['order_date']) ? $orders['order_date']->format('Y-m-d h:i:s') : '',
                   'order_status' => ($orders['order_status']) ? $orders['order_status'] : '',
                   'order_amount' => ($orders['order_amount']) ? $orders['order_amount'] : '',
                   'billing_first_name' => ($orders['billing_first_name']) ? $orders['billing_first_name'] : '',
                   'billing_last_name' => ($orders['billing_last_name']) ? $orders['billing_last_name'] : '',
                   'billing_address1' => ($orders['billing_address1']) ? $orders['billing_address1'] : '',
                   'billing_address2' => ($orders['billing_address2']) ? $orders['billing_address2'] : '',
                   'billing_city' => ($orders['billing_city']) ? $orders['billing_city'] : '',
                   'billing_postcode' => ($orders['billing_postcode']) ? $orders['billing_postcode'] : '',
                   'billing_country' => ($orders['billing_country']) ? $orders['billing_country'] : '',
                   'billing_state' => ($orders['billing_state']) ? $orders['billing_state'] : '',
                   'billing_phone' => ($orders['billing_phone']) ? $orders['billing_phone'] : '',
                   'shipping_first_name' => ($orders['shipping_first_name']) ? $orders['shipping_first_name'] : '',
                   'shipping_last_name' => ($orders['shipping_last_name']) ? $orders['shipping_last_name'] : '',
                   'shipping_address1' => ($orders['shipping_address1']) ? $orders['shipping_address1'] : '',
                   'shipping_address2' => ($orders['shipping_address2']) ? $orders['shipping_address2'] : '',
                   'shipping_city' => ($orders['shipping_city']) ? $orders['shipping_city'] : '',
                   'shipping_postcode' => ($orders['shipping_postcode']) ? $orders['shipping_postcode'] : '',
                   'shipping_country' => ($orders['shipping_country']) ? $orders['shipping_country'] : '',
                   'shipping_state' => ($orders['shipping_state']) ? $orders['shipping_state'] : '',
                   'shipping_phone' => ($orders['shipping_phone']) ? $orders['shipping_phone'] : '',
                   'shipping_amount' => ($orders['shipping_amount']) ? $orders['shipping_amount'] : '',
                   'transaction_id' => ($orders['transaction_id']) ? $orders['transaction_id'] : '',
                   'transaction_status' => ($orders['transaction_status']) ? $orders['transaction_status'] : '',
                   'payment_method' => ($orders['payment_method']) ? $orders['payment_method'] : '',
                   'credit_card' => ($orders['payment_json']) ? "xxxx-xxxx-xxxx-".json_decode($orders['payment_json'])
                    ->transaction->_attributes->creditCard->last4 : '',
                   'user_order_date' => ($orders['user_order_date']) ? $orders['user_order_date']->format('Y-m-d h:i:s') : '', 
                   'discount_amount' => ($orders['discount_amount']) ? $orders['discount_amount'] : '',
                   'total_amount' => ($orders['total_amount']) ? $orders['total_amount'] : '',
                   'item_description' => ($order_detail['item_description']) ? $order_detail['item_description'] : '',
                   'brand_name' => ($order_detail['brand_name']) ? $order_detail['brand_name'] : '',
                   'item_amount' => ($order_detail['amount']) ? $order_detail['amount'] : '',
                   'control_number' => ($order_detail['control_number']) ? $order_detail['control_number'] : '',
                   'style_id' => $style_id,
                   'quantity_item' => ($order_detail['qty']) ? $order_detail['qty'] : ''
                ); 
              //save podio order data
              $podio_id = $this->container->get('podio.helper.podiolib')->saveOrderPodio($order_podio);
              if($podio_id) {
                //podio order data is saved then add podio_id to podio_order_detail
                $order_entity = $this->container->get('order.helper.podio')->find($id);
                $save_order_detail = $this->get('orderdetail.helper.podio')->savePodioOrdersDetail($order_entity, $podio_id);
              }
              $count_order_detail++;
            }
            if($count_order_detail==$total_order_details) {
              //update podio order status to executed
              $save_podio_order = $this->get('order.helper.podio')->updatePodioOrders($id);
            }
          }
      }
      die(''.$total_podio_orders.' Podio orders added by cron job service...');
    } else {
      die('No new podio orders found...');
    }
  }

}

?>