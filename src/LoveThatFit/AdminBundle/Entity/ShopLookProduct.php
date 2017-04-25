<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopLookProduct
 *
 * @ORM\Table("shop_look_product")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ShopLookProductRepository")
 */
class ShopLookProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ShopLook", inversedBy="shop_look_product")
     * @ORM\JoinColumn(name="shop_look_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $shoplook;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $product_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sorting;


    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

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
     * Set sorting
     *
     * @param integer $sorting
     * @return ShopLookProduct
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    
        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer 
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ShopLookProduct
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
     * @return ShopLookProduct
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
     * Set shoplook
     *
     * @param \LoveThatFit\AdminBundle\Entity\ShopLook $shoplook
     * @return ShopLookProduct
     */
    public function setShoplook(\LoveThatFit\AdminBundle\Entity\ShopLook $shoplook = null)
    {
        $this->shoplook = $shoplook;
    
        return $this;
    }

    /**
     * Get shoplook
     *
     * @return \LoveThatFit\AdminBundle\Entity\ShopLook 
     */
    public function getShoplook()
    {
        return $this->shoplook;
    }


    /**
     * Set product_id
     *
     * @param integer $productId
     * @return ShopLookProduct
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;
    
        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }
}