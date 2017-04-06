<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 4/4/2017
 * Time: 3:55 PM
 */

namespace LoveThatFit\CartBundle\Utils;

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

    private $soapClient;

    public function __construct()
    {
        $envKey = 'stamps_com_dev';

        $yaml   = new Parser();
        $env    = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['enviorment'];
        $parse  = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
        if($env == 'dev')
            $envKey = 'stamps_com_dev';

        $this->integrarionId    = $parse[$envKey]["integrarionId"];
        $this->userName         = $parse[$envKey]["userName"];
        $this->password         = $parse[$envKey]["password"];
        $this->fromZipCode      = $parse[$envKey]["fromzipcode"];
        $this->serviceType      = $parse[$envKey]["serviceType"];
        $this->weightOz         = $parse[$envKey]["weightOz"];
        $this->deliverDays      = $parse[$envKey]["deliverydays"];
        $this->packageType      = $parse[$envKey]["packagetype"];

        $this->soapClient = new \SoapClient($this->wsdl);
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

    public function addressVerfication( $postData = array())
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
                    'FullName'  => $address['fullname'],
                    'FirstName' => $address['firstname'],
                    'LastName'  => $address['lastname'],
                    'Address1'  => $address['address1'],
                    'Address2'  => $address['address2'],
                    'City'      => $address['city'],
                    'State'     => $address['state'],
                    'ZIPCode'   => $address['zipcode']
                ));

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
        }else{
            return $fieldsVerified;
        }
    }

    public function getRates( $postData = array())
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
                    'WeightOz'      => $this->weightOz,
                    'PackageType'   => $this->packageType,
                    'ShipDate'      => date('Y-m-d')
                ));

            $responseRates = $this->soapClient->GetRates($callData);
            $rates = $responseRates->Rates->Rate;

            if(is_object($rates)){
                $temp['amount'] = $rates->Amount;
                $temp['deliverDays'] = $rates->DeliverDays;
                $temp['shipDate'] = $rates->ShipDate;
                $temp['deliveryDate'] = $rates->DeliveryDate;
                $temp['serviceType'] = $rates->ServiceType;

                array_push( $returnResponse, $temp);
            }else if(is_array($rates)){
                foreach ($rates as $rate){

                    $temp['amount'] = $rate->Amount;
                    $temp['deliverDays'] = (isset($rate->DeliverDays)) ? $rate->DeliverDays : "";
                    $temp['shipDate'] = $rate->ShipDate;
                    $temp['deliveryDate'] = (isset($rate->DeliveryDate)) ? $rate->DeliveryDate : "";;
                    $temp['serviceType'] = $rate->ServiceType;

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
        }
    }
}