<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\Query\Expr\Literal;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Yaml\Parser;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;

class OrderHelper
{

    protected $dispatcher;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

//------------------------------- When user does payment and add addres from screen 1 -----------------------------------------------------///////////
    public function saveBillingShipping($decoded, $user, $shipping_amount, $fnfGroup = null)
    {
        $billing = $decoded["billing"];
        $user_billing_shipping_info = $this->createNew();
        $user_billing_shipping_info->setUser($user);
        $user_billing_shipping_info->setBillingFirstName($billing["billing_first_name"]);
        $user_billing_shipping_info->setBillingLastName($billing["billing_last_name"]);
        $user_billing_shipping_info->setBillingAddress1($billing["billing_address1"]);
        $user_billing_shipping_info->setBillingAddress2($billing["billing_address2"]);
        $user_billing_shipping_info->setBillingPhone($billing["billing_phone"]);
        $user_billing_shipping_info->setBillingCity($billing["billing_city"]);
        $user_billing_shipping_info->setBillingPostCode($billing["billing_postcode"]);
        $user_billing_shipping_info->setBillingCountry($billing["billing_country"]);
        $user_billing_shipping_info->setBillingState($billing["billing_state"]);

        $user_billing_shipping_info->setShippingFirstName($billing["shipping_first_name"]);
        $user_billing_shipping_info->setShippingLastName($billing["shipping_last_name"]);
        $user_billing_shipping_info->setShippingAddress1($billing["shipping_address1"]);
        $user_billing_shipping_info->setShippingAddress2($billing["shipping_address2"]);
        $user_billing_shipping_info->setShippingPhone($billing["shipping_phone"]);
        $user_billing_shipping_info->setShippingCity($billing["shipping_city"]);
        $user_billing_shipping_info->setShippingPostCode($billing["shipping_postcode"]);
        $user_billing_shipping_info->setShippingCountry($billing["shipping_country"]);
        $user_billing_shipping_info->setShippingState($billing["shipping_state"]);
        $user_billing_shipping_info->setOrderStatus('Pending');
        $user_billing_shipping_info->setOrderAmount($decoded["order_amount"]);
        $user_billing_shipping_info->setDiscountAmount($decoded['discount_amount']);
        $user_billing_shipping_info->setTotalAmount($decoded['total_amount']);
        $user_billing_shipping_info->setShippingAmount($shipping_amount);
        if(array_key_exists('groupId', $decoded))
        {
            $user_billing_shipping_info->setUserGroup( $fnfGroup );
        }
        return $this->save($user_billing_shipping_info);

    }

//------------------------------- When user does payment and select addres from screen 2 -----------------------------------------------------///////////
    public function saveBillingShippingDefaultAddress($billing_address, $shipping_address, $user, $order_amount, $shipping_amount)
    {
        $user_billing_shipping_info = $this->createNew();
        $user_billing_shipping_info->setUser($user);
        $user_billing_shipping_info->setBillingFirstName($billing_address["first_name"]);
        $user_billing_shipping_info->setBillingLastName($billing_address["last_name"]);
        $user_billing_shipping_info->setBillingAddress1($billing_address["address1"]);
        $user_billing_shipping_info->setBillingAddress2($billing_address["address2"]);
        $user_billing_shipping_info->setBillingPhone($billing_address["phone"]);
        $user_billing_shipping_info->setBillingCity($billing_address["city"]);
        $user_billing_shipping_info->setBillingPostCode($billing_address["postcode"]);
        $user_billing_shipping_info->setBillingCountry($billing_address["country"]);
        $user_billing_shipping_info->setBillingState($billing_address["state"]);

        $user_billing_shipping_info->setShippingFirstName($shipping_address["first_name"]);
        $user_billing_shipping_info->setShippingLastName($shipping_address["last_name"]);
        $user_billing_shipping_info->setShippingAddress1($shipping_address["address1"]);
        $user_billing_shipping_info->setShippingAddress2($shipping_address["address2"]);
        $user_billing_shipping_info->setShippingPhone($shipping_address["phone"]);
        $user_billing_shipping_info->setShippingCity($shipping_address["city"]);
        $user_billing_shipping_info->setShippingPostCode($shipping_address["postcode"]);
        $user_billing_shipping_info->setShippingCountry($shipping_address["country"]);
        $user_billing_shipping_info->setShippingState($shipping_address["state"]);
        $user_billing_shipping_info->setOrderStatus('Pending');
        $user_billing_shipping_info->setOrderAmount($order_amount);
        $user_billing_shipping_info->setShippingAmount($shipping_amount);
        return $this->save($user_billing_shipping_info);

    }

#------------------------------Find cart by id--------------------------------#
    public function findCartById($id)
    {
        return $this->repo->find($id);
    }

#------------------------------Find user billing address--------------------------------#
    public function getUserBillingAddress($user)
    {
        return $this->repo->findOneByUserAddress($user);
    }

#-------------------------- Check Valid user for an order -----------------#
    public function HasUserOrder($order_id, $user)
    {
        return $this->findOrderByUserId($order_id, $user);
    }

#------------------------------Find cart by id--------------------------------#
    public function findOrderByUserId($order_id, $user)
    {
        return $this->repo->findOneByUser($order_id, $user);
    }

#------------------------------Find order by id--------------------------------#
    public function findOrderById($id)
    {
        return $this->repo->find($id);
    }

//------------------------------- Update Order Status-----------------------------------------------------///////////
    public function updateOrderStatus($order_status, $order_id)
    {
        $order = $this->findOrderById($order_id);
        $order->setOrderStatus($order_status);
        $this->save($order);
    }


    //-------- update Payment transaction status of order ----------------------------------------////////////
    public function updateUserTransaction($order_id, $transaction_id, $transaction_status, $payment_method, $payment_json, $order_number, $order_date, $rate, $sales_tax)
    {
        $order = $this->findOrderById($order_id);
        $order->setTransactionId($transaction_id);
        $order->setTransactionStatus($transaction_status);
        $order->setPaymentMethod($payment_method);
        $order->setPaymentJson($payment_json);
        $order->setOrderNumber($order_number);
        $order->setRateJson($rate);
        $order->setSalesTax($sales_tax);

        $order->setUserOrderDate(new \DateTime($order_date));
        $this->save($order);
    }

    public function getListWithPagination($page_number, $sort)
    {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllOrders($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('order' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'sort' => $sort,
        );
    }

    // my orders frontend display user's order
    public function getListWithPaginationByUser($page_number, $sort, $user)
    {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllOrdersByUser($page_number, $limit, $user, $sort);
        $rec_count = count($this->repo->countAllRecordByUser($user));
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        $track_information = $this->container->get('cart.helper.shipping')->getTrackingInformation();
        if ($track_information['TRACKRESPONSE']['RESPONSE']['RESPONSESTATUSDESCRIPTION'] == 'Success') {
            $tracking_number = $track_information["TRACKRESPONSE"]['SHIPMENT']['SHIPMENTIDENTIFICATIONNUMBER'];
            $current_status = $track_information["TRACKRESPONSE"]['SHIPMENT']["PACKAGE"]["ACTIVITY"]["STATUS"]["STATUSTYPE"]["DESCRIPTION"];


        } else {
            $tracking_number = '';
            $current_status = '';
        }
        return array('order' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'sort' => $sort,
            'current_status' => $current_status,
            'tracking_number' => $tracking_number,
            'sort' => $sort,
        );
    }

    public function getRecordsCountWithCurrentOrderLimit($brand_id)
    {

        return $this->repo->getRecordsCountWithCurrentOrderLimit($brand_id);
    }

    //-------------------------
    public function save($billing)
    {
        $class = $this->class;
        $billing->setOrderDate(new \DateTime('now'));
        $this->em->persist($billing);
        $this->em->flush();
        return $billing;
    }

    //-------------------------Create New Order--------------------------------------------

    public function createNew()
    {
        $class = $this->class;
        $cart = new $class();
        return $cart;
    }


//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id)
    {

        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('cart' => $entity,
                'message' => 'The Item has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('cart' => $entity,
                'message' => 'Cart not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//----------------------Find Order By ID----------------------------------------------------------------

    public function find($id)
    {
        return $this->repo->find($id);
    }

    #--------------------Find All Orders ---------------------------------------------------------------------------------
    public function findAll()
    {
        return $this->repo->findAll();
    }

//----------------------Find Order By name----------------------------------------------------------------
    public function findOneByName($name)
    {
        return $this->repo->findOneByName($name);
    }

    public function searchOrderByDiscount($data)
    {
        $draw = isset ( $data['draw'] ) ? intval( $data['draw'] ) : 0;
        $user_id = isset ( $data['user_id'] ) ? intval( $data['user_id'] ) : 0;
        $group_id = isset ( $data['group_id'] ) ? intval( $data['group_id'] ) : 0;
        //length
        $length  = $data['length'];
        $length  = $length && ($length!=-1) ? $length : 0;
        //limit
        $start   = $data['start'];
        $start   = $length ? ($start && ($start!=-1) ? $start : 0) / $length : 0;
        //order by
        $order   = $data['order'];
        //search data
        $search  = $data['search'];
        $filters = [
            'query' => @$search['value']
        ];

        $finalData = $this->repo->searchOrderByDiscount($filters, $start, $length, $order, true, $user_id, $group_id );

        $output = array(
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->searchOrderByDiscount($filters, 0, false, $order, true, $user_id, $group_id)),
            'recordsTotal'    => count($this->repo->searchOrderByDiscount(array(), 0, false, $order, true, $user_id, $group_id)),
            'data'            => array()
        );
        foreach ($finalData as $fData) {
            $output['data'][] = [
                'id'           => $fData["id"],
                'order_number' => $fData["order_number"],
                'user_name'    => ($fData["billing_first_name"] . " ". $fData["billing_last_name"]),
                'order_date'   => ($fData["order_date"]->format('d-m-Y')),
                'order_amount' => "$" . number_format((float)$fData["order_amount"], 2, '.', ''),
                'credit_card'  => "xxxx-xxxx-xxxx-".json_decode($fData['payment_json'])
                        ->transaction->_attributes->creditCard->last4,
                'transaction_status' => $fData['transaction_status'],
                'shipping_status' => $fData['order_status']
            ];
        }

        return $output;
    }

    public function search($data)
    {
        $draw = isset ( $data['draw'] ) ? intval( $data['draw'] ) : 0;
        //length
        $length  = $data['length'];
        $length  = $length && ($length!=-1) ? $length : 0; 
        //limit
        $start   = $data['start']; 
        $start   = $length ? ($start && ($start!=-1) ? $start : 0) / $length : 0; 
        //order by
        $order   = $data['order'];
        //search data
        $search  = $data['search'];
        $filters = [
            'query' => @$search['value']
        ];

        $finalData = $this->repo->search($filters, $start, $length, $order);

        $output = array( 
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->search($filters, 0, false, $order)),
            'recordsTotal'    => count($this->repo->search(array(), 0, false, $order)),
            'data'            => array()
        );
        foreach ($finalData as $fData) {
            $output['data'][] = [ 
                'id'           => $fData["id"],
                'order_number' => $fData["order_number"],
                'user_name'    => ($fData["billing_first_name"] . " ". $fData["billing_last_name"]),
                'order_date'   => ($fData["order_date"]->format('d-m-Y')),
                'order_amount' => "$" . number_format((float)$fData["order_amount"], 2, '.', ''),
                'credit_card'  => "xxxx-xxxx-xxxx-".json_decode($fData['payment_json'])
                    ->transaction->_attributes->creditCard->last4,
                'transaction_status' => $fData['transaction_status'],
                'shipping_status' => $fData['order_status']
            ];
        }

        return $output;
    }

    public function findOrderListByUserID($user_id)
    {
        return $this->repo->findOrderListByUserID($user_id);
    }

    public function findBasicOrderListByUserID($user_id)
    {
        return $this->repo->findBasicOrderListByUserID($user_id);
    }

    public function findOrderList()
    {
        return $this->repo->findOrderList();
    }

    public function updateTransactionStatus( UserOrder $entity, $transactionStatus)
    {
        $entity->setTransactionStatus( $transactionStatus );
        $this->save($entity);
    }

    public function updateWithShippingData( UserOrder $entity, $shipping_response)
    {
        $entity->setShipmentJson($shipping_response);
        $this->save($entity);
    }

    public function findUserByOrderId($order_id)
    {
        return $this->repo->findUserByOrderId($order_id);
    }

    public function findByOrderNo($order_number)
    {
        return $this->repo->findByOrderNo($order_number);   
    }

}