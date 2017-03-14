<?php

namespace LoveThatFit\CartBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class WishlistHelper
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
    public function fillWishlist($product_item_id, $user, $qty)
    {
        $product_item = $this->container->get('admin.helper.productitem')->find($product_item_id);
        $result = $this->findWishlistByUserId($user, $product_item);
        if (isset($result['qty']) > 0) {
            $id = $result["id"];
            $wishlist = $this->findWishlistById($id);
            $setQty = $result["qty"] + $qty;
            $wishlist->setQty($setQty);
            return $this->save($wishlist);
        } else {
            $wishlist = $this->createNew();
            $wishlist->setProductitem($product_item);
            $wishlist->setUser($user);
            $wishlist->setQty($qty);
            return $this->save($wishlist);

        }
    }

    //------------------------------- Remove User Cart -----------------------------------------------------///////////
    public function removeUserWishlist($user)
    {
        return $this->repo->removeWishlistByUser($user);
    }

    //------------------------------- Remove User Cart -----------------------------------------------------///////////
    public function removeWishlistByItem($user, $product_item)
    {
        return $this->repo->removeWishlistByItem($user, $product_item);
    }

#------------------------------Find cart by id--------------------------------#
    public function findWishlistById($id)
    {
        return $this->repo->find($id);
    }

    #------------------------------Count Items cart by user id--------------------------------#
    public function countWishlistItems($user)
    {
        return $this->repo->countWishlistByUser($user);
    }

    #------------------------------Count Items cart by user id amd Quantity--------------------------------#
    public function countWishlistItemsByQuantity($user)
    {
        return $this->repo->countWishlistByUserQuantity($user);
    }

#------------------------------Find cart by id--------------------------------#
    public function findWishlistByUserId($user, $product_item)
    {
        return $this->repo->findOneByUserItem($user, $product_item);
    }

    //-------- update Quantity of Cart if item already in cart ----------------------------------------////////////
    public function updateWishlist($decoded)
    {
        $qty = $decoded["qty"];
        for ($i = 0; $i < count($qty); $i++) {
            $id = $decoded["id"][$i];
            $wishlist = $this->findWishlistById($id);
            $wishlist->setQty($decoded["qty"][$i]);
            $this->save($wishlist);
        }
    }

    //-------- update Quantity of Cart Ajax for item already in cart ----------------------------------------////////////
    public function updateWishlistAjax($id, $qty)
    {
        $wishlist = $this->findWishlistById($id);
        $wishlist->setQty($qty);
        $this->save($wishlist);
        return true;
    }

#------------------------------Get Cart by User--------------------------------#
    public function getWishlist($user)
    {
        $wishlist_array = array();
        foreach ($user->getWishlist() as $ci) {
            $wishlist_array['price'][] = $ci->getProductItem()->getPrice();
            $wishlist_array['total'][] = $ci->getProductItem()->getPrice() * $ci->getQty();
        }
        if (count($wishlist_array) == 0) {
            $grand_total = 0;
        } else {
            $grand_total = array_sum($wishlist_array["total"]);
        }
        return $grand_total;
    }

#------------------------------Get Formatted Cart Data by User--------------------------------#
    public function getFormattedWishlist($user)
    {
        $wishlist_array = array();
        foreach ($user->getWishlist() as $ci) {
            $wishlist_array['price'][] = $ci->getProductItem()->getPrice();
            $wishlist_array['total'][] = $ci->getProductItem()->getPrice() * $ci->getQty();
            $wishlist_array['qty'][] = $ci->getQty();
            $wishlist_array['item_id'][] = $ci->getProductItem()->getId();
        }
        return $wishlist_array;
    }

#------------------------------Get User Cart for service--------------------------------#
    public function getUserWishlist($user)
    {
        $cart_array = array();
        $counter = 0;
        foreach ($user->getWishlist() as $ci) {
            $wishlist_array[$counter]['product_id'] = $ci->getProductItem()->getProduct()->getId();
            $wishlist_array[$counter]['price'] = $ci->getProductItem()->getPrice();
            $wishlist_array[$counter]['qty'] = $ci->getQty();
            $wishlist_array[$counter]['item_id'] = $ci->getProductItem()->getId();
            $get_path = $ci->getProductItem()->getProductColor()->getImagePaths();
            $cart_array[$counter]['image'] = $get_path["iphone6_list"];
            $counter++;
        }
        return $wishlist_array;
    }

    //-------------------------
    public function save($wishlist)
    {
        $class = $this->class;
        $wishlist->setDateTime(new \DateTime('now'));
        $this->em->persist($wishlist);
        $this->em->flush();
        return $wishlist;
    }

    //-------------------------Create New Brand--------------------------------------------

    public function createNew()
    {
        $class = $this->class;
        $wishlist = new $class();
        return $wishlist;
    }


//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id)
    {

        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('wishlist' => $entity,
                'message' => 'The Item has been Removed!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('wishlist' => $entity,
                'message' => 'Wishlist not found!',
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
    public function fillWishlistforService($product_item_id, $user, $qty)
    {
        $product_item = $this->container->get('admin.helper.productitem')->find($product_item_id);
        $result = $this->findWishlistByUserId($user, $product_item);
        if (isset($result['qty']) > 0) {
            $id = $result["id"];
            $wishlist = $this->findWishlistById($id);
            $setQty = $result["qty"] + $qty;
            $wishlist->setQty($setQty);
            return $this->save($wishlist);
        } else {
            $wishlist = $this->createNew();
            $wishlist->setProductitem($product_item);
            $wishlist->setUser($user);
            $wishlist->setQty($qty);
            return $this->save($wishlist);

        }
    }


// Show User Cart
    public function getUserWishlistWithNameDescription($user)
    {
        $wishlist_array = array();
        $counter = 0;
        foreach ($user->getWishlist() as $ci) {
            $wishlist_array[$counter]['color'] = $ci->getProductItem()->getProductColor()->getTitle();
            $wishlist_array[$counter]['size'] = $ci->getProductItem()->getProductSize()->getTitle();
            $wishlist_array[$counter]['name'] = $ci->getProductItem()->getProduct()->getName();
            $wishlist_array[$counter]['description'] = $ci->getProductItem()->getProduct()->getDescription();
            $wishlist_array[$counter]['price'] = $ci->getProductItem()->getPrice();
            $wishlist_array[$counter]['qty'] = $ci->getQty();
            $wishlist_array[$counter]['item_id'] = $ci->getProductItem()->getId();
            $get_path = $ci->getProductItem()->getProductColor()->getImagePaths();
            $wishlist_array[$counter]['image'] = $get_path["iphone6_list"];
            $counter++;
        }
        return $wishlist_array;
    }


}