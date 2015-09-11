<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class OrderDetailHelper {

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
///------------------------------- Add to Cart clicked -----------------------------------------------------///////////
  public function saveOrderDetail($user_cart,$user_order) {
	//$user_order=$this->container->get('cart.helper.order')->find($order_id);
	for($i=0;$i<count($user_cart["price"]);$i++){
	  $product_item=$this->container->get('admin.helper.productitem')->find($user_cart["item_id"][$i]);
	  $item_description = $product_item->getProduct()->getBrand()->getName()." ".$product_item->getProduct()->getName()." ".$product_item->getproductColor()->getTitle()." ".$product_item->getproductSize()->getTitle();
	  $user_order_detail = $this->createNew();
	  $user_order_detail->setUserOrder($user_order);
	  $user_order_detail->setQty($user_cart["qty"][$i]);
	  $user_order_detail->setProductItem($product_item);
	  $user_order_detail->setItemDescription($item_description);
	  $user_order_detail->setAmount($user_cart["total"][$i]);
	  $user_order_detail->setSku($product_item->getSku());
	  $this->save($user_order_detail);

	}
	return "saved";

  }

#------------------------------Find cart by id--------------------------------#
  public function findCartById($id){
	return $this->repo->find($id);
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
	public function save($user_order_detail) {
	  $class = $this->class;
	  $user_order_detail->setDateTime(new \DateTime('now'));
	  $this->em->persist($user_order_detail);
	  $this->em->flush();
	  return $user_order_detail;
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