<?php

namespace LoveThatFit\CartBundle\Entity;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class UserAddressesHelper
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
    public function saveAddress($decoded, $user, $bill_info, $ship_info)
    {
        $address = $decoded["billing"];
        if ($bill_info == 1) {
            $address_info = $this->createNew();
            $address_info->setUser($user);
            $address_info->setFirstName($address["billing_first_name"]);
            $address_info->setLastName($address["billing_last_name"]);
            $address_info->setAddress1($address["billing_address1"]);
            $address_info->setAddress2($address["billing_address2"]);
            $address_info->setPhone($address["billing_phone"]);
            $address_info->setCity($address["billing_city"]);
            $address_info->setPostCode($address["billing_postcode"]);
            $address_info->setCountry($address["billing_country"]);
            $address_info->setState($address["billing_state"]);
            $address_info->setBillingDefault('1');
            if ($ship_info == 0) {
                $address_info->setShippingDefault('1');
            } else {
                $address_info->setShippingDefault('0');
            }
            $this->save($address_info);
        }
        if ($ship_info == 1) {
            $address_info = $this->createNew();
            $address_info->setUser($user);
            $address_info->setFirstName($address["shipping_first_name"]);
            $address_info->setLastName($address["shipping_last_name"]);
            $address_info->setAddress1($address["shipping_address1"]);
            $address_info->setAddress2($address["shipping_address2"]);
            $address_info->setPhone($address["shipping_phone"]);
            $address_info->setCity($address["shipping_city"]);
            $address_info->setPostCode($address["shipping_postcode"]);
            $address_info->setCountry($address["shipping_country"]);
            $address_info->setState($address["shipping_state"]);
            $address_info->setShippingDefault('1');
            $address_info->setBillingDefault('0');
            return $this->save($address_info);
        }
    }

    public function saveUserBillingAddress($decoded, $user)
    {
        $addedAddressIds = array();
        $address = $decoded["billing"];
        if(!empty($address)){

            if($address['shipping_same'] == 1){
                $address_info = $this->createNew();
                $address_info->setUser($user);
                $address_info->setFirstName($address["billing_first_name"]);
                $address_info->setLastName($address["billing_last_name"]);
                $address_info->setAddress1($address["billing_address1"]);
                $address_info->setAddress2($address["billing_address2"]);
                $address_info->setPhone($address["billing_phone"]);
                $address_info->setCity($address["billing_city"]);
                $address_info->setPostCode($address["billing_postcode"]);
                $address_info->setCountry($address["billing_country"]);
                $address_info->setState($address["billing_state"]);

                if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                    $address_info->setCleanseHash($address['cleanseHash']);

                //$this->markedPreviousShippingAddressNonDefault($user);

                $isDefault = 0;
                if(!$this->getUserAddress( $user )){
                    $isDefault = 1;
                }

                $address_info->setShippingDefault($isDefault);
                $address_info->setBillingDefault('0');
                $address_info->setAddressType('2');

                if(isset($decoded['data']))
                    $address_info->setAddressData(json_encode($decoded['data']));

                $addedAddressIds['shipping'] = $this->save($address_info);
            }

            $address_info = $this->createNew();
            $address_info->setUser($user);
            $address_info->setFirstName($address["billing_first_name"]);
            $address_info->setLastName($address["billing_last_name"]);
            $address_info->setAddress1($address["billing_address1"]);
            $address_info->setAddress2($address["billing_address2"]);
            $address_info->setPhone($address["billing_phone"]);
            $address_info->setCity($address["billing_city"]);
            $address_info->setPostCode($address["billing_postcode"]);
            $address_info->setCountry($address["billing_country"]);
            $address_info->setState($address["billing_state"]);
            if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                $address_info->setCleanseHash($address['cleanseHash']);
            if( $address["billing_default"] == 1)
            {
                $this->markedPreviousBillingAddressNonDefault($user);
                $address_info->setBillingDefault('1');
            }else {
                $isDefault = 0;
                if(!$this->getUserAddress( $user )){
                    $isDefault = 1;
                }
                $address_info->setBillingDefault($isDefault);
            }
            $address_info->setShippingDefault('0');
            $address_info->setAddressType('1');

            if(isset($decoded['data']))
                $address_info->setAddressData(json_encode($decoded['data']));
            $addedAddressIds['billing'] = $this->save($address_info);

            return $addedAddressIds;
        }

        return false;
    }

    public function saveUserShippingAddress($decoded, $user){
        $address = $decoded["shipping"];
        if(!empty($address)) {
            $address_info = $this->createNew();
            $address_info->setUser($user);
            $address_info->setFirstName($address["shipping_first_name"]);
            $address_info->setLastName($address["shipping_last_name"]);
            $address_info->setAddress1($address["shipping_address1"]);
            $address_info->setAddress2($address["shipping_address2"]);
            $address_info->setPhone($address["shipping_phone"]);
            $address_info->setCity($address["shipping_city"]);
            $address_info->setPostCode($address["shipping_postcode"]);
            $address_info->setCountry($address["shipping_country"]);
            $address_info->setState($address["shipping_state"]);
            if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                $address_info->setCleanseHash($address['cleanseHash']);

            if( $address["shipping_default"] == 1)
            {
                $this->markedPreviousShippingAddressNonDefault($user);
                $address_info->setShippingDefault('1');
            }else {
                $isDefault = 0;
                if(!$this->getUserAddress( $user, 2)){
                    $isDefault = 1;
                }
                $address_info->setShippingDefault($isDefault);
            }
            $address_info->setBillingDefault('0');
            $address_info->setAddressType('2');

            if(isset($decoded['data']))
                $address_info->setAddressData(json_encode($decoded['data']));

            return $this->save($address_info);
        }

        return false;
    }

    public function updateUserBillingAddress($decoded, $user){
        $billing_id = $decoded['billing_id'];
        if($billing_id > 0){
            $address = $decoded["billing"];

            if($address['shipping_same'] == 1){
                $address_info = $this->createNew();
                $address_info->setUser($user);
                $address_info->setFirstName($address["billing_first_name"]);
                $address_info->setLastName($address["billing_last_name"]);
                $address_info->setAddress1($address["billing_address1"]);
                $address_info->setAddress2($address["billing_address2"]);
                $address_info->setPhone($address["billing_phone"]);
                $address_info->setCity($address["billing_city"]);
                $address_info->setPostCode($address["billing_postcode"]);
                $address_info->setCountry($address["billing_country"]);
                $address_info->setState($address["billing_state"]);

                if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                    $address_info->setCleanseHash($address['cleanseHash']);

                //$this->markedPreviousShippingAddressNonDefault($user);

                $isDefault = 0;
                if(!$this->getUserAddress( $user )){
                    $isDefault = 1;
                }

                $address_info->setShippingDefault($isDefault);
                $address_info->setBillingDefault('0');
                $address_info->setAddressType('2');

                if(isset($decoded['data']))
                    $address_info->setAddressData(json_encode($decoded['data']));
                $this->save($address_info);
            }

            $address_info = $this->find($billing_id);

            $address_info->setFirstName($address["billing_first_name"]);
            $address_info->setLastName($address["billing_last_name"]);
            $address_info->setAddress1($address["billing_address1"]);
            $address_info->setAddress2($address["billing_address2"]);
            $address_info->setPhone($address["billing_phone"]);
            $address_info->setCity($address["billing_city"]);
            $address_info->setPostCode($address["billing_postcode"]);
            $address_info->setCountry($address["billing_country"]);
            $address_info->setState($address["billing_state"]);
            if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                $address_info->setCleanseHash($address['cleanseHash']);
            if( $address["billing_default"] == 1)
            {
                $this->markedPreviousBillingAddressNonDefault($user);
                $address_info->setBillingDefault('1');
            }else {
                $address_info->setBillingDefault('0');
            }
            $address_info->setShippingDefault('0');
            $address_info->setAddressType('1');

            if(isset($decoded['data']))
                $address_info->setAddressData(json_encode($decoded['data']));

            return $this->save($address_info);
        }
        return false;
    }

    public function deleteUserShippingAddress( $shipping_id, User $user){
        $address_info = $this->find($shipping_id);

        if($address_info->getShippingDefault())
            $this->setOtherAddressAsDefault( $user, 2);

        $this->em->remove($address_info);
        $this->em->flush();
    }

    public function setOtherAddressAsDefault(User $user, $addressType){
        $params = array();
        if($addressType == 2){
            $params = array(
                'user' => $user->getId(),
                'adress_type' => $addressType,
                'shipping_default' => 0
            );
        }else{
            $params = array(
                'user' => $user->getId(),
                'adress_type' => $addressType,
                'billing_default' => 0
            );
        }
        $userAddressObject = $this->repo->findOneBy($params);
        if(is_object($userAddressObject)){
            if($addressType == 2)
                $userAddressObject->setShippingDefault('1');
            else
                $userAddressObject->setBillingDefault('1');

            $this->save($userAddressObject);
            return;
        }

        return;
    }

    public function deleteUserBillingAddress( $billing_id, User $user){
        $address_info = $this->find($billing_id);

        if($address_info->getBillingDefault())
            $this->setOtherAddressAsDefault( $user, 1);

        $this->em->remove($address_info);
        $this->em->flush();
    }

    public function updateUserShippingAddress($decoded, $user){
        $shipping_id = $decoded['shipping_id'];
        if($shipping_id > 0){
            $address = $decoded["shipping"];
            $address_info = $this->find($shipping_id);

            $address_info->setFirstName($address["shipping_first_name"]);
            $address_info->setLastName($address["shipping_last_name"]);
            $address_info->setAddress1($address["shipping_address1"]);
            $address_info->setAddress2($address["shipping_address2"]);
            $address_info->setPhone($address["shipping_phone"]);
            $address_info->setCity($address["shipping_city"]);
            $address_info->setPostCode($address["shipping_postcode"]);
            $address_info->setCountry($address["shipping_country"]);
            $address_info->setState($address["shipping_state"]);
            if(isset($address['cleanseHash']) && !empty($address['cleanseHash']))
                $address_info->setCleanseHash($address['cleanseHash']);

            if( $address["shipping_default"] == 1)
            {
                $this->markedPreviousShippingAddressNonDefault($user);
                $address_info->setShippingDefault('1');
            }else {
                $address_info->setShippingDefault('0');
            }
            $address_info->setBillingDefault('0');

            if(isset($decoded['data']))
                $address_info->setAddressData(json_encode($decoded['data']));

            return $this->save($address_info);
        }
        return false;
    }

    public function markedPreviousShippingAddressNonDefault(User $user)
    {
        $userAddressObject = $this->repo->findOneBy(array(
            'user' => $user->getId(),
            'adress_type' => 2,
            'shipping_default' => 1
        ));

        if(is_object($userAddressObject)){
            $userAddressObject->setShippingDefault('0');
            $this->save($userAddressObject);
        }

        return;
    }

    public function markedPreviousBillingAddressNonDefault(User $user)
    {
        $userAddressObject = $this->repo->findOneBy(array(
            'user' => $user->getId(),
            'adress_type' => 1,
            'billing_default' => 1
        ));

        // var_dump( $userAddressObject ); die;

        if(is_object($userAddressObject)){
            $userAddressObject->setBillingDefault('0');
            $this->save($userAddressObject);
        }

        return;
    }

    public function getUserAddress(User $user, $type = 1){
        $userAddressObject = $this->repo->findOneBy(array(
            'user' => $user->getId(),
            'adress_type' => $type
        ));

        if(is_object($userAddressObject)){
            return true;
        }

        return false;
    }

    public function getAllUserSavedAddresses(User $user)
    {
        $shippingAddresses = array();
        $billingAddresses = array();
        if(is_object( $user )){
            $userAddresses = $this->repo->findBy(
                array(
                    'user' => $user->getId(),
                    'adress_type' => array(1,2)
                ),
                array(
                    'billing_default' => 'DESC',
                    'shipping_default' => 'DESC'
                )
            );

            // var_dump($userAddresses); die;

            foreach($userAddresses as $address)
            {
                // echo $address->getAddressType();
                if($address->getAddressType() == 2){
                    $shippingAddresses[] = array(
                        'first_name' => $address->getFirstName(),
                        'last_name' => $address->getLastName(),
                        'address1' => $address->getAddress1(),
                        'address2' => $address->getAddress2(),
                        'phone' => $address->getPhone(),
                        'city' => $address->getCity(),
                        'country' =>$address->getCountry(),
                        'postcode' => (string) $address->getPostCode(),
                        'state' => $address->getState(),
                        'default' => $address->getShippingDefault(),
                        'id'       => $address->getId()
                    );
                }else{
                    $billingAddresses[] = array(
                        'first_name' => $address->getFirstName(),
                        'last_name' => $address->getLastName(),
                        'address1' => $address->getAddress1(),
                        'address2' => $address->getAddress2(),
                        'phone' => $address->getPhone(),
                        'city' => $address->getCity(),
                        'country' => $address->getCountry(),
                        'postcode' => (string) $address->getPostCode(),
                        'state' => $address->getState(),
                        'default' => $address->getBillingDefault(),
                        'id' => $address->getId()
                    );
                }
            }

            return array(
                'billing' => $billingAddresses,
                'shipping' => $shippingAddresses
            );
        }

        return array(
            'billing' => array(),
            'shipping' => array()
        );
    }


#------------------------------Find address by id--------------------------------#
    public function findAddressById($id)
    {
        return $this->repo->find($id);
    }

    public function findByCriteria( array $criteria)
    {
        return $this->repo->findOneBy( $criteria );
    }

#------------------------------Find All address by user id --------------------------------#
    public function getAllAddresses($user)
    {
        return $this->repo->findAllAddressByUserId($user);
    }

#------------------------------Find All address by user id and bill =1 or 0 which means billing or shipping--------------------------------#
    public function getUserAddresses($user, $bill)
    {
        return $this->repo->findAddressByUserId($user, $bill);
    }

#------------------------------Find User Default address --------------------------------#
    public function getUserDefaultAddresses($user, $is_billing)
    {
        if ($is_billing == 1) {
            return $this->repo->findDefaultBillingAddressByUserId($user);
        } else {
            return $this->repo->findDefaultShippingAddressByUserId($user);
        }

    }

    #------------------------------Find All address by user id--------------------------------#
    public function getUserAddressesCount($user)
    {
        return $this->repo->findAddressCountByUserId($user);
    }

    //-------------------------
    public function save($user_addresses)
    {
        $class = $this->class;
        $this->em->persist($user_addresses);
        $this->em->flush();
        return $user_addresses;
    }

    //-------------------------Create New Address--------------------------------------------

    public function createNew()
    {
        $class = $this->class;
        $user_addresses = new $class();
        return $user_addresses;
    }

    #------------------------------Update All address Billing and Shipping Default values by user id--------------------------------#
    public function saveUserAddressValue($user, $val)
    {
        return $this->repo->findUserAddressValue($user, $val);
    }

    //------------------------------- Update User Address -----------------------------------------------------///////////
    public function updateUserAddresses($user, $decoded)
    {
        $billing_default = isset($decoded["billing_default"]) ? $decoded["billing_default"] : 'off';
        $shipping_default = isset($decoded["shipping_default"]) ? $decoded["shipping_default"] : 'off';

        if ($shipping_default != 'off') {
            $this->saveUserAddressValue($user, 2);
            $shipping_default_val = '1';
            $billing_default_val = '0';
        }
        if ($billing_default != 'off') {
            $this->saveUserAddressValue($user, 1);
            $billing_default_val = '1';
            $shipping_default_val = '0';

        }

        $user_addresses = $this->find($decoded["address_id"]);
        $user_addresses->setFirstName($decoded["first_name"]);
        $user_addresses->setLastName($decoded["last_name"]);
        $user_addresses->setAddress1($decoded["address1"]);
        $user_addresses->setAddress2($decoded["address2"]);
        $user_addresses->setPhone($decoded["phone"]);
        $user_addresses->setCity($decoded["city"]);
        $user_addresses->setState($decoded["state"]);
        $user_addresses->setPostcode($decoded["postcode"]);
        $user_addresses->setCountry($decoded["country"]);
        if ($shipping_default != 'off') {
            $user_addresses->setShippingDefault($shipping_default_val);
        }
        if ($billing_default != 'off') {
            $user_addresses->setBillingDefault($billing_default_val);
        }
        return $this->save($user_addresses);
    }

    //--------- Add User Address -------------------------------------
    public function addUserAddresses($user, $decoded)
    {
        $billing_default = isset($decoded["billing_default"]) ? $decoded["billing_default"] : 'off';
        $shipping_default = isset($decoded["shipping_default"]) ? $decoded["shipping_default"] : 'off';
        $address_info = $this->createNew();
        $address_info->setUser($user);
        $address_info->setFirstName($decoded["first_name"]);
        $address_info->setLastName($decoded["last_name"]);
        $address_info->setAddress1($decoded["address1"]);
        $address_info->setAddress2($decoded["address2"]);
        $address_info->setPhone($decoded["phone"]);
        $address_info->setCity($decoded["city"]);
        $address_info->setPostCode($decoded["postcode"]);
        $address_info->setCountry($decoded["country"]);
        $address_info->setState($decoded["state"]);
        if ($shipping_default != 'off') {
            $this->saveUserAddressValue($user, 2);
            $address_info->setShippingDefault('1');
            $address_info->setBillingDefault('0');
        }
        if ($billing_default != 'off') {
            $this->saveUserAddressValue($user, 1);
            $address_info->setBillingDefault('1');
            $address_info->setShippingDefault('0');
        }
        if ($billing_default == 'off' && $shipping_default == 'off') {
            $address_info->setBillingDefault('0');
            $address_info->setShippingDefault('0');
        }
        return $this->save($address_info);
    }

//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id)
    {

        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('user_addresses' => $entity,
                'message' => 'Address has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('user_addresses' => $entity,
                'message' => 'Address not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//----------------------Find Address By ID----------------------------------------------------------------

    public function find($id)
    {
        return $this->repo->find($id);
    }

    #--------------------Find All Addresses ---------------------------------------------------------------------------------
    public function findAll()
    {
        return $this->repo->findAll();
    }

//----------------------Find Address By name----------------------------------------------------------------
    public function findOneByName($name)
    {
        return $this->repo->findOneByName($name);
    }


}