<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSCartController extends Controller {

    #----------------------------------------------------Shopping Cart Services -------------------------#
    // Add Single Item to Cart
    public function addItemToCartAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $item_id = $decoded["item_id"];
            $qty = $decoded["quantity"];
            $this->container->get('cart.helper.cart')->fillCart($item_id,$user,$qty);
            $resp = 'Item has been added to Cart Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    // Add Multiple Item to Cart
    public function addItemsToCartAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $items = isset($decoded["items"])?$decoded["items"]:"0";
            if($items != 0){
                $this->container->get('cart.helper.cart')->removeUserCart($user);
            foreach($items as $detail){
                $this->container->get('cart.helper.cart')->fillCart($detail["item_id"],$user,$detail["quantity"]);
            }
            $resp = 'Items has been added to Cart Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    // Remove User Cart
    public function removeUserCartAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
                $this->container->get('cart.helper.cart')->removeUserCart($user);
            $resp = 'Cart has been removed';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    // Remove User Cart
    public function removeUserItemAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $product_item = $decoded["item_id"];
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $this->container->get('cart.helper.cart')->removeCartByItem($user,$product_item);
            $resp = 'Cart Item has been removed';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    // Show User Cart
    public function showUserCartAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $resp = $this->container->get('cart.helper.cart')->getUserCart($user);
            $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
            foreach($resp as $key => $value)
            {
                $resp[$key]['image'] = $base_path.$value['image'];
            }

            $res = $this->get('webservice.helper')->response_array(true, json_encode($resp));
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

//*********************************************
// Webservice For 3.0
//**********************************************
    // Show User Cart Web 3.0
    public function showUserCartWithNameDescriptionAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $resp = $this->container->get('cart.helper.cart')->getUserCartWithNameDescription($user);
            $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
            foreach($resp as $key => $value)
            {
                $resp[$key]['image'] = $base_path.$value['image'];
            }

            $res = $this->get('webservice.helper')->response_array(true, 'success', true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    #----------------------------------------------------Shopping Cart Services -------------------------#

    // Add Single Item to Cart Version 3.0
    public function addItemToCartNewAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $item_id = $decoded["item_id"];
            $qty = $decoded["quantity"];

            $this->container->get('cart.helper.cart')->fillCartforService($item_id,$user,$qty);
            $resp = 'Item has been added to Cart Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    public function getAuthTokenAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $clientToken = $this->get('cart.helper.payment')->getClientToken();
            $res = $clientToken;
        } else{
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    public function makePaymentAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $result = $this->get('cart.helper.payment')->webServiceTransaction($user, $decoded );
            $res = $result;
        } else{
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }
}

