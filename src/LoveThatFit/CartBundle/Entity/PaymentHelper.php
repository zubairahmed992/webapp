<?php

namespace LoveThatFit\CartBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;
use Braintree_Customer;
use Braintree_PaymentMethod;

class PaymentHelper
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    #------------------------------------ Get Braintree Client Token -----------------#
    public function getClientToken()
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["braintree_live"]["environment"]);
        Braintree_Configuration::merchantId($parse["braintree_live"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["braintree_live"]["public_key"]);
        Braintree_Configuration::privateKey($parse["braintree_live"]["private_key"]);
        $clientToken = Braintree_ClientToken::generate();
        return $clientToken;
    }

    public function getClientTokenById( $user )
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
        Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
        Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);

        try {
            $customer = Braintree_Customer::find($user->getId());
            $client_creditcard_token = $customer->creditCards[0]->token;

            $clientToken = Braintree_ClientToken::generate([
                "customerId" => $customer->id
            ]);

            return array(
                'success'       => 0,
                'client_token'  => $clientToken,
                'customer_id'   => $customer->id,
                'client_creditcard_token' => $client_creditcard_token,
                'marchant_id'             => $parse["love_that_fit_cart"]["merchant_id"]
            );
        }catch (\Braintree_Exception $exception) {
            $clientToken = Braintree_ClientToken::generate();
            return array(
                'success'       => -1,
                'client_token'  => $clientToken,
                'customer_id'   => 0,
                'marchant_id'   => $parse["love_that_fit_cart"]["merchant_id"]
            );
        }
    }
#------------------------------------- End of Braintree Client Token ------------#
#------------------------------------ Braintree Transaction -----------------#
    public function braintreeTransaction($user, $decoded, $session)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["braintree_live"]["environment"]);
        Braintree_Configuration::merchantId($parse["braintree_live"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["braintree_live"]["public_key"]);
        Braintree_Configuration::privateKey($parse["braintree_live"]["private_key"]);
        $billing_shipping_info = $session->get('billing_shipping_info');
        $result = Braintree_Transaction::sale(array(
            "amount" => $decoded['order_amount'] + $decoded['shipping_amount'],
            "paymentMethodNonce" => $decoded['payment_method_nonce'],
        ));

        $payment_json = json_encode($result);
        $shipping_amount = $decoded['shipping_amount'];


        /****************** Success and Fail Conditions *********************/
        if ($result->success) {
            $transaction_id = $result->transaction->id;
            $transaction_status = $result->transaction->status;
            $payment_method = $result->transaction->paymentInstrumentType;
            $bill_info = $session->get('bill_info');
            $ship_info = $session->get('ship_info');
            $order_amount = $session->get('order_amount');
            $billing_address = $session->get('billing_address');
            $shipping_address = $session->get('shipping_address');
            $select_address = $session->get('default');
            if ($select_address == 'yes') {
                //user came from show address screen and previously ordered things so saving just order information and using the previous added billing and shipping address
                $entity = $this->container->get('cart.helper.order')->saveBillingShippingDefaultAddress($billing_address, $shipping_address, $user, $order_amount, $shipping_amount);
                $order_id = $entity->getId();
            } else {
                //User came here first time so we need to store address here
                $entity = $this->container->get('cart.helper.order')->saveBillingShipping($billing_shipping_info, $user, $shipping_amount);
                $order_id = $entity->getId();
                $this->container->get('cart.helper.userAddresses')->saveAddress($billing_shipping_info, $user, $bill_info, $ship_info);
            }
            $order_number = $order_id . rand(100, 100000);
            $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
            $response = $this->container->get('cart.helper.orderDetail')->saveOrderDetail($user_cart, $entity);
            $save_transaction = $this->container->get('cart.helper.order')->updateUserTransaction($order_id, $transaction_id, $transaction_status, $payment_method, $payment_json, $order_number);
            $this->container->get('mail_helper')->sendOrderConfirmationEmail($user, $entity, $user->getCart());
            $data = array();
            $data["order_number"] = $order_number;
            $data["transaction_status"] = $transaction_status;
            $data["response_code"] = $result->transaction->processorResponseCode;
            $session->set('billing_shipping_info', '');
            $session->set('order_amount', '');
            $session->set('bill_info', '');
            $session->set('ship_info', '');
            $session->set('billing_address', '');
            $session->set('shipping_address', '');
            $session->set('default', '');
            $remove_user_cart = $this->container->get('cart.helper.cart')->removeUserCart($user);
            return $data;
        } else {
            $transaction_status = $result->transaction->status;
            $data = array();
            $data["order_number"] = '';
            $data["transaction_status"] = $transaction_status;
            $data["response_code"] = $result->transaction->processorResponseText;
            $session->set('billing_shipping_info', '');
            $session->set('order_amount', '');
            $session->set('bill_info', '');
            $session->set('ship_info', '');
            $session->set('billing_address', '');
            $session->set('shipping_address', '');
            $session->set('default', '');
            return $data;
        }
        /****************** End of Success and Fail Conditions **************/

    }

    public function webServiceTransaction($user, $decoded)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["braintree_live"]["environment"]);
        Braintree_Configuration::merchantId($parse["braintree_live"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["braintree_live"]["public_key"]);
        Braintree_Configuration::privateKey($parse["braintree_live"]["private_key"]);

        try {
            $result = Braintree_Transaction::sale(array(
                "amount" => $decoded['order_amount'],
                "paymentMethodNonce" => $decoded['payment_method_nonce'],
                'options' => [
                    'submitForSettlement' => true
                ]
            ));

            $payment_json = json_encode($result);
            /*$shipping_amount    = $decoded['shipping_amount'];
            $order_amount       = $decoded['order_amount'];*/

            return $payment_json;
        } catch (\Braintree_Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function webServiceBrainTreeProcessUserTransaction($user, $decoded){
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["love_that_fit_cart"]["environment"]);
        Braintree_Configuration::merchantId($parse["love_that_fit_cart"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["love_that_fit_cart"]["public_key"]);
        Braintree_Configuration::privateKey($parse["love_that_fit_cart"]["private_key"]);

        $billing = $decoded["billing"];
        $saleObject = array(
            "amount" => $decoded['total_amount'],
            "paymentMethodNonce" => $decoded['payment_method_nonce'],
            'billing' => [
                'firstName' => $billing['billing_first_name'],
                'lastName' => $billing['billing_last_name'],
                'streetAddress' => $billing['billing_address1'],
                'postalCode' => $billing['billing_postcode']
            ],
            'shipping' => [
                'firstName' => $billing['shipping_first_name'],
                'lastName' => $billing['shipping_last_name'],
                'streetAddress' => $billing['shipping_address1'],
                'postalCode' => $billing['shipping_postcode']
            ],'options' => [
                'submitForSettlement' => true,
            ]
        );

        try {
            $customer = Braintree_Customer::find($user->getId());
            $saleObject['customerId'] = $customer->id;
            $result = Braintree_Transaction::sale(
                $saleObject
            );

            return $this->createReturnReponse( $result, $decoded, $user);
        } catch (\Braintree_Exception $exception) {

            $saleObject['customer'] = array(
                'id'            => $user->getId(),
                'firstName'     => $user->getFirstName(),
                'lastName'      => $user->getLastName(),
                'phone'         => $user->getPhoneNumber(),
                'email'         => $user->getEmail()
            );

            $saleObject['options'] = array(
                'storeInVault' => true
            );
            $result = Braintree_Transaction::sale(
                $saleObject
            );

            return $this->createReturnReponse( $result, $decoded, $user);
        }
    }

    private function createReturnReponse( $result, $decoded, $user )
    {
        if( $result->success )
        {
            $fnfGroup = null;
            $payment_json = json_encode($result);
            $shipping_amount    = $decoded['shipping_amount'];
            $order_amount       = $decoded['order_amount'];
            $discount_amount    = $decoded['discount_amount'];
            $total_amount       = $decoded['total_amount'];

            $transaction_id = $result->transaction->id;
            $transaction_status = $result->transaction->status;
            $payment_method = $result->transaction->paymentInstrumentType;

            if(array_key_exists('groupId', $decoded))
            {
                $fnfGroup = $this->container->get('fnfgroup.helper.fnfgroup')->findById( $decoded['groupId'] );
            }

            $entity = $this->container->get('cart.helper.order')->saveBillingShipping($decoded, $user, $shipping_amount, $fnfGroup);
            $order_id = $entity->getId();
            $this->container->get('cart.helper.userAddresses')->saveAddress($decoded, $user, 1, 1);

            $order_number = $order_id . rand(100, 100000);
            $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
            $response = $this->container->get('cart.helper.orderDetail')->saveOrderDetail($user_cart, $entity);
            $save_transaction = $this->container->get('cart.helper.order')->updateUserTransaction($order_id, $transaction_id, $transaction_status, $payment_method, $payment_json, $order_number);

            $data = array(
                'success' => 0
            );

            $data['order_id']      = $order_id;
            $data["order_number"] = $order_number;
            $data["transaction_status"] = $transaction_status;
            $data["response_code"] = $result->transaction->processorResponseCode;
            $data["customerDetails"] = $result->transaction->customer;
            $data["billingDetails"] = $result->transaction->billing;
            $data["shippingDetails"] = $result->transaction->shipping;
            $data["result"] = $result;

            $remove_user_cart = $this->container->get('cart.helper.cart')->removeUserCart($user);
            return $data;
        }
        else{
            $transaction_status = $result->transaction;
            $data = array(
                'success' => -1,
            );
            $data["order_number"] = '';
            $data["transaction_status"] = $transaction_status;
            $data["response_code"] = $result->message;
            return $data;
        }
    }

    public function webServiceBrainTreeTransaction( $user, $decoded)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse["braintree_live"]["environment"]);
        Braintree_Configuration::merchantId($parse["braintree_live"]["merchant_id"]);
        Braintree_Configuration::publicKey($parse["braintree_live"]["public_key"]);
        Braintree_Configuration::privateKey($parse["braintree_live"]["private_key"]);

        try {
            $result = Braintree_Transaction::sale(array(
                "amount" => $decoded['order_amount'],
                "paymentMethodNonce" => $decoded['payment_method_nonce'],
                'options' => [
                    'submitForSettlement' => true
                ]
            ));

            if( $result->success )
            {
                $payment_json = json_encode($result);
                $shipping_amount    = $decoded['shipping_amount'];
                $order_amount       = $decoded['order_amount'];

                $transaction_id = $result->transaction->id;
                $transaction_status = $result->transaction->status;
                $payment_method = $result->transaction->paymentInstrumentType;


                $entity = $this->container->get('cart.helper.order')->saveBillingShipping($decoded, $user, $shipping_amount);
                $order_id = $entity->getId();
                $this->container->get('cart.helper.userAddresses')->saveAddress($decoded, $user, 1, 1);

                $order_number = $order_id . rand(100, 100000);
                $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
                $response = $this->container->get('cart.helper.orderDetail')->saveOrderDetail($user_cart, $entity);
                $save_transaction = $this->container->get('cart.helper.order')->updateUserTransaction($order_id, $transaction_id, $transaction_status, $payment_method, $payment_json, $order_number);

                $data = array(
                    'success' => 0
                );
                $data["order_number"] = $order_number;
                $data["transaction_status"] = $transaction_status;
                $data["response_code"] = $result->transaction->processorResponseCode;

                $remove_user_cart = $this->container->get('cart.helper.cart')->removeUserCart($user);
                return $data;
            }
            else{
                $transaction_status = $result->transaction;
                $data = array(
                    'success' => -1,
                );
                $data["order_number"] = '';
                $data["transaction_status"] = $transaction_status;
                $data["response_code"] = $result->message;
                return $data;
            }
        } catch (\Braintree_Exception $exception) {

            return array(
                'success' => -1,
                'response_code' => $exception->getMessage()
            );
        }
    }
#------------------------------------- End of Braintree Transaction ------------#
}