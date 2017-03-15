<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
            $qty = $decoded["quantity"];
            $this->container->get('cart.helper.cart')->fillCart($item_id, $user, $qty);
            $resp = 'Item has been added to Cart Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
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
                $this->container->get('cart.helper.cart')->removeUserCart($user);
                foreach ($items as $detail) {
                    $this->container->get('cart.helper.cart')->fillCart($detail["item_id"], $user, $detail["quantity"]);
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

    // Remove User Cart
    public function removeUserCartAction()
    {
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
    public function removeUserItemAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $product_item = $decoded["item_id"];
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $this->container->get('cart.helper.cart')->removeCartByItem($user, $product_item);
            $resp = 'Cart Item has been removed';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
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

            /*Remove Item from wishlist */
            $this->container->get('cart.helper.wishlist')->removeWishlistByItem($user, $item_id);

            $this->container->get('cart.helper.cart')->fillCartforService($item_id, $user, $qty);
            $resp = 'Item has been added to Cart Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
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
        }else{
            return false;
        }
    }

    public function brainTreeProcessTransactionAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $discount_amount    = $decoded['discount_amount'];

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
            if(is_object($fnfUser))
            {
                $fnfGroupId = $fnfUser->getGroups()[0]->getId();
                $decoded['groupId'] = $fnfGroupId;
            }

            $result = $this->get('cart.helper.payment')->webServiceBrainTreeProcessUserTransaction($user, $decoded);
            if ($result['success'] == 0) {
                if($discount_amount > 0){
                    $fnfUser            = $this->get('fnfuser.helper.fnfuser')->getFNFUserById($user);
                    $fnfUserAfterUpdate = $this->get('fnfuser.helper.fnfuser')->setIsAvailable($fnfUser);
                }

                $this->sendEmailToUser( $user, $decoded, $result);
                $this->sendEmailToAdmin( $user, $decoded, $result);

                $res = $this->get('webservice.helper')->response_array(true, 'successfully complete transaction', true, $result);
            } else if ($result['success'] < 0) {
                $res = $this->get('webservice.helper')->response_array(false, 'some thing went wrong', true, $result);
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
            $itemsArray[] = array(
                'pname' => $entity->getProduct()->getName(),
                'quantity' => $detail['quantity'],
                'item_price'     => $entity->getPrice(),
                'price'     => $entity->getPrice() * $detail['quantity'],
                'sku'       => $entity->getProduct()->getControlNumber(),
                'size'      => $entity->getProductSize()->getTitle(),
                'color'     => $entity->getProductColor()->getTitle()
            );
        }
        $orderNummber = $result['order_number'];
        $orderAmount = $decode['total_amount'];
        $creditCard = $result['result']->transaction->creditCard;
        $billing    = $decode['billing'];
        $order_id = $result['order_id'];

        $orderEntity = $this->container->get('cart.helper.order')->findOrderById( $order_id );

        $dataArray = array(
            'purchase_date' => $orderEntity->getOrderDate()->format('Y-m-d H:i:s'),
            'items'         => $itemsArray,
            'order_numnber' => $orderNummber,
            'card_type'     => $creditCard['cardType'],
            'last_four_number' => $creditCard['last4'],
            'contact_number'    => '262-391-3403',
            'email'         => $user->getEmail(),
            'frist_name'    => $user->getFullName(),
            'order_amount'  => $orderAmount,
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
            'billing_address1'  => $billing['billing_address1']
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
        $orderAmount = $decode['total_amount'];
        $creditCard = $result['result']->transaction->creditCard;
        $billing    = $decode['billing'];
        $order_id = $result['order_id'];

        $orderEntity = $this->container->get('cart.helper.order')->findOrderById( $order_id );

        $dataArray = array(
            'purchase_date' => $orderEntity->getOrderDate()->format('Y-m-d H:i:s'),
            'items'         => $itemsArray,
            'order_numnber' => $orderNummber,
            'card_type'     => $creditCard['cardType'],
            'last_four_number' => $creditCard['last4'],
            'contact_number'    => '262-391-3403',
            'email'         => $user->getEmail(),
            'frist_name'    => $user->getFullName(),
            'order_amount'  => $orderAmount,
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
            'billing_phone_no'  => $billing['billing_phone']
        );

        $this->get('mail_helper')->sendPurchaseEmailToAdmin($user, $dataArray);
        return;
    }
    #----------------------------------------------------Order Detail Services -------------------------#
    public function orderDetailAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $orders = $this->get('cart.helper.order')->findOrderListByUserID($user->getId());
            $a = 0;
            foreach ($orders as $order) {
                $orders[$a]['orderItem'] = $this->get('cart.helper.orderDetail')->findByOrderID($order['id']);
                $a++;
            }

            $res = $this->get('webservice.helper')->response_array(true, $orders);
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
            }else{
                $billingObject = $this->container->get('cart.helper.userAddresses')->saveUserBillingAddress($decoded, $user);
            }

            if($billingObject)
            {
                $res = $this->get('webservice.helper')->response_array(true, 'user address successfully saved', true, array(
                    "billing_address_id" => $billingObject->getId()
                ));
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'Some thing went wrong please try again later.');
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
                $res = $this->get('webservice.helper')->response_array(true, 'user address successfully saved', true, array(
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

    public function getAllUserSavedAddressesAction(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $addresses = $this->container->get('cart.helper.userAddresses')->getAllUserSavedAddresses( $user );
            $addresses['shipping_methods'][] = array(
                "method" => "4-Day Shipping",
                'detail' => "Deliver on or Monday",
                'method_cost' => "Free",
                "method_id" => '1'
            );
            $res = $this->get('webservice.helper')->response_array(true, 'user addresses found', true, $addresses);
        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }

}