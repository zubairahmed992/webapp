<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * LoveThatFit\AdminBundle\Entity\ProductColor
 *
 * @ORM\Table(name="product_color")
 * @ORM\Entity
 */
class ProductColor
{
    
     /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_colors")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;  
    
    
    /////////////////////////////////////////////////////////////////////////
    
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string $colorA
     *
     * @ORM\Column(name="color_a", type="string",nullable=true)
     */
    private $colorA;

    /**
     * @var string $colorB
     *
     * @ORM\Column(name="color_b", type="string",nullable=true)
     */
    private $colorB;

    /**
     * @var string $colorC
     *
     * @ORM\Column(name="color_c", type="string",nullable=true)
     */
    private $colorC;

    /**
     * @var string $pattern
     *
     * @ORM\Column(name="pattern", type="string",nullable=true)
     */
    private $pattern;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string",nullable=true)
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
     * Set title
     *
     * @param string $title
     * @return ProductColor
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set colorA
     *
     * @param string $colorA
     * @return ProductColor
     */
    public function setColorA($colorA)
    {
        $this->colorA = $colorA;
    
        return $this;
    }

    /**
     * Get colorA
     *
     * @return string 
     */
    public function getColorA()
    {
        return $this->colorA;
    }

    /**
     * Set colorB
     *
     * @param string $colorB
     * @return ProductColor
     */
    public function setColorB($colorB)
    {
        $this->colorB = $colorB;
    
        return $this;
    }

    /**
     * Get colorB
     *
     * @return string 
     */
    public function getColorB()
    {
        return $this->colorB;
    }

    /**
     * Set colorC
     *
     * @param string $colorC
     * @return ProductColor
     */
    public function setColorC($colorC)
    {
        $this->colorC = $colorC;
    
        return $this;
    }

    /**
     * Get colorC
     *
     * @return string 
     */
    public function getColorC()
    {
        return $this->colorC;
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     * @return ProductColor
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    
        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return ProductColor
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
     * @return ProductColor
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
    //------------ facilitating sizes ---------
    private $sizes;
   public function getSizes()
    {
        return $this->sizes;
    }
     public function setSizes($sizes)
    {
        $this->sizes = $sizes;    
        return $this;
    }
       //-------------------------------------------------
    //-------------- Image Upload ---------------------


    public function upload() {
        if (null === $this->file) {
            return;
        }        
        $ih=new ImageHelper('product', $this);
        $ih->upload();
    }

 //------------------------------------------------------------

public function getImagePaths() {
    $ih=new ImageHelper('product', $this);
    return $ih->getImagePaths();        
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
       return 'uploads/ltf/products';            
    }

    //---------------------------------------------------
    
    
    
    
}