<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class OrderHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
//------------------------------- Add to Cart clicked -----------------------------------------------------///////////
	public function saveBillingShipping($decoded,$user) {
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
		return $this->save($user_billing_shipping_info);

	}

#------------------------------Find cart by id--------------------------------#
  public function findCartById($id){
	return $this->repo->find($id);
  }
#------------------------------Find user billing address--------------------------------#
  public function getUserBillingAddress($user){
	return $this->repo->findOneByUserAddress($user);
  }
#-------------------------- Check Valid user for an order -----------------#
  public function HasUserOrder($order_id,$user){
	return $this->findOrderByUserId($order_id,$user);
  }

#------------------------------Find cart by id--------------------------------#
  public function findOrderByUserId($order_id,$user){
	return $this->repo->findOneByUser($order_id,$user);
  }
#------------------------------Find order by id--------------------------------#
  public function findOrderById($id){
	return $this->repo->find($id);
  }
//------------------------------- Update Order Status-----------------------------------------------------///////////
  public function updateOrderStatus($order_status,$order_id) {
	$order=$this->findOrderById($order_id);
	$order->setOrderStatus($order_status);
	$this->save($order);
  }



  //-------- update Payment transaction status of order ----------------------------------------////////////
  public function updateUserTransaction($order_id,$transaction_id,$transaction_status,$payment_method,$payment_json,$order_number) {
	  $order=$this->findOrderById($order_id);
	  $order->setTransactionId($transaction_id);
	  $order->setTransactionStatus($transaction_status);
	  $order->setPaymentMethod($payment_method);
	  $order->setPaymentJson($payment_json);
	  $order->setOrderNumber($order_number);
	  $this->save($order);
  }

  public function getListWithPagination($page_number, $sort) {
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
	  'sort'=>$sort,
	);
  }

  // my orders frontend display user's order
  public function getListWithPaginationByUser($page_number, $sort,$user) {
	$yaml = new Parser();
	$pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
	$limit = $pagination_constants["constants"]["pagination"]["limit"];

	$entity = $this->repo->listAllOrdersByUser($page_number, $limit,$user, $sort);
	$rec_count = count($this->repo->countAllRecordByUser($user));
	//$rec_count = $rec_count[0]["id"];
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
	  'sort'=>$sort,
	);
  }

  public function getRecordsCountWithCurrentOrderLimit($brand_id){

	return $this->repo->getRecordsCountWithCurrentOrderLimit($brand_id);
  }

#------------------------------ Get Cart Grand Total--------------------------------#
//  public function getCartGrandTotal($decoded){
//		print_r($decoded);die;
//	for($i=0;$i<=count($decoded);$i++){
//	  echo $decoded["product_item"][$i];die;
//	  $product_item=$this->container->get('admin.helper.productitem')->find($decoded["product_item"][$i]);
//	  echo $product_item->getPrice();
//	  //$grand_total+=
//	}
//  }
//#------------------------------Find cart by id--------------------------------#
//  public function findCartByUserId($user,$product_item){
//	return $this->repo->findOneByUserItem($user,$product_item);
//  }
//	//-------- update Quantity of Cart if item already in cart ----------------------------------------////////////
//  public function updateCart($decoded) {
//	  $qty = $decoded["qty"];
//	  for($i=0;$i<count($qty);$i++){
//		$id = $decoded["id"][$i];
//		$cart=$this->findCartById($id);
//		$cart->setQty($decoded["qty"][$i]);
//		$this->save($cart);
//	  }
//  }
//#------------------------------Get Cart by User--------------------------------#
//  public function getCart($user){
//	$cart_array=array();
//	foreach($user->getCart() as $ci){
//	  	$cart_array['price'][]=$ci->getProductItem()->getPrice();
//	  	$cart_array['total'][]=$ci->getProductItem()->getPrice()*$ci->getQty();
//	}
//	return $cart_array;
//  }

	//-------------------------
	public function save($billing) {
	  $class = $this->class;
	  $billing->setOrderDate(new \DateTime('now'));
	  $this->em->persist($billing);
	  $this->em->flush();
	  return $billing;
	}
  //-------------------------Create New Brand--------------------------------------------

    public function createNew() {
        $class = $this->class;
        $cart = new $class();
        return $cart;
    }



//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        //$entity_name = $entity->getName();
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

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All Orders ---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------Find Order By name----------------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }




   
}