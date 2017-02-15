<?php

namespace LoveThatFit\CartBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class CartHelper
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

//------------------------------- Add to Cart clicked -----------------------------------------------------///////////
    public function fillCart($product_item_id, $user, $qty)
    {
        $product_item = $this->container->get('admin.helper.productitem')->find($product_item_id);
        $result = $this->findCartByUserId($user, $product_item);
        if (isset($result['qty']) > 0) {
            $id = $result["id"];
            $cart = $this->findCartById($id);
            $setQty = $result["qty"] + $qty;
            $cart->setQty($setQty);
            return $this->save($cart);
        } else {
            $cart = $this->createNew();
            $cart->setProductitem($product_item);
            $cart->setUser($user);
            $cart->setQty($qty);
            return $this->save($cart);

        }
    }

    //------------------------------- Remove User Cart -----------------------------------------------------///////////
    public function removeUserCart($user)
    {
        return $this->repo->removeCartByUser($user);
    }

    //------------------------------- Remove User Cart -----------------------------------------------------///////////
    public function removeCartByItem($user, $product_item)
    {
        return $this->repo->removeCartByItem($user, $product_item);
    }

#------------------------------Find cart by id--------------------------------#
    public function findCartById($id)
    {
        return $this->repo->find($id);
    }

    #------------------------------Count Items cart by user id--------------------------------#
    public function countCartItems($user)
    {
        return $this->repo->countCartByUser($user);
    }

    #------------------------------Count Items cart by user id amd Quantity--------------------------------#
    public function countCartItemsByQuantity($user)
    {
        return $this->repo->countCartByUserQuantity($user);
    }

#------------------------------Find cart by id--------------------------------#
    public function findCartByUserId($user, $product_item)
    {
        return $this->repo->findOneByUserItem($user, $product_item);
    }

    //-------- update Quantity of Cart if item already in cart ----------------------------------------////////////
    public function updateCart($decoded)
    {
        $qty = $decoded["qty"];
        for ($i = 0; $i < count($qty); $i++) {
            $id = $decoded["id"][$i];
            $cart = $this->findCartById($id);
            $cart->setQty($decoded["qty"][$i]);
            $this->save($cart);
        }
    }

    //-------- update Quantity of Cart Ajax for item already in cart ----------------------------------------////////////
    public function updateCartAjax($id, $qty)
    {
        $cart = $this->findCartById($id);
        $cart->setQty($qty);
        $this->save($cart);
        return true;
    }

#------------------------------Get Cart by User--------------------------------#
    public function getCart($user)
    {
        $cart_array = array();
        foreach ($user->getCart() as $ci) {
            $cart_array['price'][] = $ci->getProductItem()->getPrice();
            $cart_array['total'][] = $ci->getProductItem()->getPrice() * $ci->getQty();
        }
        if (count($cart_array) == 0) {
            $grand_total = 0;
        } else {
            $grand_total = array_sum($cart_array["total"]);
        }
        return $grand_total;
    }

#------------------------------Get Formatted Cart Data by User--------------------------------#
    public function getFormattedCart($user)
    {
        $cart_array = array();
        foreach ($user->getCart() as $ci) {
            $cart_array['price'][] = $ci->getProductItem()->getPrice();
            $cart_array['total'][] = $ci->getProductItem()->getPrice() * $ci->getQty();
            $cart_array['qty'][] = $ci->getQty();
            $cart_array['item_id'][] = $ci->getProductItem()->getId();
        }
        return $cart_array;
    }

#------------------------------Get User Cart for service--------------------------------#
    public function getUserCart($user)
    {
        $cart_array = array();
        $counter = 0;
        foreach ($user->getCart() as $ci) {
            $cart_array[$counter]['price'] = $ci->getProductItem()->getPrice();
            $cart_array[$counter]['qty'] = $ci->getQty();
            $cart_array[$counter]['item_id'] = $ci->getProductItem()->getId();
            $get_path = $ci->getProductItem()->getProductColor()->getImagePaths();
            $cart_array[$counter]['image'] = $get_path["iphone6_list"];
            $counter++;
        }
        return $cart_array;
    }

    //-------------------------
    public function save($cart)
    {
        $class = $this->class;
        $cart->setDateTime(new \DateTime('now'));
        $this->em->persist($cart);
        $this->em->flush();
        return $cart;
    }

    //-------------------------Create New Brand--------------------------------------------

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
        //$entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('cart' => $entity,
                'message' => 'The Item has been Removed!',
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

//----------------------Find Cart By ID----------------------------------------------------------------

    public function find($id)
    {
        return $this->repo->find($id);
    }

    #--------------------Find All Carts---------------------------------------------------------------------------------
    public function findAll()
    {
        return $this->repo->findAll();
    }

//----------------------Find Cart By name----------------------------------------------------------------
    public function findOneByName($name)
    {
        return $this->repo->findOneByName($name);
    }


//*********************************************
// Webservice For 3.0
//**********************************************


//------------------------------- Add to Cart clicked Clone this after discusion with Ovais -----------------------///////////
    public function fillCartforService($product_item_id, $user, $qty)
    {
        $product_item = $this->container->get('admin.helper.productitem')->find($product_item_id);
        $result = $this->findCartByUserId($user, $product_item);
        if (isset($result['qty']) > 0) {
            $id = $result["id"];
            $cart = $this->findCartById($id);
            $setQty = $result["qty"] + $qty;
            $cart->setQty($setQty);
            return $this->save($cart);
        } else {
            $cart = $this->createNew();
            $cart->setProductitem($product_item);
            $cart->setUser($user);
            $cart->setQty($qty);
            return $this->save($cart);

        }
    }


// Show User Cart
    public function getUserCartWithNameDescription($user)
    {
        $cart_array = array();
        $counter = 0;
        foreach ($user->getCart() as $ci) {
            $cart_array[$counter]['color'] = $ci->getProductItem()->getProductColor()->getTitle();
            $cart_array[$counter]['size'] = $ci->getProductItem()->getProductSize()->getTitle();
            $cart_array[$counter]['name'] = $ci->getProductItem()->getProduct()->getName();
            $cart_array[$counter]['description'] = $ci->getProductItem()->getProduct()->getDescription();
            $cart_array[$counter]['price'] = $ci->getProductItem()->getPrice();
            $cart_array[$counter]['qty'] = $ci->getQty();
            $cart_array[$counter]['item_id'] = $ci->getProductItem()->getId();
            $get_path = $ci->getProductItem()->getProductColor()->getImagePaths();
            $cart_array[$counter]['image'] = $get_path["iphone6_list"];
            $counter++;
        }
        return $cart_array;
    }


}