<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoveThatFit\AdminBundle\Entity\ProductItem
 *
 * @ORM\Table(name="product_item")
 * @ORM\Entity
 */
class ProductItem
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */    
    protected $product; 
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductSize", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id")
     */    
    protected $productSize; 
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id")
     */    
    protected $productColor; 
    
    /////////////////////////////////////////////////////////////
    
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $lineNumber
     *
     * @ORM\Column(name="line_number", type="string", nullable=true)
     */
    private $lineNumber;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;


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
     * Set lineNumber
     *
     * @param string $lineNumber
     * @return ProductItem
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
    
        return $this;
    }

    /**
     * Get lineNumber
     *
     * @return string 
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return ProductItem
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductItem
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return LoveThatFit\AdminBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set productSize
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSize
     * @return ProductItem
     */
    public function setProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSize = null)
    {
        $this->productSize = $productSize;
    
        return $this;
    }

    /**
     * Get productSize
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductSize 
     */
    public function getProductSize()
    {
        return $this->productSize;
    }

    /**
     * Set productColor
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColor
     * @return ProductItem
     */
    public function setProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColor = null)
    {
        $this->productColor = $productColor;
    
        return $this;
    }

    /**
     * Get productColor
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductColor 
     */
    public function getProductColor()
    {
        return $this->productColor;
    }
}