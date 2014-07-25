<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\SiteBundle\Entity\UserFittingRoomItem
 *
 * @ORM\Table(name="users_fitting_room_items")
 * @ORM\Entity(repositoryClass="LoveThatFit\SiteBundle\Entity\UserFittingRoomItemRepository")
 */
class UserFittingRoomItem
{    
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="user_fitting_room_ittem")
     * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productitem;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_fitting_room_ittem")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    
    
     public function __construct()
    {
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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

   
}