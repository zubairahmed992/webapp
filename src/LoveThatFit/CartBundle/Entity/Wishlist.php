<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Cart
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LoveThatFit\CartBundle\Entity\WishlistRepository")
 */
class Wishlist
{

	/**
	 * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="wishlist")
	 * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $product_item;

	/**
	 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="wishlist")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	public function __construct()
	{
	  $this->product_item = new ArrayCollection();
	  $this->user = new ArrayCollection();
	}


	/**
	 * @var integer $id
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="datetime", type="datetime")
	 */
	private $datetime;



	/**
	 * Set qty
	 *
	 * @param integer $qty
	 * @return Cart
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
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
	  return $this->id;
	}

	/**
	 * Set id
	 *
	 * @param integer $id
	 * @return Cart
	 */
	public function setId($id)
	{
	  $this->id = $id;

	  return $this;
	}

	/**
	 * Set datetime
	 *
	 * @param \DateTime $datetime
	 * @return Cart
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
}