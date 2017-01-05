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

    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=true)
     */
    private $disabled;
    
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
    #-------------------------------------------------------------------------------
     public function getMeasurementArray()
    {
        $fp = $this->product->getFitPriorityArray();
        $size_array = array();
        foreach ($this->product_size_measurements as $psm) {
            if($psm->getTitle()!='key')
            $size_array[$psm->getTitle()] = array( 'id' => $psm->getId(),  
                                                   'title' => $psm->getTitle(), # will remove after checking usage 
                                                   'fit_point' => $psm->getTitle(),                                                          
                                                   'fit_priority' =>  array_key_exists($psm->getTitle(), $fp)? $fp[$psm->getTitle()]: 0,
                                                   'grade_rule' =>  $psm->getGradeRule(),
                                                   'garment_measurement_flat' => $psm->getGarmentMeasurementFlat(),                
                                                   'garment_measurement_stretch_fit' =>  $psm->getGarmentMeasurementStretchFit(),                
                                                   'min_calculated' =>  $psm->getMinCalculated(),
                                                   'min_body_measurement' => $psm->getMinBodyMeasurement(), # will remove after checking usage 
                                                   'ideal_body_low' => $psm->getIdealBodySizeLow(), # will remove 
                                                   'ideal_body_size_low' => $psm->getIdealBodySizeLow(), 
                                                   'fit_model' => $psm->getFitModelMeasurement(), 
                                                   'ideal_body_high' => $psm->getIdealBodySizeHigh(), # will remove 
                                                   'ideal_body_size_high' => $psm->getIdealBodySizeHigh(), 
                                                   'max_body_measurement' => $psm->getMaxBodyMeasurement(),
                                                   'max_calculated' =>  $psm->getMaxCalculated(),                                                   
                                                   );
         }
         $size_array['size_title']=$this->getTitle();
         $size_array['body_type']=$this->getBodyType();     
            return $size_array;
    }
      #-------------------------------------------------------------------------------
     public function getPriorityMeasurementArray()
    {
        $fp = $this->product->getFitPriorityArray();
         
        $size_array = array();
        foreach ($this->product_size_measurements as $psm) {
            if (array_key_exists($psm->getTitle(), $fp)){
                if ($fp[$psm->getTitle()]>0){
                    $size_array[$psm->getTitle()] = array( 'id' => $psm->getId(),  
                                                           'fit_point' => $psm->getTitle(),  
                                                            'ideal_body_size_high' => $psm->getIdealBodySizeHigh() , 
                                                            'ideal_body_size_low' => $psm->getIdealBodySizeLow(), 
                                                            'max_body_measurement' => $psm->getMaxBodyMeasurement(), 
                                                            'fit_priority' => $fp[$psm->getTitle()],
                        );
                }
                
            }
        }
         $size_array['size_title']=$this->getTitle();
         $size_array['body_type']=$this->getBodyType();
            return $size_array;
    }
    #-------------------------------------------------------------------------------
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

    /**
     * Set index_value
     *
     * @param integer $indexValue
     * @return ProductSize
     */
    public function setIndexValue($indexValue)
    {
        $this->index_value = $indexValue;
    
        return $this;
    }

    /**
     * Get index_value
     *
     * @return integer 
     */
    public function getIndexValue()
    {
        return $this->index_value;
    }

    //----------------------------------------------------------
    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Product
     */
    public function setDisabled($disabled) {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled() {
        return $this->disabled;
    }
    
    function toArray(){
        return array(
            'id'=>  $this->id,
            'title'=> $this->title,
            'body_type'=>  $this->body_type,
            'index_value'=>  $this->index_value,
        );    
    }
    
    
   public function fitpointMeasurements($fit_point) {
       foreach($this->product_size_measurements as $psm){
           if($fit_point==$psm->getTitle()){
               return $psm;
           }
       }
       return;
    }
}