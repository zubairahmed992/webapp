<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * UserOrder
 *
 * @ORM\Table(name="user_orders")
 * @ORM\Entity(repositoryClass="LoveThatFit\CartBundle\Entity\UserOrderRepository")
 */
class UserOrder
{


	/**
	 * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\UserOrderDetail", mappedBy="user_order")
	 */
	protected $user_order_detail;

  	/**
	 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_orders")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	public function __construct()
	{
	  $this->user = new ArrayCollection();
	  $this->user_order_detail = new ArrayCollection();
	}
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime")
     */
    private $order_date;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_first_name", type="string", length=255)
     */
    private $billing_first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_last_name", type="string", length=255)
     */
    private $billing_last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_address1", type="string", length=255)
     */
    private $billing_address1;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_address2", type="string", length=255 ,nullable=true)
     */
    private $billing_address2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="billing_phone", type="string", length=20)
	 */
	private $billing_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_city", type="string", length=255)
     */
    private $billing_city;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_postcode", type="string", length=255)
     */
    private $billing_postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_country", type="string", length=255)
     */
    private $billing_country;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_state", type="string", length=255)
     */
    private $billing_state;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_first_name", type="string", length=255)
     */
    private $shipping_first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_last_name", type="string", length=255)
     */
    private $shipping_last_name;


    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address1", type="string", length=255)
     */
    private $shipping_address1;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address2", type="string", length=255 ,nullable=true)
     */
    private $shipping_address2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="shipping_phone", type="string", length=20)
	 */
	private $shipping_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_city", type="string", length=255)
     */
    private $shipping_city;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_postcode", type="string", length=255)
     */
    private $shipping_postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_country", type="string", length=255)
     */
    private $shipping_country;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_state", type="string", length=255)
     */
    private $shipping_state;

    /**
     * @var string
     *
     * @ORM\Column(name="order_status", type="string", length=255)
     */
    private $order_status;

	/**
	 * @ORM\Column(type="integer")
	 *
	 */
	private $order_amount;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="transaction_status", type="string", length=255 , nullable=true)
	 */
	private $transaction_status;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="transaction_id", type="string", length=255, nullable=true)
	 */
	private $transaction_id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="payment_method", type="string", length=255 , nullable=true)
	 */
	private $payment_method;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="payment_json", type="text" , nullable=true)
	 */
	private $payment_json;



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
     * Set order_date
     *
     * @param \DateTime $orderDate
     * @return UserOrder
     */
    public function setOrderDate($orderDate)
    {
        $this->order_date = $orderDate;
    
        return $this;
    }

    /**
     * Get order_date
     *
     * @return \DateTime 
     */
    public function getOrderDate()
    {
        return $this->order_date;
    }

    /**
     * Set billing_first_name
     *
     * @param string $billingFirstName
     * @return UserOrder
     */
    public function setBillingFirstName($billingFirstName)
    {
        $this->billing_first_name = $billingFirstName;
    
        return $this;
    }

    /**
     * Get billing_first_name
     *
     * @return string 
     */
    public function getBillingFirstName()
    {
        return $this->billing_first_name;
    }

    /**
     * Set billing_last_name
     *
     * @param string $billingLastName
     * @return UserOrder
     */
    public function setBillingLastName($billingLastName)
    {
        $this->billing_last_name = $billingLastName;
    
        return $this;
    }

    /**
     * Get billing_last_name
     *
     * @return string 
     */
    public function getBillingLastName()
    {
        return $this->billing_last_name;
    }

    /**
     * Set billing_address1
     *
     * @param string $billingAddress1
     * @return UserOrder
     */
    public function setBillingAddress1($billingAddress1)
    {
        $this->billing_address1 = $billingAddress1;
    
        return $this;
    }

    /**
     * Get billing_address1
     *
     * @return string 
     */
    public function getBillingAddress1()
    {
        return $this->billing_address1;
    }

    /**
     * Set billing_address2
     *
     * @param string $billingAddress2
     * @return UserOrder
     */
    public function setBillingAddress2($billingAddress2)
    {
        $this->billing_address2 = $billingAddress2;
    
        return $this;
    }

    /**
     * Get billing_address2
     *
     * @return string 
     */
    public function getBillingAddress2()
    {
        return $this->billing_address2;
    }

	/**
	 * Set billing_phone
	 *
	 * @param string $billingPhone
	 * @return UserOrder
	 */
	public function setBillingPhone($billingPhone)
	{
	  $this->billing_phone = $billingPhone;

	  return $this;
	}

	/**
	 * Get billing_phone
	 *
	 * @return string
	 */
	public function getBillingPhone()
	{
	  return $this->billing_phone;
	}

    /**
     * Set billing_city
     *
     * @param string $billingCity
     * @return UserOrder
     */
    public function setBillingCity($billingCity)
    {
        $this->billing_city = $billingCity;
    
        return $this;
    }

    /**
     * Get billing_city
     *
     * @return string 
     */
    public function getBillingCity()
    {
        return $this->billing_city;
    }

    /**
     * Set billing_postcode
     *
     * @param string $billingPostcode
     * @return UserOrder
     */
    public function setBillingPostcode($billingPostcode)
    {
        $this->billing_postcode = $billingPostcode;
    
        return $this;
    }

    /**
     * Get billing_postcode
     *
     * @return string 
     */
    public function getBillingPostcode()
    {
        return $this->billing_postcode;
    }

    /**
     * Set billing_country
     *
     * @param string $billingCountry
     * @return UserOrder
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billing_country = $billingCountry;
    
        return $this;
    }

    /**
     * Get billing_country
     *
     * @return string 
     */
    public function getBillingCountry()
    {
        return $this->billing_country;
    }

    /**
     * Set billing_state
     *
     * @param string $billingState
     * @return UserOrder
     */
    public function setBillingState($billingState)
    {
        $this->billing_state = $billingState;
    
        return $this;
    }

    /**
     * Get billing_state
     *
     * @return string 
     */
    public function getBillingState()
    {
        return $this->billing_state;
    }

    /**
     * Set shipping_first_name
     *
     * @param string $shippingFirstName
     * @return UserOrder
     */
    public function setShippingFirstName($shippingFirstName)
    {
        $this->shipping_first_name = $shippingFirstName;
    
        return $this;
    }

    /**
     * Get shipping_first_name
     *
     * @return string 
     */
    public function getShippingFirstName()
    {
        return $this->shipping_first_name;
    }

    /**
     * Set shipping_last_name
     *
     * @param string $shippingLastName
     * @return UserOrder
     */
    public function setShippingLastName($shippingLastName)
    {
        $this->shipping_last_name = $shippingLastName;
    
        return $this;
    }

    /**
     * Get shipping_last_name
     *
     * @return string 
     */
    public function getShippingLastName()
    {
        return $this->shipping_last_name;
    }


    /**
     * Set shipping_address1
     *
     * @param string $shippingAddress1
     * @return UserOrder
     */
    public function setShippingAddress1($shippingAddress1)
    {
        $this->shipping_address1 = $shippingAddress1;
    
        return $this;
    }

    /**
     * Get shipping_address1
     *
     * @return string 
     */
    public function getShippingAddress1()
    {
        return $this->shipping_address1;
    }

    /**
     * Set shipping_address2
     *
     * @param string $shippingAddress2
     * @return UserOrder
     */
    public function setShippingAddress2($shippingAddress2)
    {
        $this->shipping_address2 = $shippingAddress2;
    
        return $this;
    }

    /**
     * Get shipping_address2
     *
     * @return string 
     */
    public function getShippingAddress2()
    {
        return $this->shipping_address2;
    }

	/**
	 * Set shipping_phone
	 *
	 * @param string $shippingPhone
	 * @return UserOrder
	 */
	public function setShippingPhone($shippingPhone)
	{
	  $this->shipping_phone = $shippingPhone;

	  return $this;
	}

	/**
	 * Get shipping_phone
	 *
	 * @return string
	 */
	public function getShippingPhone()
	{
	  return $this->shipping_phone;
	}

  /**
     * Set shipping_city
     *
     * @param string $shippingCity
     * @return UserOrder
     */
    public function setShippingCity($shippingCity)
    {
        $this->shipping_city = $shippingCity;
    
        return $this;
    }

    /**
     * Get shipping_city
     *
     * @return string 
     */
    public function getShippingCity()
    {
        return $this->shipping_city;
    }

    /**
     * Set shipping_postcode
     *
     * @param string $shippingPostcode
     * @return UserOrder
     */
    public function setShippingPostcode($shippingPostcode)
    {
        $this->shipping_postcode = $shippingPostcode;
    
        return $this;
    }

    /**
     * Get shipping_postcode
     *
     * @return string 
     */
    public function getShippingPostcode()
    {
        return $this->shipping_postcode;
    }

    /**
     * Set shipping_country
     *
     * @param string $shippingCountry
     * @return UserOrder
     */
    public function setShippingCountry($shippingCountry)
    {
        $this->shipping_country = $shippingCountry;
    
        return $this;
    }

    /**
     * Get shipping_country
     *
     * @return string 
     */
    public function getShippingCountry()
    {
        return $this->shipping_country;
    }

    /**
     * Set shipping_state
     *
     * @param string $shippingState
     * @return UserOrder
     */
    public function setShippingState($shippingState)
    {
        $this->shipping_state = $shippingState;
    
        return $this;
    }

    /**
     * Get shipping_state
     *
     * @return string 
     */
    public function getShippingState()
    {
        return $this->shipping_state;
    }

    /**
     * Set order_status
     *
     * @param string $orderStatus
     * @return UserOrder
     */
    public function setOrderStatus($orderStatus)
    {
        $this->order_status = $orderStatus;
    
        return $this;
    }

    /**
     * Get order_status
     *
     * @return string 
     */
    public function getOrderStatus()
    {
        return $this->order_status;
    }

	/**
 * Set order_amount
 *
 * @param integer $orderAmount
 * @return OrderAmount
 */
  public function setOrderAmount($orderAmount)
  {
	$this->order_amount = $orderAmount;

	return $this;
  }

  /**
   * Get order_amount
   *
   * @return integer
   */
  public function getOrderAmount()
  {
	return $this->order_amount;
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




    /**
     * Add user_order
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrder
     * @return UserOrder
     */
    public function addUserOrder(\LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail)
    {
        $this->user_order_detail[] = $userOrderDetail;
    
        return $this;
    }

    /**
     * Remove user_order
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrder
     */
    public function removeUserOrder(\LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail)
    {
        $this->user_order_detail->removeElement($userOrderDetail);
    }

    /**
     * Get user_order
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserOrder()
    {
        return $this->user_order_detail;
    }

    /**
     * Set transaction_status
     *
     * @param string $transactionStatus
     * @return UserOrder
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transaction_status = $transactionStatus;
    
        return $this;
    }

    /**
     * Get transaction_status
     *
     * @return string 
     */
    public function getTransactionStatus()
    {
        return $this->transaction_status;
    }

    /**
     * Set transaction_id
     *
     * @param string $transactionId
     * @return UserOrder
     */
    public function setTransactionId($transactionId)
    {
        $this->transaction_id = $transactionId;
    
        return $this;
    }

    /**
     * Get transaction_id
     *
     * @return string 
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * Set payment_method
     *
     * @param string $paymentMethod
     * @return UserOrder
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->payment_method = $paymentMethod;
    
        return $this;
    }

    /**
     * Get payment_method
     *
     * @return string 
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Set payment_json
     *
     * @param string $paymentJson
     * @return UserOrder
     */
    public function setPaymentJson($paymentJson)
    {
        $this->payment_json = $paymentJson;
    
        return $this;
    }

    /**
     * Get payment_json
     *
     * @return string 
     */
    public function getPaymentJson()
    {
        return $this->payment_json;
    }
}