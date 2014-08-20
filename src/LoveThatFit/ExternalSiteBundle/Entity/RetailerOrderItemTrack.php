<?php

namespace LoveThatFit\ExternalSiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\AdminBundle\ImageHelper;

/** 
 * @ORM\Table("retailer_order_item_track")
 * @ORM\Entity(repositoryClass="LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderItemTrackRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RetailerOrderItemTrack
{
    
    /**
     * @ORM\ManyToOne(targetEntity="RetailerOrderTrack", inversedBy="retailer_order_item_track")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $retailer_order_track;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="retailer_order_item_track")
     * @ORM\JoinColumn(name="product_Item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_items;
    
    
     public function __construct() {        
        $this->retailer_order_track = new ArrayCollection();
        $this->product_items = new ArrayCollection();
        
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
     * @var string $recommended_fit_size
     *
     * @ORM\Column(name="recommended_fit_size", type="string", length=255)
     */
    private $recommended_fit_size;

    
    /**
     * @var string $recommended_fit_index
     *
     * @ORM\Column(name="recommended_fit_index", type="string", length=255)
     */
    private $recommended_fit_index;
   
    
     /**
     * @var float $purchased_fit_index
     *
     * @ORM\Column(name="purchased_fit_index", type="float", length=255)
     */
    private $purchased_fit_index;
    
    /**
     * @var string $purchased_fit_size
     *
     * @ORM\Column(name="purchased_fit_size", type="string", length=255)
     */
    private $purchased_fit_size;
    
    
    /**
     * @var boolean $tried_on
     *
     * @ORM\Column(name="tried_on", type="boolean", length=255)
     */
    private $tried_on;
    
 /**
     * @var string $sku
     *
     * @ORM\Column(name="sku", type="string", length=255)
     */
    private $sku;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set recommended_fit_size
     *
     * @param string $recommendedFitSize
     * @return RetailerOrderItemTrack
     */
    public function setRecommendedFitSize($recommendedFitSize)
    {
        $this->recommended_fit_size = $recommendedFitSize;
    
        return $this;
    }

    /**
     * Get recommended_fit_size
     *
     * @return string 
     */
    public function getRecommendedFitSize()
    {
        return $this->recommended_fit_size;
    }

    /**
     * Set recommended_fit_index
     *
     * @param string $recommendedFitIndex
     * @return RetailerOrderItemTrack
     */
    public function setRecommendedFitIndex($recommendedFitIndex)
    {
        $this->recommended_fit_index = $recommendedFitIndex;
    
        return $this;
    }

    /**
     * Get recommended_fit_index
     *
     * @return string 
     */
    public function getRecommendedFitIndex()
    {
        return $this->recommended_fit_index;
    }

    /**
     * Set purchased_fit_index
     *
     * @param float $purchasedFitIndex
     * @return RetailerOrderItemTrack
     */
    public function setPurchasedFitIndex($purchasedFitIndex)
    {
        $this->purchased_fit_index = $purchasedFitIndex;
    
        return $this;
    }

    /**
     * Get purchased_fit_index
     *
     * @return float 
     */
    public function getPurchasedFitIndex()
    {
        return $this->purchased_fit_index;
    }

    /**
     * Set purchased_fit_size
     *
     * @param string $purchasedFitSize
     * @return RetailerOrderItemTrack
     */
    public function setPurchasedFitSize($purchasedFitSize)
    {
        $this->purchased_fit_size = $purchasedFitSize;
    
        return $this;
    }

    /**
     * Get purchased_fit_size
     *
     * @return string 
     */
    public function getPurchasedFitSize()
    {
        return $this->purchased_fit_size;
    }

    /**
     * Set tried_on
     *
     * @param boolean $triedOn
     * @return RetailerOrderItemTrack
     */
    public function setTriedOn($triedOn)
    {
        $this->tried_on = $triedOn;
    
        return $this;
    }

    /**
     * Get tried_on
     *
     * @return boolean 
     */
    public function getTriedOn()
    {
        return $this->tried_on;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return RetailerOrderItemTrack
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return RetailerOrderItemTrack
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
     * @return RetailerOrderItemTrack
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
     * Set retailer_order_track
     *
     * @param \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack
     * @return RetailerOrderItemTrack
     */
    public function setRetailerOrderTrack(\LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack = null)
    {
        $this->retailer_order_track = $retailerOrderTrack;
    
        return $this;
    }

    /**
     * Get retailer_order_track
     *
     * @return \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack 
     */
    public function getRetailerOrderTrack()
    {
        return $this->retailer_order_track;
    }

    

    /**
     * Set product_items
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return RetailerOrderItemTrack
     */
    public function setProductItems(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems = null)
    {
        $this->product_items = $productItems;
    
        return $this;
    }

    /**
     * Get product_items
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getProductItems()
    {
        return $this->product_items;
    }
}