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
	public function saveAddress($decoded,$user) {
	  $address = $decoded["billing"];
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
	  $address_info->setIsBilling('1');
	  $this->save($address_info);

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
	  $address_info->setIsBilling('0');
	  return $this->save($address_info);

	}


#------------------------------Find address by id--------------------------------#
  public function findAddressById($id){
	return $this->repo->find($id);
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