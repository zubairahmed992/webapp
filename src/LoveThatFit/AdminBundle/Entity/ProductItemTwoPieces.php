<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\AdminBundle\Entity\ProductItemTwoPieces
 * @ORM\Table(name="product_item_two_pieces")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductItemTwoPiecesRepository")
 */
class ProductItemTwoPieces
{
   
     /**     
     * Bidirectional (OWNING SIDE - FK)
     *  
     * @ORM\ManyToOne(targetEntity="ProductItem", inversedBy="productitemtwopieces")    
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
      */
    protected $productitem;      
   
    
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string  $piece_type  
     * @ORM\Column(name="piece_type", type="string", length=255)
     */
    protected $piece_type;

    /**
     * @var string $piece_image  
     * @ORM\Column(name="piece_image", type="string",length=255, nullable=true)
     */
    protected $piece_image;

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
     * @return ProductItemTwoPieces
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
     * Set piece_image
     *
     * @param string $pieceImage
     * @return ProductItemTwoPieces
     */
    public function setPieceImage($pieceImage)
    {
        $this->piece_image = $pieceImage;
    
        return $this;
    }

    /**
     * Get piece_image
     *
     * @return string 
     */
    public function getPieceImage()
    {
        return $this->piece_image;
    }

    /**
     * Set productitem
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItem $productitem
     * @return ProductItemTwoPieces
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
}