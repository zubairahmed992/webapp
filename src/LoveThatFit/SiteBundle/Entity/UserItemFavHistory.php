<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\SiteBundle\Entity\UserItemFavHistory
 *
 * @ORM\Table(name="user_product_item_fav_history")
 * @ORM\Entity(repositoryClass="LoveThatFit\SiteBundle\Entity\UserItemFavHistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserItemFavHistory
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\Product", inversedBy="user_item_fav_history")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="user_item_fav_history")
     * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productitem;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_item_fav_history")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var int $status
     *
     * @ORM\Column(name="status", type="integer", length=11)
     */
    private $status;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $page;


    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->productitem = new ArrayCollection();
        $this->user = new ArrayCollection();
    }



    /**
     * Set status
     *
     * @param integer $status
     * @return UserItemFavHistory
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return UserItemFavHistory
     */
  /*  public function setCreatedAt(\dateTime $createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }*/

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
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
     * Set product
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $product
     * @return UserItemFavHistory
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
     * Set productitem
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItem $productitem
     * @return UserItemFavHistory
     */
    public function setProductitem(\LoveThatFit\AdminBundle\Entity\ProductItem $productitem = null)
    {
        $this->productitem = $productitem;
    
        return $this;
    }

    /**
     * Get productitem
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getProductitem()
    {
        return $this->productitem;
    }

    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserItemFavHistory
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set page
     *
     * @param string $page
     * @return UserItemFavHistory
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }
}