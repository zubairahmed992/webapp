<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSWishlistController extends Controller
{

    // Add Multiple Item to Wishlist
    public function addItemsToWishlistAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $items = isset($decoded["items"]) ? $decoded["items"] : "0";
            if ($items != 0) {
                $response = $this->container->get('cart.helper.wishlist')->removeUserWishlist($user);
                if ($response != null) {
                    foreach ($items as $detail) {
                        /* Find this item on the cart and remove */
                        $this->container->get('cart.helper.cart')->removeCartByItem($user, $detail["item_id"]);
                        $this->container->get('cart.helper.cart')->fillWishlist($detail["item_id"], $user, $detail["quantity"]);
                    }
                    $resp = 'Items has been added to Wishlist Successfully';
                    $res = $this->get('webservice.helper')->response_array(true, $resp);
                } else {
                    $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
                }
            } else {
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    // Remove User Cart
    public function removeUserWishlistAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $response = $this->container->get('cart.helper.wishlist')->removeUserWishlist($user);
            if ($response != null) {
                $resp = 'Wishlist has been removed';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    // Remove User Cart
    public function removeUserItemAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $product_item = $decoded["item_id"];
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $response = $this->container->get('cart.helper.wishlist')->removeWishlistByItem($user, $product_item);
            if ($response != null) {
                $resp = 'Wishlist Item has been removed';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    // Show User Cart
    public function showUserWishlistAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $resp = $this->container->get('cart.helper.wishlist')->getUserWishlist($user);
            if($resp){
                $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
                foreach ($resp as $key => $value) {
                    $resp[$key]['image'] = $base_path . $value['image'];
                }

                $res = $this->get('webservice.helper')->response_array(true, "item found", true, $resp);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'No Item Found.');
            }

        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

//*********************************************
// Webservice For 3.0
//**********************************************
    // Show User Wishlist Web 3.0
    public function showUserWishlistWithNameDescriptionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $resp = $this->container->get('cart.helper.wishlist')->getUserWishlistWithNameDescription($user);
            $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
            foreach ($resp as $key => $value) {
                $resp[$key]['image'] = $base_path . $value['image'];
            }

            $res = $this->get('webservice.helper')->response_array(true, 'success', true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    #----------------------------------------------------Wishlist Services -------------------------#

    // Add Single Item to Cart Version 3.0
    public function addItemToWishlistNewAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $item_id = $decoded["item_id"];
            $qty = $decoded["quantity"];

            /* Find this item on the cart and remove */
            $response = $this->container->get('cart.helper.cart')->removeCartByItem($user, $item_id);
            if ($response != null) {
                $this->container->get('cart.helper.wishlist')->fillWishlistforService($item_id, $user, $qty);
                $resp = 'Item has been added to Wishlist Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    private function addItemToUserWishlist($user, $post_variable)
    {
        $items = isset($post_variable["items"]) ? $post_variable["items"] : "0";
        if ($items != 0) {
            $response = $this->container->get('cart.helper.wishlist')->removeUserWishlist($user);
            if ($response != null) {
                foreach ($items as $detail) {
                    $this->container->get('cart.helper.wishlist')->fillWishlist($detail["item_id"], $user, $detail["quantity"]);
                }
                return true;
            }
        }
        return false;
    }
}