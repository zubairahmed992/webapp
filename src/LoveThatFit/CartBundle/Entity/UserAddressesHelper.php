<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class UserAddressesHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
//------------------------------- Add to Cart clicked -----------------------------------------------------///////////
	public function saveAddress($decoded,$user,$bill_info,$ship_info) {
	  $address = $decoded["billing"];
	  if($bill_info == 1){
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
	  //$address_info->setIsBilling('0');
	  $address_info->setBillingDefault('1');
	  //$address_info->setIsShipping('0');
	  if($ship_info == 0){
		$address_info->setShippingDefault('1');
	  }else{
	  $address_info->setShippingDefault('0');
	  }
	  	$this->save($address_info);
	  }
	  if($ship_info == 1){
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
	  //$address_info->setIsShipping('1');
	  $address_info->setShippingDefault('1');
	  //$address_info->setIsBilling('0');
	  $address_info->setBillingDefault('0');
	  return $this->save($address_info);
	  }
	}


#------------------------------Find address by id--------------------------------#
  public function findAddressById($id){
	return $this->repo->find($id);
  }
#------------------------------Find All address by user id --------------------------------#
  public function getAllAddresses($user){
	return $this->repo->findAllAddressByUserId($user);
  }
#------------------------------Find All address by user id and bill =1 or 0 which means billing or shipping--------------------------------#
  public function getUserAddresses($user,$bill){
	return $this->repo->findAddressByUserId($user,$bill);
  }
#------------------------------Find User Default address --------------------------------#
  public function getUserDefaultAddresses($user,$is_billing){
	if($is_billing == 1){
	  return $this->repo->findDefaultBillingAddressByUserId($user);
	}else{
	  return $this->repo->findDefaultShippingAddressByUserId($user);
	}

  }
  #------------------------------Find All address by user id--------------------------------#
  public function getUserAddressesCount($user){
	return $this->repo->findAddressCountByUserId($user);
  }
	//-------------------------
	public function save($user_addresses) {
	  $class = $this->class;
	  $this->em->persist($user_addresses);
	  $this->em->flush();
	  return $user_addresses;
	}
  //-------------------------Create New Address--------------------------------------------

    public function createNew() {
        $class = $this->class;
	  	$user_addresses = new $class();
        return $user_addresses;
    }
  #------------------------------Update All address Billing and Shipping Default values by user id--------------------------------#
  public function saveUserAddressValue($user,$val){
	return $this->repo->findUserAddressValue($user,$val);
  }

  //------------------------------- Update User Address -----------------------------------------------------///////////
  public function updateUserAddresses($user,$decoded) {
//	$is_billing='1';
//	$is_shipping='1';
//	echo "<pre>";
//	print_r($decoded);die;
	$billing_default = isset($decoded["billing_default"])?$decoded["billing_default"]:'off';
	$shipping_default = isset($decoded["shipping_default"])?$decoded["shipping_default"]:'off';

//	echo $billing_default."<br>";
//	echo $shipping_default;
//	die;
	if($shipping_default != 'off'){
	  $this->saveUserAddressValue($user,2);
	  $shipping_default_val = '1';
	  $billing_default_val = '0';
	}
	if($billing_default != 'off'){
	  $this->saveUserAddressValue($user,1);
	  $billing_default_val = '1';
	  $shipping_default_val = '0';

	}
	//echo $shipping_default_val;die;
//	if($billing_default == "on" && $shipping_default == "off"){
//	  ### update all billing default to 0 for that user and
//	  $val='1';
//	  $billing_default = '1';
//	  $shipping_default = '0';
//
//	}
//	if($billing_default == "off" && $shipping_default == "on"){
//	  ### update all shipping default to 0 for that user and
//	  $val='2';
//	  $billing_default = '0';
//	  $shipping_default = '1';
//	}

//		echo $val;
//		echo "<br>";
//		echo $billing_default;
//		echo "<br>";
//		echo $shipping_default;
//		die;
	  //$this->saveUserAddressValue($user);

	  $user_addresses=$this->find($decoded["address_id"]);
	  $user_addresses->setFirstName($decoded["first_name"]);
	  $user_addresses->setLastName($decoded["last_name"]);
	  $user_addresses->setAddress1($decoded["address1"]);
	  $user_addresses->setAddress2($decoded["address2"]);
	  $user_addresses->setPhone($decoded["phone"]);
	  $user_addresses->setCity($decoded["city"]);
	  $user_addresses->setState($decoded["state"]);
	  $user_addresses->setPostcode($decoded["postcode"]);
	  $user_addresses->setCountry($decoded["country"]);
	  if($shipping_default != 'off'){
	  	$user_addresses->setShippingDefault($shipping_default_val);
	  }
	  if($billing_default != 'off'){
	 	 $user_addresses->setBillingDefault($billing_default_val);
	  }

//	  if($shipping_default != 'off'){
//		$user_addresses->setShippingDefault('1');
//	  }
//	  if($billing_default != 'off'){
//		$user_addresses->setBillingDefault('1');
//	  }
	  return $this->save($user_addresses);
  }
	//--------- Add User Address -------------------------------------
  public function addUserAddresses($user,$decoded) {
	$billing_default = isset($decoded["billing_default"])?$decoded["billing_default"]:'off';
	$shipping_default = isset($decoded["shipping_default"])?$decoded["shipping_default"]:'off';
//	echo $billing_default."<br>";
//	echo $shipping_default."<br>";
//	die;
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
	if($shipping_default != 'off'){
	  $this->saveUserAddressValue($user,2);
	  $address_info->setShippingDefault('1');
	  $address_info->setBillingDefault('0');
	  //$address_info->setIsShipping('1');
	  //$address_info->setIsBilling('0');
	}
	if($billing_default != 'off'){
	  $this->saveUserAddressValue($user,1);
	  $address_info->setBillingDefault('1');
	  $address_info->setShippingDefault('0');
	  //$address_info->setIsShipping('0');
	  //$address_info->setIsBilling('1');
	}
	if($billing_default == 'off' && $shipping_default == 'off'){
	  $address_info->setBillingDefault('0');
	  $address_info->setShippingDefault('0');
	  //$address_info->setIsShipping('0');
	  //$address_info->setIsBilling('0');
	}
	return $this->save($address_info);
  }
//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id) {

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

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All Addresses ---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------Find Address By name----------------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }




   
}