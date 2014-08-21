<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\AdminBundle\ImageHelper;

/** 
 * @ORM\Table("retailer_order_track")
 * @ORM\Entity(repositoryClass="LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrackRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RetailerOrderTrack
{
    
    
    /**
     * @ORM\OneToMany(targetEntity="RetailerOrderItemTrack", mappedBy="retailer_order_track")
     */
    protected $retailer_order_item_track;
    
    
     /** 
    * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\Retailer", inversedBy="retailer_order_track")
    *@ORM\JoinColumn(name="retailer_id", onDelete="CASCADE", referencedColumnName="id")
    */
    protected $retailer;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="retailer_order_track")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    
    
     public function __construct() {        
        $this->retailer = new ArrayCollection();
        $this->user = new ArrayCollection();
        
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
     * @var string $order_number
     *
     * @ORM\Column(name="order_number", type="string", length=255)
     */
    private $order_number;

    /**
     * @var string $order_status
     *
     * @ORM\Column(name="order_status", type="string", length=255)
     */
    private $order_status;

     /**
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;
     
    /**
     * @var string $cart_token
     *
     * @ORM\Column(name="cart_token", type="string", length=255)
     */
    private $cart_token;   
    
   
    
    /**
     * @var dateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    
    private $createdAt;
    
    /**
     * @var dateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    
    /**
     * @var dateTime $closed_at
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closed_at;
   

    

    

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
     * Set order_number
     *
     * @param string $orderNumber
     * @return RetailerOrderTrack
     */
    public function setOrderNumber($orderNumber)
    {
        $this->order_number = $orderNumber;
    
        return $this;
    }

    /**
     * Get order_number
     *
     * @return string 
     */
    public function getOrderNumber()
    {
        return $this->order_number;
    }

    /**
     * Set order_status
     *
     * @param string $orderStatus
     * @return RetailerOrderTrack
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
     * Set token
     *
     * @param string $token
     * @return RetailerOrderTrack
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set cart_token
     *
     * @param string $cartToken
     * @return RetailerOrderTrack
     */
    public function setCartToken($cartToken)
    {
        $this->cart_token = $cartToken;
    
        return $this;
    }

    /**
     * Get cart_token
     *
     * @return string 
     */
    public function getCartToken()
    {
        return $this->cart_token;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return RetailerOrderTrack
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return RetailerOrderTrack
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set closed_at
     *
     * @param \DateTime $closedAt
     * @return RetailerOrderTrack
     */
    public function setClosedAt($closedAt)
    {
        $this->closed_at = $closedAt;
    
        return $this;
    }

    /**
     * Get closed_at
     *
     * @return \DateTime 
     */
    public function getClosedAt()
    {
        return $this->closed_at;
    }

    

    

    /**
     * Add retailer_order_item_track
     *
     * @param \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack $retailerOrderItemTrack
     * @return RetailerOrderTrack
     */
    public function addRetailerOrderItemTrack(\LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack $retailerOrderItemTrack)
    {
        $this->retailer_order_item_track[] = $retailerOrderItemTrack;
    
        return $this;
    }

    /**
     * Remove retailer_order_item_track
     *
     * @param \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack $retailerOrderItemTrack
     */
    public function removeRetailerOrderItemTrack(\LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrack $retailerOrderItemTrack)
    {
        $this->retailer_order_item_track->removeElement($retailerOrderItemTrack);
    }

    /**
     * Get retailer_order_item_track
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailerOrderItemTrack()
    {
        return $this->retailer_order_item_track;
    }

    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return RetailerOrderTrack
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
     * Set retailer
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailer
     * @return RetailerOrderTrack
     */
    public function setRetailer(\LoveThatFit\AdminBundle\Entity\Retailer $retailer = null)
    {
        $this->retailer = $retailer;
    
        return $this;
    }

    /**
     * Get retailer
     *
     * @return \LoveThatFit\AdminBundle\Entity\Retailer 
     */
    public function getRetailer()
    {
        return $this->retailer;
    }
}