<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id")
     
      */    
    protected $product_size; 
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id")
     
     */    
    protected $product_color; 
    
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
     * @var string $line_number
     *
     * @ORM\Column(name="line_number", type="string", nullable=true)
     */
    private $line_number;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
   
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

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
     * Set line_number
     *
     * @param string $lineNumber
     * @return ProductItem
     */
    public function setLineNumber($lineNumber)
    {
        $this->line_number = $lineNumber;
    
        return $this;
    }

    /**
     * Get line_number
     *
     * @return string 
     */
    public function getLineNumber()
    {
        return $this->line_number;
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
     * Set product_size
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSize
     * @return ProductItem
     */
    public function setProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSize = null)
    {
        $this->product_size = $productSize;
    
        return $this;
    }

    /**
     * Get product_size
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductSize 
     */
    public function getProductSize()
    {
        return $this->product_size;
    }

    /**
     * Set product_color
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColor
     * @return ProductItem
     */
    public function setProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColor = null)
    {
        $this->product_color = $productColor;
    
        return $this;
    }

    /**
     * Get product_color
     *
     * @return LoveTha  tFit\AdminBundle\Entity\ProductColor 
     */
    public function getProductColor()
    {
        return $this->product_color;
    }
    //---------------------------------------------------
    
     public function upload() {
        
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        $this->image = uniqid() .'_fr.'. $ext;        
        $this->file->move(
                $this->getUploadRootDir(), $this->image
        );
        
        $this->file = null;          
        
        
         
    }
    
    //-------------------------------------------------------
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }
//-------------------------------------------------------
    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }
//-------------------------------------------------------
    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
//-------------------------------------------------------
    protected function getUploadDir() {
       return 'uploads/ltf/products/fitting_room';            
    }

 
}