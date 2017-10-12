<?php

namespace LoveThatFit\CartBundle\Entity;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints\DateTime;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;
use Braintree_Customer;
use Braintree_PaymentMethod;
use Braintree_PaymentMethodNonce;

class PaymentHelper
{

    private $container;
    private $env;

    public function __construct(Container $container)
    {
        $this->container    = $container;
        $yaml               = new Parser();
        $env                = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['enviorment'];
        if($env == 'dev')
            $this->env = "love_that_fit_cart";
        else
            $this->env  = "braintree_live";
    }

    #------------------------------------ Get Braintree Client Token -----------------#
    public function getClientToken()
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);
        $clientToken = Braintree_ClientToken::generate();
        return $clientToken;
    }

    public function getClientTokenById( $user )
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

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
                'marchant_id'             => $parse[$this->env]["merchant_id"]
            );
        }catch (\Braintree_Exception $exception) {
            $clientToken = Braintree_ClientToken::generate();
            return array(
                'success'       => -1,
                'client_token'  => $clientToken,
                'customer_id'   => 0,
                'marchant_id'   => $parse[$this->env]["merchant_id"]
            );
        }
    }
#------------------------------------- End of Braintree Client Token ------------#
#------------------------------------ Braintree Transaction -----------------#
    public function braintreeTransaction($user, $decoded, $session)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);
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
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

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
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);
        $sales_tax  =   (isset($decoded['sales_tax']) ? $decoded['sales_tax'] : 0);

        $billing = $decoded["billing"];
        $saleObject = array(
            "amount" => round($decoded['total_amount'],2),
            "taxAmount" => $sales_tax,
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
                'submitForSettlement' => true,
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
            $datetimeObj = new \DateTime('now');
            $fnfGroup = null;
            $payment_json = json_encode($result);
            $shipping_amount    = $decoded['rates']['shipping_rate_amount'];
            $order_amount       = $decoded['order_amount'];
            $discount_amount    = $decoded['discount_amount'];
            $total_amount       = $decoded['total_amount'];
            $order_date         = (isset($decoded['order_date']) ? $decoded['order_date'] : $datetimeObj->format('Y-m-d H:i:s'));

            $decoded['rates']['amount'] = $decoded['rates']['shipping_rate_amount']; //assign your rate variable

            $rates              = (isset($decoded['rates']) ? $decoded['rates'] : "");
            $sales_tax          = (isset($decoded['sales_tax']) ? $decoded['sales_tax'] : 0);

            $transaction_id = $result->transaction->id;
            $transaction_status = $result->transaction->status;
            $payment_method = $result->transaction->paymentInstrumentType;

            if(array_key_exists('groupId', $decoded))
            {
                $fnfGroup = $this->container->get('fnfgroup.helper.fnfgroup')->findById( $decoded['groupId'] );
            }

            $entity = $this->container->get('cart.helper.order')->saveBillingShipping($decoded, $user, $shipping_amount, $fnfGroup);
            $order_id = $entity->getId();

            if(!isset($decoded['billing_id']) && !isset($decoded['shipping_id']))
                $this->container->get('cart.helper.userAddresses')->saveAddress($decoded, $user, 1, 1);

            $order_number = $order_id . rand(100, 100000);
            $user_cart = $this->container->get('cart.helper.cart')->getFormattedCart($user);
            $response = $this->container->get('cart.helper.orderDetail')->saveOrderDetail($user_cart, $entity);
            $save_transaction = $this->container->get('cart.helper.order')->updateUserTransaction($order_id, $transaction_id, $transaction_status, $payment_method, $payment_json, $order_number, $order_date, json_encode($rates), $sales_tax);

            try {
                //create podio orders entity
                $this->createPodioOrder($entity->getId(),$order_number);
            } catch(\Exception $e) {
                // log $e->getMessage()
            }

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

    private function createPodioOrder($order_id,$order_number){
        ## add user podio log data
        if ($order_id) {
            $order_entity = $this->container->get('cart.helper.order')->find($order_id);
            $save_order_podio = $this->container->get('order.helper.podio')->savePodioOrders($order_entity,$order_number);
        }
    }

    public function webServiceBrainTreeTransaction( $user, $decoded)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

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

    public function getTransactionStatus( $transactionId = null, $transactionStatus)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

        try{
            $transaction = Braintree_Transaction::find($transactionId);
            $status = $transaction->status;

            return array(
                'status' => 200,
                'transaction_status' => $status
            );

        }catch (Braintree_Exception_NotFound $exception){
            return array(
                'status' => 300,
                'transaction_status' => $transactionStatus
            );
        }
    }

    public function registeredUserCreditCard(User $user, $decoded)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

        try{
            $customer = Braintree_Customer::find($user->getId());
            $result = Braintree_PaymentMethod::create([
                'customerId' => $customer->id,
                'paymentMethodNonce' => $decoded['payment_method_nonce']
            ]);

            if($result->success){
                $data = array(
                    'success' => 0,
                    'token' => $result->paymentMethod->token,
                    'customerId' => $customer->id,
                    'cardType' => $result->paymentMethod->cardType,
                    'default' => $result->paymentMethod->default,
                    'result' => $result
                );
            }else{
                $data = array(
                    'success' => -1,
                    'token' => "",
                    'customerId' => 0,
                    'cardType' => "",
                    'default' => "",
                    'message' => $result->message,
                    'result' => $result
                );
            }
        }catch (\Braintree_Exception $exception) {
            $result = Braintree_Customer::create([
                'id'            => $user->getId(),
                'firstName'     => $user->getFirstName(),
                'lastName'      => $user->getLastName(),
                'phone'         => $user->getPhoneNumber(),
                'email'         => $user->getEmail()
            ]);

            if($result->success){
                $paymentMethodCreate = Braintree_PaymentMethod::create([
                    'customerId' => $result->customer->id,
                    'paymentMethodNonce' => $decoded['payment_method_nonce']
                ]);

                if($paymentMethodCreate->success){
                    $data = array(
                        'success' => 0,
                        'token' => $paymentMethodCreate->paymentMethod->token,
                        'customerId' => $result->customer->id,
                        'cardType' => $paymentMethodCreate->paymentMethod->cardType,
                        'default' => $paymentMethodCreate->paymentMethod->default,
                        'result' => $paymentMethodCreate
                    );
                }else{
                    $data = array(
                        'success' => -1,
                        'token' => "",
                        'customerId' => 0,
                        'cardType' => "",
                        'default' => "",
                        'message' => $paymentMethodCreate->message,
                        'result' => $paymentMethodCreate
                    );
                }
            }else{
                $data = array(
                    'success' => -1,
                    'token' => "",
                    'customerId' => 0,
                    'cardType' => "",
                    'default' => "",
                    'message' => $result->message,
                    'result' => $result
                );
            }

        }

        return $data;
    }

    public function getUserCreditCards(User $user)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

        try {
            $customer = Braintree_Customer::find($user->getId());
            $creditCrads = $customer->creditCards;
            $customerCards = array(
                'success' => 0
            );

            foreach ( $creditCrads as $card){
                try{
                    $result = Braintree_PaymentMethodNonce::create($card->token);
                    $payment_method_nonce =  $result->paymentMethodNonce->nonce;
                }catch (\Braintree_Exception_NotFound $exception){
                    $payment_method_nonce = "";
                }


                $customerCards['cards'][] = array(
                    'maskedNumber' => $card->maskedNumber,
                    'token'     => $card->token,
                    'default' => $card->default,
                    'cardType' => $card->cardType,
                    'expirationMonth' => $card->expirationMonth,
                    'expirationYear' => $card->expirationYear,
                    'cardholderName' => $card->cardholderName,
                    'uniqueNumberIdentifier' => $card->uniqueNumberIdentifier,
                    'imageUrl'      => $card->imageUrl,
                    'bin'           => $card->bin,
                    'last4'         => $card->last4,
                    'payment_method_nonce' => $payment_method_nonce
                );
            }

        }catch (\Braintree_Exception $exception) {
            $customerCards = array(
                'success' => -1,
                'cards'     => array()
            );
        }

        return $customerCards;
    }

    public function deleteUserPaymentMethod( User $user, $decode )
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

        try{
            $paymentMethod = Braintree_PaymentMethod::find($decode['braintreeToken']);
            $result = Braintree_PaymentMethod::delete($decode['braintreeToken']);
            if($result->success){
                $data = array(
                    'success' => 0,
                    'message' => "Payment remove successfully"
                );
            }else{
                $data = array(
                    'success' => -1,
                    'message' => "Unable to remove payment method try again later"
                );
            }
        }catch (\Braintree_Exception_NotFound $exception)
        {
            $data = array(
                'success' => -1,
                'message' => "No payment method fround"
            );
        }

        return $data;
    }

    public function updateUserPaymentMethod(User $user, $decoded)
    {
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        Braintree_Configuration::environment($parse[$this->env]["environment"]);
        Braintree_Configuration::merchantId($parse[$this->env]["merchant_id"]);
        Braintree_Configuration::publicKey($parse[$this->env]["public_key"]);
        Braintree_Configuration::privateKey($parse[$this->env]["private_key"]);

        try{
            $paymentMethod = Braintree_PaymentMethod::find($decoded['token']);
            $result = Braintree_PaymentMethod::update($decoded['token'],array(
                'paymentMethodNonce' => $decoded['payment_method_nonce']
            ));
            if($result->success){
                $data = array(
                    'success' => 0,
                    'message' => "Payment method update successfully"
                );
            }else{
                $data = array(
                    'success' => -1,
                    'message' => "Unable to update payment method try again later"
                );
            }

        }catch (\Braintree_Exception_NotFound $exception)
        {
            $data = array(
                'success' => -1,
                'message' => "No payment method fround"
            );
        }

        return $data;
    }

#------------------------------------- End of Braintree Transaction ------------#
}