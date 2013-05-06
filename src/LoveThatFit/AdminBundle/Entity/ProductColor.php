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
class ProductColor
{
    
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
    
    
      public function __construct()
    {
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
     * @var string $color_a
     *
     * @ORM\Column(name="color_a", type="string",nullable=true)
     * @Assert\NotBlank()
     */
    private $color_a;

    /**
     * @var string $color_b
     *
     * @ORM\Column(name="color_b", type="string",nullable=true)
     */
    private $color_b;

    /**
     * @var string $color_c
     *
     * @ORM\Column(name="color_c", type="string",nullable=true)
     */
    private $color_c;

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
     * @var string $displayProductColor
     *
     */
    public $displayProductColor;

    
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
     * Set color_a
     *
     * @param string $colorA
     * @return ProductColor
     */
    public function setColorA($colorA)
    {
        $this->color_a = $colorA;
    
        return $this;
    }

    /**
     * Get color_a
     *
     * @return string 
     */
    public function getColorA()
    {
        return $this->color_a;
    }

    /**
     * Set color_b
     *
     * @param string $colorB
     * @return ProductColor
     */
    public function setColorB($colorB)
    {
        $this->color_b = $colorB;
    
        return $this;
    }

    /**
     * Get color_b
     *
     * @return string 
     */
    public function getColorB()
    {
        return $this->color_b;
    }

    /**
     * Set color_c
     *
     * @param string $colorC
     * @return ProductColor
     */
    public function setColorC($colorC)
    {
        $this->color_c = $colorC;
    
        return $this;
    }

    /**
     * Get colorC
     *
     * @return string 
     */
    public function getColorC()
    {
        return $this->color_c;
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
    //---------------------------------------
    
    public function getSizeTitleArray(){
        $items = $this->product_items;
        $size_titles=array();
        foreach($items as $i){
            array_push($size_titles, $i->getProductSize()->getTitle());
        }
        return $size_titles;
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
      $ih=new ImageHelper('product',$this);
   $ih->upload();
     
    }
 //------------------------------------------------------------
   public function uploadPattern() {             
      $pattern_file_name=$this->getAbsolutePatternTempPath();
      $dest=$this->getUploadRootDir().'/pattern/'. $this->pattern;
      rename($pattern_file_name,$dest);
    } 
    
 //------------------------------------------------------------
   public function uploadImage() {   
   $ih=new ImageHelper('product',$this);
   $ih->uploadTempImage();
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

//-------------------------------------------------------
//-------------------------------------------------------
    
    public function getAbsoluteTempPath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/temp/' . $this->image;
    }
    //-------------------------------------------------------
    public function getAbsolutePatternTempPath() {
        return null === $this->pattern ? null : $this->getUploadRootDir() . '/temp/' . $this->pattern;
    }
//-------------------------------------------------------
    public function getAbsolutePatternPath() {
        return null === $this->pattern ? null : $this->getUploadRootDir() . '/' . $this->pattern;
    }    

    

//------------------------------------------------
    
     public function uploadTemporaryImage() {
         
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $temp_image =  $this->product->getId() .'_'. uniqid() .'.'. $ext;        
        
        $this->file->move(
                $this->getUploadRootDir().'/temp/', $temp_image
        );
        
        $this->file = null;             
        return $this->getUploadDir() . '/temp/' . $temp_image;
    }
    
//---------------------------------------------------------------
    
    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return ProductColor
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items[] = $productItems;
    
        return $this;
    }
//---------------------------------------------------------------
    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items->removeElement($productItems);
    }
//---------------------------------------------------------------
    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems()
    {
        return $this->product_items;
    }
//---------------------------------------------------------------    
    
 /**
 * @ORM\postRemove
 */
    
public function deleteImages()
{
    if($this->image)
    {    
    $ih=new ImageHelper('product', $this);
    $ih->deleteImages($this->image);
  }
} 


}