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
    
    /**
   * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItemPiece", inversedBy="user_fitting_room_ittem")
   * @ORM\JoinColumn(name="product_item_piece_id", referencedColumnName="id", onDelete="CASCADE")
   */
    protected $product_item_piece;


    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\Product", inversedBy="user_fitting_room_ittem")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_id;


    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;
    
    #---------------------------------------------------
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
    public function getUpdatedAt(){
        return $this->updated_at;
    }


    /**
     * Set qty
     *
     * @param integer $qty
     * @return User Fitting Room
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
 #---------------------------------------------------
    /**
     * Set product_item_piece
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece
     * @return UserFittingRoomItem
     */
    public function setProductItemPiece(\LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece = null){
        $this->product_item_piece = $product_item_piece;    
        return $this;
    }

    /**
     * Get product_item_piece
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductItemPiece 
     */
    public function getProductItemPiece(){
        return $this->product_item_piece;
    }

    #---------------------------------------------------
    /**
     * Set product_id
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product_id
     * @return UserFittingRoomItem
     */
    public function setProductId(\LoveThatFit\AdminBundle\Entity\Product $product_id = null){
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * Get product_id
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductId
     */
    public function getProductId(){
        return $this->product_id;
    }
#------------------------------------------------   
   

    /**
     * Set productitem
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productitem
     * @return UserFittingRoomItem
     */
    public function setProductitem(\LoveThatFit\AdminBundle\Entity\ProductItem $productitem = null){
        $this->productitem = $productitem;
    
        return $this;
    }

    /**
     * Get productitem
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getProductitem(){
        return $this->productitem;
    }
#---------------------------------------------------
    /**
     * Set user
     *
     * @param LoveThatFit\UserBundle\Entity\User $user
     * @return UserFittingRoomItem
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null){
        $this->user = $user;    
        return $this;
    }

    /**
     * Get user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser(){
        return $this->user;
    }

   
}