<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * UserAddresses
 *
 * @ORM\Table(name="user_addresses")
 * @ORM\Entity(repositoryClass="LoveThatFit\CartBundle\Entity\UserAddressesRepository")
 */
class UserAddresses
{

	public function __construct()
	  {
		$this->user = new ArrayCollection();
	  }

	/**
	 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_addresses")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="address1", type="string", length=255)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=255)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=20)
     */
    private $country;

	/**
	 * @var boolean $is_billing
	 * @ORM\Column(name="is_billing", type="boolean", nullable=false , options={"default":"0"})
	 */
	private $is_billing;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return UserAddresses
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return UserAddresses
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return UserAddresses
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    
        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return UserAddresses
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    
        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return UserAddresses
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return UserAddresses
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return UserAddresses
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return UserAddresses
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    
        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return UserAddresses
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }
	/**
	 * Set is_billing
	 *
	 * @param string $is_billing
	 * @return UserAddresses
	 */
	public function setIsBilling($is_billing)
	{
	  $this->is_billing = $is_billing;
	}

	/**
	 * Get is_billing
	 *
	 * @return string
	 */
	public function getIsBilling()
	{
	  return (bool)$this->is_billing;
	}

	/**
	 * Set user
	 *
	 * @param \LoveThatFit\UserBundle\Entity\User $user
	 * @return Cart
	 */
	public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null)
	{
	  $this->user = $user;

	  return $this;
	}

	/**
	 * Get user
	 *
	 * @return \LoveThatFit\UserBundle\Entity\User
	 */
	public function getUser()
	{
	  return $this->user;
	}

	#---------------------------------------------------
	public function toArray(){

	  $obj = array();
	  $obj['id'] = $this->getId();
	  $obj['first_name'] = $this->getFirstName();
	  $obj['last_name'] = $this->getLastName();
	  $obj['address1'] = $this->getAddress1();
	  $obj['address2'] = $this->getAddress2();
	  $obj['phone'] = $this->getPhone();
	  $obj['city'] = $this->getCity();
	  $obj['state'] = $this->getState();
	  $obj['postcode'] = $this->getPostcode();
	  $obj['country'] = $this->getCountry();
	  $obj['user'] = $this->getUser();
	  return $obj;

	}
}
