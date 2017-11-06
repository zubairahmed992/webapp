<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use LoveThatFit\CartBundle\Utils\Stamps;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class WSCartController extends Controller
{

    #----------------------------------------------------Shopping Cart Services -------------------------#
    // Add Single Item to Cart
    public function addItemToCartAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $item_id = $decoded["item_id"];
            $itemObject = $this->container->get('admin.helper.productitem')->find($item_id);
            if(is_object($itemObject) && $itemObject->getProduct()->getDisabled() == false){
                $qty = $decoded["quantity"];
                $this->container->get('cart.helper.cart')->fillCart($item_id, $user, $qty);
                $resp = 'Item has been added to Cart Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Item does not exists or out of stock.');
            }

        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    // Add Multiple Item to Cart
    public function addItemsToCartAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $items = isset($decoded["items"]) ? $decoded["items"] : "0";
            if ($items != 0) {
                $response = $this->container->get('cart.helper.cart')->removeUserCart($user);
                if ($response != null) {
                    foreach ($items as $detail) {
                        $this->container->get('cart.helper.cart')->fillCart($detail["item_id"], $user, $detail["quantity"]);
                    }
                    $resp = 'Items has been added to Cart Successfully';
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
    public function removeUserCartAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $response = $this->container->get('cart.helper.cart')->removeUserCart($user);
            if($response != null){
                $resp = 'Cart has been removed';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            }else{
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
            $response = $this->container->get('cart.helper.cart')->removeCartByItem($user, $product_item);
            if($response !== null){
                $resp = 'Cart Item has been removed';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }

        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    // Show User Cart
    public function showUserCartAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $resp = $this->container->get('cart.helper.cart')->getUserCart($user);
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
    // Show User Cart Web 3.0
    public function showUserCartWithNameDescriptionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $cartresp = $this->container->get('cart.helper.cart')->getUserCartWithNameDescription($user);
            $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
            foreach ($cartresp as $key => $value) {
                $cartresp[$key]['image'] = $base_path . $value['image'];
            }

            $wishlistresp = $this->container->get('cart.helper.wishlist')->getUserWishlistWithNameDescription($user);
            $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
            foreach ($wishlistresp as $key => $value) {
                $wishlistresp[$key]['image'] = $base_path . $value['image'];
            }

            $cartList = $cartresp;
            $wishlistList = $wishlistresp;

            $cartconf= array(
                'data' => $cartList,
                'count'=> count($cartList),
                'message' => 'Cart list',
                'success' => 'true',
            );

            $wishlistconf= array(
                'data' => $wishlistList,
                'count'=> count($wishlistList),
                'message' => 'Wish list',
                'success' => 'true',
            );

            $data = array(
                'count'=> 2,
                'message' => 'Success Result',
                'success' => 1,
            );
            $data['cart'] = $cartconf;
            $data['wishlist'] = $wishlistconf;

            return new Response(json_encode($data));

        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    #----------------------------------------------------Shopping Cart Services -------------------------#

    // Add Single Item to Cart Version 3.0
    public function addItemToCartNewAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $item_id = $decoded["item_id"];
            $qty = $decoded["quantity"];
            $requested_screen = $decoded["display_screen"];

            /* IOSV3-252 - From the Product Detail page, if product item already exist then quantity not change  */
            if($requested_screen == "detail_page"){
                $product_item = $this->container->get('admin.helper.productitem')->find($item_id);
                $find_item_against_user = $this->container->get('cart.helper.cart')->findCartByUserId($user, $product_item);
                if(!empty($find_item_against_user["qty"])){
                    $qty = $find_item_against_user["qty"];
                }
            }

            /*Remove Item from wishlist */
            $this->container->get('cart.helper.wishlist')->removeWishlistByItem($user, $item_id);
            $response = $this->container->get('cart.helper.cart')->fillCartforService($item_id, $user, $qty);
            if ($response != null) {
                $resp = 'Item has been added to Cart Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }


    // Add Multiple Item to Cart
    public function addItemsToCartNewAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $items = isset($decoded["items"]) ? $decoded["items"] : "0";
            if ($items != 0) {
                //$this->container->get('cart.helper.cart')->removeUserCart($user);
                foreach ($items as $detail) {
                    /*Remove From Wish list*/
                    $this->container->get('cart.helper.wishlist')->removeWishlistByItem($user, $detail["item_id"]);
                    $this->container->get('cart.helper.cart')->fillCartforService($detail["item_id"], $user, $detail["quantity"]);
                }
                $resp = 'Items has been added to Cart Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

    public function getAuthTokenAction()
    {
        /*$decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $clientToken = $this->get('cart.helper.payment')->getClientToken();
            $res = $clientToken;
        } else{
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }*/

        $clientToken = $this->get('cart.helper.payment')->getClientToken();
        if ($clientToken) {
            $res = $clientToken;
        } else {
            $res = "some thing went wrong, try again later";
        }


        return new Response($res);
    }

    public function brainTreeGetClientTokerAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $result = $this->get('cart.helper.payment')->getClientTokenById( $user );
            $res = $this->get('webservice.helper')->response_array(true, 'success', true, $result);
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
            $result = $this->get('cart.helper.payment')->webServiceTransaction($user, $decoded);
            $res = $result;
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    public function brainTreePaymentWithAddItemToCartAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if($this->addItemToUserCart( $user, $decoded)){
                $result = $this->get('cart.helper.payment')->webServiceBrainTreeTransaction($user, $decoded);
                if($result['success'] == 0)
                {
                    $res = $this->get('webservice.helper')->response_array(true, 'successfully complete transaction', true, $result);
                }else if($result['success'] < 0)
                {
                    $res = $this->get('webservice.helper')->response_array(false, 'some thing went wrong', true, $result);
                }
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    private function addItemToUserCart($user, $post_variable)
    {
        $items = isset($post_variable["items"]) ? $post_variable["items"] : "0";
        if ($items != 0) {
            $this->container->get('cart.helper.cart')->removeUserCart($user);
            foreach ($items as $detail) {
                $this->container->get('cart.helper.cart')->fillCart($detail["item_id"], $user, $detail["quantity"]);
            }
            return true;
        } else {
            return false;
        }
    }

    private function addItemsToPostArray(User $user, $decoded = array())
    {
        $keys = array('price', 'qty', 'item_id');
        $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
        $itemsArray = array();
        foreach($user_cart as $key => $items)
        {
            if(in_array($key, $keys))
            {
                for($i=0; $i < count($user_cart[$key]); $i++){
                    if($key == 'qty'){
                        $itemsArray[$i]['quantity'] = $user_cart[$key][$i];
                    }else if ($key == 'price'){
                        $itemsArray[$i]['unit_price'] = $user_cart[$key][$i];
                    }else{
                        $itemsArray[$i][$key] = $user_cart[$key][$i];
                    }
                }
            }
        }
        $decoded['items'] = $itemsArray;
        $itemsArray = array();

        return $decoded;
    }

    public function nwsBrainTreeProcessTransactionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $discount_amount    = $decoded['discount_amount'];

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $decoded = $this->addItemsToPostArray($user, $decoded);
        if(isset($decoded['billing_id']))
            $decoded['rates']['billing_id'] = $decoded['billing_id'];
        if(isset($decoded['shipping_id']))
            $decoded['rates']['shipping_id'] = $decoded['shipping_id'];
        if ($user) {
            $user_cart = $this->get('cart.helper.cart')->getFormattedCart($user);
            if(empty($user_cart)){
                $res = $this->get('webservice.helper')->response_array(false, 'User cart is empty.');
                return new Response( $res );
            }

            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
            if(is_array($fnfUser) && !empty($fnfUser))
            {
                $fnfGroupId = $fnfUser['group_id'];
                $decoded['groupId'] = $fnfGroupId;
                $decoded['discount'] = $fnfUser['discount'];
            }

             $result = $this->get('cart.helper.payment')->webServiceBrainTreeProcessUserTransaction($user, $decoded);
            if ($result['success'] == 0) {
                if($discount_amount > 0){
                    $fnfUser            = $this->get('fnfuser.helper.fnfuser')->getFNFUserById($user);
                    if(is_object($fnfUser)){
                        if($decoded['group_type'] == 1) {
                            $fnfUserAfterUpdate = $this->get('fnfuser.helper.fnfuser')->setIsAvailable($fnfUser);
                        }
                    }
                }

                $this->sendEmailToUser( $user, $decoded, $result);
                $this->sendEmailToAdmin( $user, $decoded, $result);

                $res = $this->get('webservice.helper')->response_array(true, 'successfully complete transaction', true, $result);
            } else if ($result['success'] < 0) {
                $res = $this->get('webservice.helper')->response_array(false, $result['response_code'], true, $result);
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function brainTreeProcessTransactionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $discount_amount    = $decoded['discount_amount'];

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $decoded = $this->addItemsToPostArray($user, $decoded);
        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
            if(is_array($fnfUser) && !empty($fnfUser))
            {
                $fnfGroupId = $fnfUser['group_id'];
                $decoded['groupId'] = $fnfGroupId;
                $decoded['discount'] = $fnfUser['discount'];
            }

            if(!empty($decoded['items'])) {
                $result = $this->get('cart.helper.payment')->webServiceBrainTreeProcessUserTransaction($user, $decoded);
                if ($result['success'] == 0) {
                    if ($discount_amount > 0) {
                        $fnfUser = $this->get('fnfuser.helper.fnfuser')->getFNFUserById($user);
                        $fnfUserAfterUpdate = $this->get('fnfuser.helper.fnfuser')->setIsAvailable($fnfUser);
                    }

                    $this->sendEmailToUser($user, $decoded, $result);
                    $this->sendEmailToAdmin($user, $decoded, $result);

                    $res = $this->get('webservice.helper')->response_array(true, 'successfully complete transaction', true, $result);
                } else if ($result['success'] < 0) {
                    $res = $this->get('webservice.helper')->response_array(false, 'some thing went wrong', true, $result);
                }
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'no items found in cart');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function ___brainTreeProcessTransactionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if($this->addItemToUserCart( $user, $decoded)) {
                $result = $this->get('cart.helper.payment')->webServiceBrainTreeProcessUserTransaction($user, $decoded);
                if ($result['success'] == 0) {
                    $this->sendEmailToUser( $user, $decoded, $result);
                    $this->sendEmailToAdmin( $user, $decoded, $result);
                    $res = $this->get('webservice.helper')->response_array(true, 'successfully complete transaction', true, $result);
                } else if ($result['success'] < 0) {
                    $res = $this->get('webservice.helper')->response_array(false, 'some thing went wrong', true, $result);
                }
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    private function sendEmailToUser( User $user, $decode, $result)
    {
        $current = date('d-m-Y');
        $items = isset($decode["items"]) ? $decode["items"] : "0";
        $itemsArray = array();
        foreach ($items as $detail) {
            $entity = $this->container->get('admin.helper.productitem')->find($detail['item_id']);
            $price_sum = $entity->getPrice() * $detail['quantity'];
            $itemsArray[] = array(
                'pname' => $entity->getProduct()->getName(),
                'quantity' => $detail['quantity'],
                'item_price'     => $entity->getPrice(),
                'price'     => number_format((float)$price_sum, 2, '.', ''),
                'sku'       => $entity->getProduct()->getControlNumber(),
                'size'      => $entity->getProductSize()->getTitle(),
                'color'     => $entity->getProductColor()->getTitle(),
                'brand'      => $entity->getProduct()->getBrand()->getName(),
            );
        }
        $orderNummber = $result['order_number'];
        $orderAmount = $decode['order_amount'];
        $totalAmount = $decode['total_amount'];
        $discount   = number_format((float)$decode['discount_amount'], 2, '.', ''); 
        $creditCard = $result['result']->transaction->creditCard;
        $billing    = $decode['billing'];
        $order_id = $result['order_id'];
        $salesTax  =   (isset($decode['sales_tax']) ? number_format((float)$decode['sales_tax'], 2, '.', '') : '0.00'); 
        $d_discount  =   (isset($decode['discount']) ? number_format((float)$decode['discount'], 2, '.', '') : '0.00'); 

        $orderEntity = $this->container->get('cart.helper.order')->findOrderById( $order_id );

        $dataArray = array(
            'purchase_date' => $orderEntity->getUserOrderDate()->format('F j, Y'),
            'items'         => $itemsArray,
            'order_numnber' => $orderNummber,
            'card_type'     => $creditCard['cardType'],
            'last_four_number' => $creditCard['last4'],
            'contact_number'    => '262-391-3403',
            'email'         => $user->getEmail(),
            'frist_name'    => $user->getFullName(),
            'order_amount'  => number_format((float)$orderAmount, 2, '.', ''),
            'total_amount'  => number_format((float)$totalAmount, 2, '.', ''),
            'discount'  => ($discount > 0 ? "-$".$discount : '0.00'),
            'discountType' => (isset($decode['group_type']) && $decode['group_type'] == 2 ? "(".$d_discount."%)" : ""),
            'phone_number'  => $user->getPhoneNumber(),
            'expirate_date' => $creditCard['expirationMonth']. "/". $creditCard['expirationYear'],
            'cardholderName' => $creditCard['cardholderName'],
            'shipping_first_name' => $billing['shipping_first_name'],
            'shipping_last_name' => $billing['shipping_last_name'],
            'shipping_address1' => $billing['shipping_address1'],
            'shipping_address2' => $billing['shipping_address2'],
            'shipping_phone' => $billing['shipping_phone'],
            'shipping_city' => $billing['shipping_city'],
            'shipping_postcode' => $billing['shipping_postcode'],
            'shipping_country' => $billing['shipping_country'],
            'shipping_country' => $billing['shipping_country'],
            'shipping_state' => $billing['shipping_state'],
            'billing_first_name' => $billing['billing_first_name']. " ". $billing['billing_last_name'],
            'billing_phone_no'  => $billing['billing_phone'],
            'billing_address1'  => $billing['billing_address1'],
            'billing_city'      => $billing['billing_city'],
            'billing_state'     => $billing['billing_state'],
            'billing_postcode'  => $billing['billing_postcode'],
            'sales_tax'  => $salesTax
        );

        $this->get('mail_helper')->sendSuccessPurchaseEmail($user, $dataArray);
        return;
    }

    private function sendEmailToAdmin( User $user, $decode, $result)
    {
        $current = date('d-m-Y');
        $items = isset($decode["items"]) ? $decode["items"] : "0";
        $itemsArray = array();
        foreach ($items as $detail) {
            $entity = $this->container->get('admin.helper.productitem')->find($detail['item_id']);
            $itemsArray[] = array(
                'pname' => $entity->getProduct()->getName(),
                'quantity' => $detail['quantity'],
                'price'     => $entity->getPrice() * $detail['quantity'],
                'sku'       => $entity->getProduct()->getControlNumber(),
                'size'      => $entity->getProductSize()->getTitle(),
                'color'     => $entity->getProductColor()->getTitle()
            );
        }
        $orderNummber = $result['order_number'];
        $orderAmount = $decode['order_amount'];
        $totalAmount = $decode['total_amount'];
        $discount   = $decode['discount_amount'];

        $creditCard = $result['result']->transaction->creditCard;
        $billing    = $decode['billing'];
        $order_id = $result['order_id'];
        $salesTax  =   (isset($decode['sales_tax']) ? $decode['sales_tax'] : 0);
        $d_discount  =   (isset($decode['discount']) ? $decode['discount'] : 0);

        $orderEntity = $this->container->get('cart.helper.order')->findOrderById( $order_id );

        $dataArray = array(
            'purchase_date' => $orderEntity->getUserOrderDate()->format('Y-m-d H:i:s'),
            'items'         => $itemsArray,
            'order_numnber' => $orderNummber,
            'card_type'     => $creditCard['cardType'],
            'last_four_number' => $creditCard['last4'],
            'contact_number'    => '262-391-3403',
            'email'         => $user->getEmail(),
            'frist_name'    => $user->getFullName(),
            'order_amount'  => $orderAmount,
            //'total_amount'  => $totalAmount,
            'total_amount'  => number_format((float)$totalAmount, 2, '.', ''),
            'discount'  => ($discount > 0 ? "-$".$discount : 0),
            'discountType' => (isset($decode['group_type']) && $decode['group_type'] == 2 ? "(".$d_discount."%)" : ""),
            'phone_number'  => $user->getPhoneNumber(),
            'expirate_date' => $creditCard['expirationMonth']. "/". $creditCard['expirationYear'],
            'cardholderName' => $creditCard['cardholderName'],
            'shipping_first_name' => $billing['shipping_first_name'],
            'shipping_last_name' => $billing['shipping_last_name'],
            'shipping_address1' => $billing['shipping_address1'],
            'shipping_address2' => $billing['shipping_address2'],
            'shipping_phone' => $billing['shipping_phone'],
            'shipping_city' => $billing['shipping_city'],
            'shipping_postcode' => $billing['shipping_postcode'],
            'shipping_country' => $billing['shipping_country'],
            'shipping_country' => $billing['shipping_country'],
            'shipping_state' => $billing['shipping_state'],
            'billing_first_name' => $billing['billing_first_name']. " ". $billing['billing_last_name'],
            'billing_phone_no'  => $billing['billing_phone'],
            'sales_tax'  => $salesTax
        );

        $this->get('mail_helper')->sendPurchaseEmailToAdmin($user, $dataArray);
        return;
    }
    #----------------------------------------------------Order Detail Services -------------------------#
    public function orderDetailAction( Request $request)
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $orders = $this->get('cart.helper.order')->findOrderListByUserID($user->getId());
            $a = 0;
            foreach ($orders as $order) {
                $order_items = $this->get('cart.helper.orderDetail')->findByOrderID($order['id']);
                $order['tracking_number']   = '';

                $shipping_information = ($order['shipment_json'] != null ? json_decode($order['shipment_json']) : "");
                if($order['shipment_json'] != null)
                {
                    $order['tracking_number'] = $shipping_information->TrackingNumber;
                }

                $order['shipping_amount'] = ($order['shipping_amount'] != null) ? $order['shipping_amount'] : 0;

                if(is_object($order['user_order_date']))
                    $order['order_user_date'] = $order['user_order_date']->format('Y-m-d H:i:s');
                else
                    $order['order_user_date'] = $order['order_date']->format('Y-m-d H:i:s');

                foreach($order_items as $index => $item){
                    $itemObject = $this->container->get('admin.helper.productitem')->find($item['item_id']);
                    $product_color = $itemObject->getProductColor();
                    $product_size = $itemObject->getProductSize();

                    $item['color'] = $product_color->getTitle();
                    $item['size'] = $product_size->getTitle();

                    $item['image'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . "/" .$itemObject->getWebPath();
                    $order_items[$index] = $item;
                }

                $order['shipment_json']     = '';
                $orders[$a] = $order;
                $orders[$a]['orderItem'] = $order_items;
                $a++;
            }
            $res = $this->get('webservice.helper')->response_array(true, null, true,$orders);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    #----------------------------------------------------Order Detail Services -------------------------#
    public function basicOrderInfoAction( Request $request)
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $orders = $this->get('cart.helper.order')->findBasicOrderListByUserID($user->getId());
            $res = $this->get('webservice.helper')->response_array(true, null, true,$orders);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    #---------------------------------------------- User address Services ---------------------------#

    public function saveUserBillingAddressAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $billing_id = $decoded['billing_id'];
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if($billing_id > 0){
                $billingObject = $this->container->get('cart.helper.userAddresses')->updateUserBillingAddress($decoded, $user);
                if($billingObject)
                {
                    $res = $this->get('webservice.helper')->response_array(true, 'Thanks for updating your info! Your address has been changed.', true, array(
                        "billing_address_id" => $billingObject->getId()
                    ));
                }else{
                    $res = $this->get('webservice.helper')->response_array(false, 'Some thing went wrong please try again later.');
                }
            }else{
                $billingObject = $this->container->get('cart.helper.userAddresses')->saveUserBillingAddress($decoded, $user);
                if($billingObject)
                {
                    $res = $this->get('webservice.helper')->response_array(true, 'Thanks for updating your info! Your address has been changed.', true, array(
                        "billing_address_id" => $billingObject['billing']->getId(),
                        'shipping_address_id' => (isset($billingObject['shipping']) ? $billingObject['shipping']->getId() : 0)
                    ));
                }else{
                    $res = $this->get('webservice.helper')->response_array(false, 'Some thing went wrong please try again later.');
                }
            }


        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function saveUserShippingAddressAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $shipping_id = $decoded['shipping_id'];
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if($shipping_id > 0){
                $shippingObject = $this->container->get('cart.helper.userAddresses')->updateUserShippingAddress($decoded, $user);
            }else{
                $shippingObject = $this->container->get('cart.helper.userAddresses')->saveUserShippingAddress($decoded, $user);
            }

            if($shippingObject)
            {
                $res = $this->get('webservice.helper')->response_array(true, 'Thanks for updating your info! Your address has been changed.', true, array(
                    "shipping_address_id" => $shippingObject->getId()
                ));
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Some thing went wrong please try again later.');
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function deleteUserShippingOrBillingAddressAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $shipping_id = (isset($decoded['shipping_id'])) ? $decoded['shipping_id'] : 0;
            $billing_id = (isset($decoded['billing_id'])) ? $decoded['billing_id'] : 0;
            if($shipping_id > 0 || $billing_id > 0){
                if($shipping_id > 0){
                    $addressRemove = $this->container->get('cart.helper.userAddresses')->deleteUserShippingAddress($shipping_id, $user);
                    $res = $this->get('webservice.helper')->response_array(true, 'user address successfully deleted');
                }else if($billing_id > 0){
                    $addressRemove = $this->container->get('cart.helper.userAddresses')->deleteUserBillingAddress($billing_id, $user);
                    $res = $this->get('webservice.helper')->response_array(true, 'user address successfully deleted');
                }
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Billing or shipping id must be greater then zero(0).');
            }

        }else{
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response( $res );

    }

    public function getAllUserSavedAddressesAction(){
        $stampsDotCom = new Stamps();

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $addresses = $this->container->get('cart.helper.userAddresses')->getAllUserSavedAddresses( $user );
            // $response = $stampsDotCom->getRates( $decoded );
            /*$addresses['shipping_methods'] = array();
            if($response['verified']){
                $addresses['shipping_methods'] = $response['shipping_method'];
            }*/
            $addresses['shipping_methods'] = array(
                array(
                "method"      => "4-Day Shipping",
                'detail'      => "Deliver on or Monday",
                'method_cost' => "Free",
                "method_id"   => '1',
                "days"        => '4'
                ),
                array(
                    "method"      => "2-Day Shipping",
                    'detail'      => "Deliver on or Fridat",
                    'method_cost' => "10.25",
                    "method_id"   => '2',
                    "days"        => '2',
                )
            );
            $res = $this->get('webservice.helper')->response_array(true, 'user addresses found', true, $addresses);
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function getShippingMethodsAction(){
        $addresses = array();
        $stampsDotCom = new Stamps();

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
            $addresses['shipping_methods'] = array();
            if(!empty($user_cart)){
                $shippmentType  = $this->get('webservice.helper')->getShippintType();
                if($shippmentType == 1){
                    $productItemWeoghtOz = $this->get('webservice.helper')->getProductItemWeight( $user_cart );
                    $response = $stampsDotCom->getRates( $decoded, $productItemWeoghtOz );
                }elseif ($shippmentType == 0){
                    $response = array(
                        'verified' => true,
                        'shipping_method' => array(
                            array(
                                'amount' => 0,
                                'deliverDays' => "4",
                                'shipDate'    => date('Y-m-d'),
                                'deliveryDate' => date('Y-m-d', strtotime("+4 days")),
                                'serviceType' => "",
                                'FromZIPCode' => "",
                                'ToZIPCode' => "",
                                'WeightOz' => 0,
                                'InsuredValue' => 0,
                                'RectangularShaped' => false
                            ),
                        )
                    );
                }

                $addresses['shipping_methods'] = array();
                if($response['verified']){
                    $addresses['shipping_methods'] = $response['shipping_method'];
                }

                $res = $this->get('webservice.helper')->response_array(true, 'shipping method found', true, $addresses);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'shopping cart is empty/no shipping method found', true, $addresses);
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }


    public function getAllUserSavedAddressesWithRatesAction(){
        $stampsDotCom = new Stamps();

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $addresses = $this->container->get('cart.helper.userAddresses')->getAllUserSavedAddresses( $user );
            $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);

            $addresses['shipping_methods'] = array();

            if(!empty($user_cart)){
                $productItemWeoghtOz = $this->get('webservice.helper')->getProductItemWeight( $user_cart );
                $response = $stampsDotCom->getRates( $decoded, $productItemWeoghtOz );
                if($response['verified']){
                    $addresses['shipping_methods'] = $response['shipping_method'];
                }
            }

            $res = $this->get('webservice.helper')->response_array(true, 'user addresses found', true, $addresses);
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function addressVerificationAction(){
        $stampsDotCom   = new Stamps();

        $decoded        = $this->get('webservice.helper')->processRequest($this->getRequest());
        $shippmentType  = $this->get('webservice.helper')->getShippintType();

        if($shippmentType == 1){
            $response = $stampsDotCom->addressVerification( $decoded );
        }elseif ($shippmentType == 0){
            $response = array(
                'verified'  => true,
                'data'      => array()
            );
        }

        if($response['verified'])
        {
            $res = $this->get('webservice.helper')->response_array(true, 'user addresses found', true, $response['data']);
        }else
        {
            $res = $this->get('webservice.helper')->response_array(false, $response['msg']);
        }

        return new Response( $res );
    }

    public function getShippingRatesAction(){
        $stampsDotCom = new Stamps();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $response = $stampsDotCom->getRates( $decoded );
    }

    public function nwsBraintreeSaveCreditCardAction( Request $request ){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $data = $this->get('cart.helper.payment')->registeredUserCreditCard( $user, $decoded);
            if($data['success'] == 0){
                $res = $this->get('webservice.helper')->response_array(true, 'User payment save successfully', true, $data);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, $data['message'], true, $data);
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function nwsBraintreeDeleteCreditCardAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $data = $this->get('cart.helper.payment')->deleteUserPaymentMethod( $user, $decoded );
            if($data['success'] == 0){
                $res = $this->get('webservice.helper')->response_array(true, 'Successfully remove user payment methods', true, $data);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, $data['message'], true, $data);
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );

    }

    public function nwsBraintreeGetCreditCardsAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $data = $this->get('cart.helper.payment')->getUserCreditCards( $user );
            if($data['success'] == 0){
                $res = $this->get('webservice.helper')->response_array(true, 'User credits cards found', true, $data);
            }else{
                $res = $this->get('webservice.helper')->response_array(true, "no card found", true, $data);
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function nwsBraintreeUpdateCreditCardAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $data = $this->get('cart.helper.payment')->updateUserPaymentMethod( $user, $decoded );
            if($data['success'] == 0){
                $res = $this->get('webservice.helper')->response_array(true, 'User credits cards found', true, $data);
            }else{
                $res = $this->get('webservice.helper')->response_array(false, "no card found", true, $data);
            }
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

    public function showEmailDetailAction()
    {
        $decode = $this->container->get('request')->attributes->get('_route_params');
        $order_number = $decode['order_number'];
        #get order data
        $orders = $this->container->get('cart.helper.order')->findByOrderNo( $order_number );
        $ordersDetail=$this->container->get('cart.helper.orderDetail')->findByOrderID( $orders["id"] );
        $itemsArray = array();
        foreach ($ordersDetail as $detail) {
            $entity = $this->container->get('admin.helper.productitem')->find($detail['item_id']);
            $itemsArray[] = array(
                'pname'      => $entity->getProduct()->getName(),
                'quantity'   => $detail['qty'],
                'item_price' => $entity->getPrice(),
                'price'      => number_format((float)($entity->getPrice() * $detail['qty']), 2, '.', ''),
                'sku'        => $entity->getProduct()->getControlNumber(),
                'size'       => $entity->getProductSize()->getTitle(),
                'color'      => $entity->getProductColor()->getTitle()
            );
        }
        $creditCard   = json_decode($orders['payment_json'])->transaction->_attributes->creditCard;
        $d_discount   = (isset($orders['discount']) ? $orders['discount'] : 0);

        $dataArray = array(
            'purchase_date' => ($orders['user_order_date'] != "") ? $orders['user_order_date']->format('F d, Y'): "",
            'items'         => $itemsArray,
            'order_numnber' => $orders['order_number'],
            'card_type'     => $creditCard->cardType,
            'last_four_number' => $creditCard->last4,
            'contact_number'   => '262-391-3403',
            'email'         => $orders['email'],
            'frist_name'    => $orders['firstName'] . " " . $orders['lastName'],
            'order_amount'  => number_format((float)$orders['order_amount'], 2, '.', ''),
            'total_amount'  => number_format((float)$orders['total_amount'], 2, '.', ''),
            'discount'      => ($orders['discount_amount'] > 0 ? "-$".$orders['discount_amount'] : 0),
            'discountType' => (isset($orders['group_type']) && $orders['group_type'] == 2 ? "(".$d_discount."%)" : ""),
            'expirate_date' => $creditCard->expirationMonth. "/". $creditCard->expirationYear,
            'shipping_first_name' => $orders['shipping_first_name'],
            'shipping_last_name' => $orders['shipping_last_name'],
            'shipping_address1' => $orders['shipping_address1'],
            'shipping_address2' => $orders['shipping_address2'],
            'shipping_phone' => $orders['shipping_phone'],
            'shipping_city' => $orders['shipping_city'],
            'shipping_postcode' => $orders['shipping_postcode'],
            'shipping_country' => $orders['shipping_country'],
            'shipping_state' => $orders['shipping_state'],
            'billing_first_name' => $orders['billing_first_name']. " ". $orders['billing_last_name'],
            'billing_phone_no'  => $orders['billing_phone'],
            'billing_address1'  => $orders['billing_address1'],
            'billing_city'      => $orders['billing_city'],
            'billing_state'     => $orders['billing_state'],
            'billing_postcode'  => $orders['billing_postcode'],
            'sales_tax'         => number_format((float)$orders['sales_tax'], 2, '.', ''),
        );
        
        // echo "<pre>";
        // print_r($dataArray);
    
        // die();
        return $this->render('LoveThatFitWebServiceBundle:Order:user_purchase.html.twig',
            array('dataArray'   => $dataArray)
        );
    }

    // update Single Item to Cart Version 3.0
    public function updateItemToCartNewAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $old_item_id = $decoded["old_item_id"];
            $item_id = $decoded["item_id"];
            $qty = $decoded["quantity"];
            $requested_screen = $decoded["display_screen"];

            //find old item id and delete that item from cart
            $find_old_item_against_user = $this->container->get('cart.helper.cart')->findCartByUserId($user, $old_item_id);
            if(!empty($find_old_item_against_user)) {
                $this->container->get('cart.helper.cart')->removeCartByItem($user, $old_item_id);
            }

            /* IOSV3-252 - From the Product Detail page, if product item already exist then quantity not change  */
            if($requested_screen == "detail_page"){
                $product_item = $this->container->get('admin.helper.productitem')->find($item_id);
                $find_item_against_user = $this->container->get('cart.helper.cart')->findCartByUserId($user, $product_item);
                if(!empty($find_item_against_user["qty"])){
                    $qty = $find_item_against_user["qty"];
                }
            }

            /*Remove Item from wishlist */
            $this->container->get('cart.helper.wishlist')->removeWishlistByItem($user, $item_id);
            $response = $this->container->get('cart.helper.cart')->fillCartforService($item_id, $user, $qty);
            if ($response != null) {
                $resp = 'Item has been added to Cart Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "some thing went wrong");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }

}