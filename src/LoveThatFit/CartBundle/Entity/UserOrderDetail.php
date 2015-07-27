<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserOrderDetail
 *
 * @ORM\Table("user_order_detail")
 * @ORM\Entity(repositoryClass="LoveThatFit\CartBundle\Entity\UserOrderDetailRepository")
 */
class UserOrderDetail
{

  /**
   * @ORM\ManyToOne(targetEntity="LoveThatFit\CartBundle\Entity\UserOrder", inversedBy="user_order_detail")
   * @ORM\JoinColumn(name="user_order_id", referencedColumnName="id", onDelete="CASCADE")
   */
    protected $user_order;

	/**
	 * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="user_order_detail")
	 * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $product_item;

	public function __construct()
	{
	  $this->user_order = new ArrayCollection();
	  $this->product_item = new ArrayCollection();
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
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="item_description", type="string")
	 */
	private $item_description;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="sku", type="string", nullable=true)
	 */
	private $sku;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;


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
     * Set qty
     *
     * @param integer $qty
     * @return UserOrderDetail
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    
        return $this;
    }

    /**
     * Get qty
     *
     * @return integer 
     */
    public function getQty()
    {
        return $this->qty;
    }

	/**
	 * Set item_description
	 *
	 * @param string $item_description
	 * @return UserOrderDetail
	 */
	public function setItemDescription($item_description)
	{
	  $this->item_description = $item_description;

	  return $this;
	}

	/**
	 * Get qty
	 *
	 * @return integer
	 */
	public function getItemDescription()
	{
	  return $this->item_description;
	}

    /**
     * Set amount
     *
     * @param integer $amount
     * @return UserOrderDetail
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return UserOrderDetail
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }


	/**
	 * Set product_item
	 *
	 * @param \LoveThatFit\AdminBundle\Entity\ProductItem $productItem
	 * @return Cart
	 */
	public function setProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItem = null)
	{
	  $this->product_item = $productItem;

	  return $this;
	}

	/**
	 * Get product_item
	 *
	 * @return \LoveThatFit\AdminBundle\Entity\ProductItem
	 */
	public function getProductItem()
	{
	  return $this->product_item;
	}

	/**
	 * Set UserOrder
	 *
	 * @param \LoveThatFit\UserBundle\Entity\UserOrder $user_order
	 * @return UserOrder
	 */
	public function setUserOrder(\LoveThatFit\CartBundle\Entity\UserOrder $user_order = null)
	{
	  $this->user_order = $user_order;

	  return $this;
	}

	/**
	 * Get user
	 *
	 * @return \LoveThatFit\CartBundle\Entity\UserOrder
	 */
	public function getUserOrder()
	{
	  return $this->user_order;
	}

    /**
     * Set sku
     *
     * @param string $sku
     * @return UserOrderDetail
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    
        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }
}