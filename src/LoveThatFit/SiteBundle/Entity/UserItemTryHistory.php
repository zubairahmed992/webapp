<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\SiteBundle\Entity\UserItemTryHistory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LoveThatFit\SiteBundle\Entity\UserItemTryHistoryRepository")
 */
class UserItemTryHistory
{    
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\Product", inversedBy="user_ittem_try_history")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="user_ittem_try_history")
     * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productitem;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_ittem_try_history")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    
    
     public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->productitem = new ArrayCollection();
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
     * @var int $count
     *
     * @ORM\Column(name="count", type="integer", length=11)
     */
    private $count;

    /**
     * @var boolean $fit
     *
     * @ORM\Column(name="fit", type="boolean",nullable=true)
     */
    private $fit;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @var string $feedback
     *
     * @ORM\Column(name="feedback", type="text",nullable=true)
     */
    private $feedback;
    
    
    /**
     * @var string $fit_index
     *
     * @ORM\Column(name="fit_index", type="text",nullable=true)
     */
    private $fit_index;
    
    
    /**
     * @var string $recommended_size
     *
     * @ORM\Column(name="recommended_size", type="string", length=40,nullable=true)
     */
    private $recommended_size;
    
    
    /**
     * @var string $recommended_fit
     *
     * @ORM\Column(name="recommended_fit", type="string", length=40,nullable=true)
     */
    private $recommended_fit;
    
    
    /**
     * @var string $recommended_index
     *
     * @ORM\Column(name="recommended_index", type="string", length=40,nullable=true)
     */
    private $recommended_index;


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
     * Set count
     *
     * @param int $count
     * @return UserItemTryHistory
     */
    public function setCount($count)
    {
        $this->count = $count;
    
        return $this;
    }

    /**
     * Get count
     *
     * @return int 
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set fit
     *
     * @param boolean $fit
     * @return UserItemTryHistory
     */
    public function setFit($fit)
    {
        $this->fit = $fit;
    
        return $this;
    }

    /**
     * Get fit
     *
     * @return boolean 
     */
    public function getFit()
    {
        return $this->fit;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return UserItemTryHistory
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return UserItemTryHistory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set feedback
     *
     * @param string $feedback
     * @return UserItemTryHistory
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    
        return $this;
    }

    /**
     * Get feedback
     *
     * @return string 
     */
    public function getFeedback()
    {
        return $this->feedback;
    }
   

    /**
     * Set productitem
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productitem
     * @return UserItemTryHistory
     */
    public function setProductitem(\LoveThatFit\AdminBundle\Entity\ProductItem $productitem = null)
    {
        $this->productitem = $productitem;
    
        return $this;
    }

    /**
     * Get productitem
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getProductitem()
    {
        return $this->productitem;
    }

    /**
     * Set user
     *
     * @param LoveThatFit\UserBundle\Entity\User $user
     * @return UserItemTryHistory
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set product
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $product
     * @return UserItemTryHistory
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return \LoveThatFit\AdminBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set fit_index
     *
     * @param string $fitIndex
     * @return UserItemTryHistory
     */
    public function setFitIndex($fitIndex)
    {
        $this->fit_index = $fitIndex;
    
        return $this;
    }

    /**
     * Get fit_index
     *
     * @return string 
     */
    public function getFitIndex()
    {
        return $this->fit_index;
    }

    /**
     * Set recommended_size
     *
     * @param string $recommendedSize
     * @return UserItemTryHistory
     */
    public function setRecommendedSize($recommendedSize)
    {
        $this->recommended_size = $recommendedSize;
    
        return $this;
    }

    /**
     * Get recommended_size
     *
     * @return string 
     */
    public function getRecommendedSize()
    {
        return $this->recommended_size;
    }

    /**
     * Set recommended_fit
     *
     * @param string $recommendedFit
     * @return UserItemTryHistory
     */
    public function setRecommendedFit($recommendedFit)
    {
        $this->recommended_fit = $recommendedFit;
    
        return $this;
    }

    /**
     * Get recommended_fit
     *
     * @return string 
     */
    public function getRecommendedFit()
    {
        return $this->recommended_fit;
    }

    /**
     * Set recommended_index
     *
     * @param string $recommendedIndex
     * @return UserItemTryHistory
     */
    public function setRecommendedIndex($recommendedIndex)
    {
        $this->recommended_index = $recommendedIndex;
    
        return $this;
    }

    /**
     * Get recommended_index
     *
     * @return string 
     */
    public function getRecommendedIndex()
    {
        return $this->recommended_index;
    }
}