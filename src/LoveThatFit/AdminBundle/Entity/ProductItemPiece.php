<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\AdminBundle\Entity\ProductItemPiece
 * @ORM\Table(name="product_item_piece")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductItemPieceRepository")
 */
class ProductItemPiece
{
   
     /**     
     * Bidirectional (OWNING SIDE - FK)
     *  
     * @ORM\ManyToOne(targetEntity="ProductItem", inversedBy="productitemtwopieces")    
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
      */
    protected $productitem;      
   
    /**     
     * Bidirectional (OWNING SIDE - FK)
     *  
     * @ORM\ManyToOne(targetEntity="ProductColorView", inversedBy="product_item_piece")    
     * @ORM\JoinColumn(name="product_color_view_id", referencedColumnName="id", onDelete="CASCADE")
      */
    protected $product_color_view;   
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string  $piece_type  
     * @ORM\Column(name="piece_type", type="string", length=255,nullable=true)
     */
    protected $piece_type;

    
    
    /**
     * @var string $image 
     * @ORM\Column(name="image", type="string",length=255, nullable=true)
     */
    protected $image;

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
     * Set piece_type
     *
     * @param string $pieceType
     * @return ProductItemPiece
     */
    public function setPieceType($pieceType)
    {
        $this->piece_type = $pieceType;
    
        return $this;
    }

    /**
     * Get piece_type
     *
     * @return string 
     */
    public function getPieceType()
    {
        return $this->piece_type;
    }   

    /**
     * Set productitem
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItem $productitem
     * @return ProductItemPiece
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
     * Set image
     *
     * @param string $image
     * @return ProductItemPiece
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
    
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
       $ih=new ImageHelper('product_item', $this);
        $ih->upload();
    }
//---------------------------------------------------
    
  public function getAbsolutePath()
    {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }
//---------------------------------------------------
    public function getWebPath()
    {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }
//---------------------------------------------------
    protected function getUploadRootDir()
    {
        return $this->getRootDir() . $this->getUploadDir();
    }
//-------------------------------------------------------4
    protected function getRootDir() {
        return __DIR__ . '/../../../../web/';
    }

//-------------------------------------------------------5
    protected function getUploadDir() {        
       return 'uploads/ltf/products/fitting_room/web';            
    }

    //---------------------------------------------------
       public function getImagePaths() {
        $ih = new ImageHelper('product_item', $this);        
        return $ih->getImagePaths();
    }
    
 /**
 * @ORM\PostRemove
 */
public function deleteImages()
{
    if($this->image)
    {
        $generated_file_name=$this->getUploadRootDir(). '/' . $this->image;
        if (is_readable($generated_file_name )){
                @unlink($generated_file_name );    
            }
    
  }
}

    
    /**
     * Set product_color_view
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView
     * @return ProductItemPiece
     */
    public function setProductColorView(\LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView = null)
    {
        $this->product_color_view = $productColorView;
    
        return $this;
    }

    /**
     * Get product_color_view
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductColorView 
     */
    public function getProductColorView()
    {
        return $this->product_color_view;
    }
}