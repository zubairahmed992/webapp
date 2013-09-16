<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\AdminBundle\Entity\ProductColor
 *
 * @ORM\Table(name="product_color")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductColorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductColor {

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_colors")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="ProductItem", mappedBy="product_color", orphanRemoval=true)
     */
    protected $product_items;

    public function __construct() {
        $this->product_items = new ArrayCollection();
    }

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
    //---------------------- Public Variables -----------------------------------------
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @var string $tempImage
     * @Assert\Blank()
     */
    public $tempImage;

    /**
     * @var string $tempPattern
     * @Assert\Blank()
     */
    public $tempPattern;

    /**
     * @var string $displayProductColor
     */
    public $displayProductColor;

//---------------------------------------------------------------

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

//---------------------------------------------------------------
    /**
     * Set title
     *
     * @param string $title
     * @return ProductColor
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }
//----------------------------------------------------------------------------------
    /**
     * Set pattern
     *
     * @param string $pattern
     * @return ProductColor
     */
    public function setPattern($pattern) {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern() {
        return $this->pattern;
    }

//---------------------------------------------------------------
    /**
     * Set image
     *
     * @param string $image
     * @return ProductColor
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

//---------------------------------------------------------------
    /**
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductColor
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return LoveThatFit\AdminBundle\Entity\Product 
     */
    public function getProduct() {
        return $this->product;
    }

    //---------------------------------------------------------------

    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return ProductColor
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items[] = $productItems;

        return $this;
    }

    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items->removeElement($productItems);
    }

    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems() {
        return $this->product_items;
    }

    //---------------------------------------------------------------    
    //------------ Facilitating sizes ---------

    private $sizes;

    public function getSizes() {
        return $this->sizes;
    }

    public function setSizes($sizes) {
        $this->sizes = $sizes;
        return $this;
    }

//---------------------------------------------------------------    


    public function getSizeTitleArray() {
        $items = $this->product_items;
        $size_titles = array();
        foreach ($items as $i) {
            //$size_titles[$i->getProductSize()->getTitle()] = $i->getProductSize()->getId();
            $size_titles[$i->getProductSize()->getId()] = $i->getProductSize()->getTitle();
            //changed due to issue in size selection
        }
        asort($size_titles);
        return $size_titles;
    }

//-------------------- Product Display image -------------------------------------------    
    
    public function upload() {
        $ih = new ImageHelper('product', $this);
        $ih->uploadProductColorImage(); // save & resize images 
    }
   
//------------------------------------------------------------
    public function saveImage() {
        if ($this->tempImage) {
            $ih = new ImageHelper('product', $this);
            $ih->uploadProductColorImage(); // save & resize images 
        }
    }

//-------------------------------------------------------1
    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/web/' . $this->image;
    }
    
//-------------------------------------------------------2
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/web/' . $this->image;
    }

//-------------------------------------------------------3
    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

//-------------------------------------------------------4
    protected function getUploadDir() {        
        return 'uploads/ltf/products/display';
    }

//--------------------------------------------------------5

    public function getImagePaths() {
        $ih = new ImageHelper('product', $this);
        return $ih->getImagePaths();
    }
 //-------------------------------------------------------6

    public function getAbsoluteTempPath() {
        return null === $this->tempImage ? null : $this->getUploadRootDir() . '/temp/' . $this->tempImage;
    }
   
    
//-------------------- Pattern image -------------------------------------------        

    public function savePattern() {
        if ($this->tempPattern) {
            $ih = new ImageHelper('product_pattern', $this);
            $ih->uploadProductPatternImage();
        }
    }
//-------------------------------------------------------1
    public function getPatternWebPath() {        
        return null === $this->pattern ? null : $this->getPatternUploadDir()  . '/web/' .  $this->pattern;
    }

//-------------------------------------------------------2
    public function getAbsolutePatternPath() {
        return null === $this->pattern ? null : $this->getPatternUploadRootDir()  . '/web/' . $this->pattern;
    }

//-------------------------------------------------------3
    public function getPatternUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getPatternUploadDir();
    }
    
//-------------------------------------------------------4
    protected function getPatternUploadDir() {        
        return 'uploads/ltf/products/pattern';
    }

//-------------------------------------------------------5

    public function getPatternPaths() {
        $ih = new ImageHelper('product_pattern', $this);
        return $ih->getImagePaths();
    }
//-------------------------------------------------------6
    public function getAbsolutePatternTempPath() {
        return null === $this->tempPattern ? null : $this->getUploadDir() . '/temp/' . $this->tempPattern;
    }

//------------------------------------------------

    public function uploadTemporaryImage() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $temp_image = $this->product->getId() . '_' . uniqid() . '.' . $ext;

        $this->file->move(
                $this->getUploadRootDir() . '/temp/', $temp_image
        );

        $this->file = null;
        return array('image_url'=>$this->getUploadDir() . '/temp/' . $temp_image,'image_name'=>$temp_image);
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages() {
        if ($this->image) {
            $ih = new ImageHelper('product', $this);
            $ih->deleteImages($this->image);
        }
    }

}