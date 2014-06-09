<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\AdminBundle\Entity\ProductColorView
 *
 * @ORM\Table(name="product_color_view")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductColorViewRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductColorView {    
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_color_view")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     
     */    
    protected $product; 
    
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_color_view")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id", onDelete="CASCADE")
     
     */    
    protected $product_color; 
    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToMany(targetEntity="ProductItemPiece", mappedBy="product_color_view")
     * */
    private $product_item_piece;
    
    
    
    
    
    public function __construct() {
        $this->product_color = new ArrayCollection();
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
     * @var string $image
     *
     * @ORM\Column(name="image", type="string",nullable=true)
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
     * Set title
     *
     * @param string $title
     * @return ProductColorView
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
     * Set image
     *
     * @param string $image
     * @return ProductColorView
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
     * Set product_color
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductColor $productColor
     * @return ProductColorView
     */
    public function setProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColor = null)
    {
        $this->product_color = $productColor;
    
        return $this;
    }

    /**
     * Get product_color
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductColor 
     */
    public function getProductColor()
    {
        return $this->product_color;
    }

    /**
     * Set product
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductColorView
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

    #----------------------------------------------------------------------------
    #-----------------image related --------------------------------
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    
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
//-------------------------------------------------------    
 public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
       $ih=new ImageHelper('product', $this);
        $ih->upload();
    }

    /**
     * Add product_item_piece
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemPiece $productItemPiece
     * @return ProductColorView
     */
    public function addProductItemPiece(\LoveThatFit\AdminBundle\Entity\ProductItemPiece $productItemPiece)
    {
        $this->product_item_piece[] = $productItemPiece;
    
        return $this;
    }

    /**
     * Remove product_item_piece
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemPiece $productItemPiece
     */
    public function removeProductItemPiece(\LoveThatFit\AdminBundle\Entity\ProductItemPiece $productItemPiece)
    {
        $this->product_item_piece->removeElement($productItemPiece);
    }

    /**
     * Get product_item_piece
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductItemPiece()
    {
        return $this->product_item_piece;
    }
}