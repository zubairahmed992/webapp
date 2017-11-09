<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 4/4/2017
 * Time: 3:55 PM
 */

namespace LoveThatFit\CartBundle\Utils;

use LoveThatFit\CartBundle\Entity\UserAddresses;
use Symfony\Component\Yaml\Parser;


class Stamps
{
    private $integrarionId;
    private $userName;
    private $password;
    private $fromZipCode;
    private $serviceType;
    private $weightOz;
    private $deliverDays;
    private $packageType;

    private $wsdl = "https://swsim.testing.stamps.com/swsim/swsimv57.asmx?wsdl";
    private $proWsdl = "https://swsim.stamps.com/swsim/swsimv58.asmx?wsdl";

    private $soapClient;

    public function __construct()
    {
        $envKey = 'stamps_com_dev';

        $yaml   = new Parser();
        $env    = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['enviorment'];
        $parse  = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        if($env == 'dev') {
            $envKey = 'stamps_com_dev';
            $this->soapClient = new \SoapClient($this->wsdl, array('trace' => 1));
        }
        else{
            $envKey = 'stamps_com_pro';
            $this->soapClient = new \SoapClient($this->proWsdl, array('trace' => 1));
        }

        $this->integrarionId    = $parse[$envKey]["integrarionId"];
        $this->userName         = $parse[$envKey]["userName"];
        $this->password         = $parse[$envKey]["password"];
        $this->fromZipCode      = $parse[$envKey]["fromzipcode"];
        $this->serviceType      = $parse[$envKey]["serviceType"];
        $this->weightOz         = $parse[$envKey]["weightOz"];
        $this->deliverDays      = $parse[$envKey]["deliverydays"];
        $this->packageType      = $parse[$envKey]["packagetype"];
    }

    private function authenticateUser()
    {
        $authData = array(
            "Credentials"       => array(
                "IntegrationID"     => $this->integrarionId,
                "Username"          => $this->userName,
                "Password"          => $this->password
            ));

        $auth = $this->soapClient->AuthenticateUser($authData);
        return $auth->Authenticator;
    }

    public function addressVerification( $postData = array())
    {
        $address = $postData['address'];
        $fieldsVerified = $this->verifyFields( $address, 'addressVerfication');
        if($fieldsVerified['verified']){
            $authenticator = $this->authenticateUser();
            $callData = array(
                "Credentials"       => array(
                    "IntegrationID"     => $this->integrarionId,
                    "Username"          => $this->userName,
                    "Password"          => $this->password
                ),
                'Address' => array(
                    'FullName'  => (isset($address['fullname'])) ? $address['fullname'] : '',
                    'FirstName' => $address['firstname'],
                    'LastName'  => $address['lastname'],
                    'Address1'  => $address['address1'],
                    'Address2'  => (isset($address['address2'])) ? $address['address2'] : "",
                    'City'      => $address['city'],
                    'State'     => $address['state'],
                    'ZIPCode'   => $address['zipcode']
                ));
            try{
                $addressStatus = $this->soapClient->CleanseAddress( $callData );
                if($addressStatus->AddressMatch){
                    return array(
                        'verified' => true,
                        'data' => $addressStatus->Address
                    );
                }else{
                    return array(
                        'verified'  => false,
                        'msg'       => 'address not found'
                    );
                }
            }catch (\SoapFault $exception)
            {
                return array(
                    'verified'  => false,
                    'msg'       => $exception->getMessage()
                );
            }

        }else{
            return $fieldsVerified;
        }
    }

    public function createPostages( UserAddresses $billingAddress, UserAddresses $shippingAddress, $rate_json)
    {
        $returnResponse = array();
        $authenticator = $this->authenticateUser();

        $shipping_data = json_decode($shippingAddress->getAddressData());

        $callData = array(
            "Credentials"       => array(
                "IntegrationID"     => $this->integrarionId,
                "Username"          => $this->userName,
                "Password"          => $this->password
            ),
            'IntegratorTxID' => md5(uniqid($this->integrarionId.$shippingAddress->getId().rand(), true)),
            'Rate' => array(
                'FromZIPCode'   => $rate_json->FromZIPCode,
                'ToZIPCode'     => $rate_json->ToZIPCode,
                'Amount'   => $rate_json->amount,
                'ServiceType'   => $rate_json->serviceType,
                'DeliverDays'      => $rate_json->DeliverDays,
                'WeightOz'   => $rate_json->WeightOz,
                'WeightLb'      => 0,
                'PackageType' => 'Package',
                'ShipDate' => date('Y-m-d'),//'ShipDate' => $rate_json->shipDate,
                'DeliveryDate' => $rate_json->deliveryDate,
                'RectangularShaped' => $rate_json->RectangularShaped
            ),
            'From' => array(
                'FullName' => 'SelfieStyler, Inc',
                'Address1' => '250 E Wisconsin AVE Suite 1800',
                'Address2' => '',
                'City' => 'Milwaukee',
                'State' => 'WI',
                'ZIPCode' => '53202',
            ),'To' => array(
                'FullName' => '',// $shippingAddress->getFirstName()." ". $shippingAddress->getLastName(),
                'NamePrefix' =>'',
                'FirstName' => (isset($shipping_data->FirstName) ? $shipping_data->FirstName : ""),
                'MiddleName' => '',
                'LastName' => (isset($shipping_data->LastName) ? $shipping_data->LastName :""),
                'NameSuffix' => '',
                'Title' => '',
                'Department' => '',
                'Company' => '',
                'Address1' => (isset($shipping_data->Address1) ? $shipping_data->Address1 : ""),
                'Address2' => (isset($shipping_data->Address2) ? $shipping_data->Address2: ""),
                'Address3' => '',
                'City' => (isset($shipping_data->City) ? $shipping_data->City : ""),
                'State' => (isset($shipping_data->State) ? $shipping_data->State : ""),
                'ZIPCode' => (isset($shipping_data->ZIPCode) ? $shipping_data->ZIPCode : ""),
                'ZIPCodeAddOn' => (isset($shipping_data->ZIPCodeAddOn) ? $shipping_data->ZIPCodeAddOn : ""),
                'DPB' => (isset($shipping_data->DPB) ? $shipping_data->DPB : ""),
                'CheckDigit' => (isset($shipping_data->CheckDigit) ? $shipping_data->CheckDigit : ""),
                'Province' => '',
                'PostalCode' => '',
                'Country' => '',
                'Urbanization' => '',
                'PhoneNumber' => $shippingAddress->getPhone(),
                'Extension' => '',
                'CleanseHash' => (isset($shipping_data->CleanseHash) ? $shipping_data->CleanseHash : ""),
                'OverrideHash' => (isset($shipping_data->OverrideHash) ? $shipping_data->OverrideHash : "")
            ),
        );

        try{
            $response = $this->soapClient->CreateIndicium($callData);
            return $response;
        }catch (\SoapFault $e){
            return "";
        }
    }

    public function getShippingStatusByTrackingNumber( $stampTxId = null)
    {
        if(!is_null($stampTxId))
        {
            $callData = array(
                "Credentials"       => array(
                    "IntegrationID"     => $this->integrarionId,
                    "Username"          => $this->userName,
                    "Password"          => $this->password
                ),
                'StampsTxID' => $stampTxId
            );
            try{
                $response = $this->soapClient->TrackShipment($callData);
                $tracking_event = $response->TrackingEvents->TrackingEvent->Event;

                return $tracking_event;

            }catch (\SoapFault $e){
                return "Pending";
            }
        }else{
            return "pending";
        }
    }

    public function getRates( $postData = array(), $weightInOz = 0)
    {
        $returnResponse = array();
        $fieldsVerified = $this->verifyFields( $postData, 'getRates');
        if($fieldsVerified['verified']){
            $authenticator = $this->authenticateUser();
            $callData = array(
                "Credentials"       => array(
                    "IntegrationID"     => $this->integrarionId,
                    "Username"          => $this->userName,
                    "Password"          => $this->password
                ),
                'Rate' => array(
                    'FromZIPCode'   => $this->fromZipCode,
                    'ToZIPCode'     => $postData['tozipcode'],
                    'ServiceType'   => $this->serviceType,
                    'DeliverDays'   => $this->deliverDays,
                    'WeightOz'      => $weightInOz,
                    'PackageType'   => $this->packageType,
                    'ShipDate'      => date('Y-m-d'),
                    'RectangularShaped' => false
                ));

            $responseRates = $this->soapClient->GetRates($callData);
            $rates = $responseRates->Rates->Rate;

            if(is_object($rates)){
                $temp['shipping_rate_amount'] = $rates->Amount; //new variable here
                $temp['amount'] = 0; //reset to zero
                $temp['deliverDays'] = (isset($rates->DeliverDays)) ? $rates->DeliverDays : "";;
                $temp['shipDate'] = $rates->ShipDate;
                $temp['deliveryDate'] = (isset($rates->DeliveryDate)) ? $rates->DeliveryDate : "";;
                $temp['serviceType'] = $rates->ServiceType;
                $temp['FromZIPCode'] = $rates->FromZIPCode;
                $temp['ToZIPCode']  = $rates->ToZIPCode;
                $temp['DeliverDays'] = $rates->DeliverDays;
                $temp['WeightOz']   = $rates->WeightOz;
                $temp['InsuredValue'] = (isset($rates->InsuredValue) ? $rates->InsuredValue : 0);
                $temp['RectangularShaped'] = $rates->RectangularShaped;

                array_push( $returnResponse, $temp);
            }else if(is_array($rates)){
                foreach ($rates as $rate){
                    $temp['shipping_rate_amount'] = $rate->Amount; //new variable here
                    $temp['amount'] = 0; //reset to zero
                    $temp['deliverDays'] = (isset($rate->DeliverDays)) ? $rate->DeliverDays : "";
                    $temp['shipDate'] = $rate->ShipDate;
                    $temp['deliveryDate'] = (isset($rate->DeliveryDate)) ? $rate->DeliveryDate : "";
                    $temp['serviceType'] = $rate->ServiceType;
                    $temp['FromZIPCode'] = $rate->FromZIPCode;
                    $temp['ToZIPCode']  = $rate->ToZIPCode;
                    $temp['DeliverDays'] = $rate->DeliverDays;
                    $temp['WeightOz']   = $rate->WeightOz;
                    $temp['InsuredValue'] = (isset($rate->InsuredValue) ? $rate->InsuredValue : 0);
                    $temp['RectangularShaped'] = $rate->RectangularShaped;

                    array_push( $returnResponse, $temp);
                }
            }

            return array(
                'verified' => true,
                'shipping_method' => $returnResponse
            );
        } else{
            return $fieldsVerified;
        }
    }

    private function verifyFields( $postArray = array(), $method = null){
        switch ($method){
            case "addressVerfication":
                if(!isset($postArray['fullname'])){
                    if(!isset($postArray['firstname']))
                        return array(
                            'msg' => 'First name and last name cannot be empty if not using full',
                            'verified' => false
                        );
                    else if(!isset($postArray['lastname']))
                        return array(
                            'msg' => 'First name and last name cannot be empty if not using full',
                            'verified' => false
                        );
                }

                if(!isset($postArray['address1']))
                    return array(
                        'msg' => 'address1 should not empty',
                        'verified' => false
                    );

                if(!isset($postArray['zipcode'])){
                    if(!isset($postArray['city']))
                        return array(
                            'msg' => "city and state can not be empty if not using postcode",
                            'verified' => false
                        );
                    else if(!isset($postArray['state']))
                        return array(
                            'msg' => "state and city can not be empty if not using postcode",
                            'verified' => false
                        );
                }

                return array(
                    'msg' => "",
                    'verified' => true
                );
                break;

            case "getRates":
                if(!isset($postArray['tozipcode']))
                    return array(
                        'msg' => "to zipcode can not be empty!",
                        'verified' => false
                    );

                return array(
                    'msg' => "",
                    'verified' => true
                );
                break;

            case "createIndicium":
                break;
        }
    }
}