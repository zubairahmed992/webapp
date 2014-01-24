<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * LoveThatFit\AdminBundle\Entity\ProductSize
 *
 * @ORM\Table(name="product_size")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductSizeRepository")
 */
class ProductSize
{
    
    
     /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_sizes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     *  */
    protected $product; 

     /**
     * @ORM\OneToMany(targetEntity="ProductItem", mappedBy="product_size", orphanRemoval=true)
     */
    
    protected $product_items; 
    
    
/**
     * @ORM\OneToMany(targetEntity="ProductSizeMeasurement", mappedBy="product_size", orphanRemoval=true)
     */
    
    protected $product_size_measurements;
      
    
      public function __construct()
    {
        $this->product_items = new ArrayCollection(); 
        $this->product_size_measurements = new ArrayCollection(); 
    }
    
    
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string",nullable=true)
     */
    private $title;    
    
    /**
     * @var string $body_type
     *
     * @ORM\Column(name="body_type", type="string",nullable=true)
     */
    private $body_type;   
    /**
     * Get id
     *
     * @return integer 
     */
    
    /**
     * @var string $index_value
     *
     * @ORM\Column(name="index_value", type="integer",nullable=true)
     */
    private $index_value;  
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ProductSize
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
    
    public function getDescription()
    {
        return $this->body_type . ' ' . $this->title;
    }
    
    /**
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductSize
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
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return ProductSize
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items[] = $productItems;
    
        return $this;
    }

    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items->removeElement($productItems);
    }

    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems()
    {
        return $this->product_items;
    }   

    /**
     * Add product_size_measurements
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement $productSizeMeasurements
     * @return ProductSize
     */
    public function addProductSizeMeasurement(\LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement $productSizeMeasurements)
    {
        $this->product_size_measurements[] = $productSizeMeasurements;
    
        return $this;
    }

    /**
     * Remove product_size_measurements
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement $productSizeMeasurements
     */
    public function removeProductSizeMeasurement(\LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement $productSizeMeasurements)
    {
        $this->product_size_measurements->removeElement($productSizeMeasurements);
    }

    /**
     * Get product_size_measurements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductSizeMeasurements()
    {
        return $this->product_size_measurements;
    }

     public function getMeasurementArray()
    {
        $size_array = array();
        foreach ($this->product_size_measurements as $psm) {
            $size_array[$psm->getTitle()] = array( 'id' => $psm->getId(),  'title' => $psm->getTitle(),  'ideal_body_high' => $psm->getIdealBodySizeHigh() , 'ideal_body_low' => $psm->getIdealBodySizeLow(), 'max_body_measurement' => $psm->getMaxBodyMeasurement(), 'size_title'=>$this->getTitle());
         }
            return $size_array;
    }
    public function getFitPointMeasurements($fit_point)
    {
        foreach ($this->product_size_measurements as $psm) {            
            if ($psm->getTitle()==$fit_point){
                return $psm;
            }
         }
            return;
    }
    /**
     * Set body_type
     *
     * @param string $bodyType
     * @return ProductSize
     */
    public function setBodyType($bodyType)
    {
        $this->body_type = $bodyType;
    
        return $this;
    }

    /**
     * Get body_type
     *
     * @return string 
     */
    public function getBodyType()
    {
        return $this->body_type;
    }
}