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
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_color_view")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id", onDelete="CASCADE")
     
     */    
    protected $product_color; 
    
    
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
}