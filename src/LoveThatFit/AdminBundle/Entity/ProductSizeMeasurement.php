<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement
 *
 * @ORM\Table(name="product_size_measurement")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductSizeMeasurementRepository")
 */
class ProductSizeMeasurement
{   
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductSize", inversedBy="product_size_measurements")
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id", onDelete="CASCADE")
     *  */
    
    protected $product_size; 
        
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;
    
    /**
     * @var float $grade_rule
     *
     * @ORM\Column(name="grade_rule", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    
    private $grade_rule;
    
    /**
     * @var float $min_calculated
     *
     * @ORM\Column(name="min_calculated", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    
    private $min_calculated;
    /**
     * @var float $min_body_measurement
     *
     * @ORM\Column(name="min_body_measurement", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    
    private $min_body_measurement;
    

    /**
     * @var float $garment_measurement_flat
     *
     * @ORM\Column(name="garment_measurement_flat", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $garment_measurement_flat=0;
    /**
     * @var float $fit_model_measurement
     *
     * @ORM\Column(name="fit_model_measurement", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $fit_model_measurement=0;
    /**
     * @var float $max_body_measurement
     *
     * @ORM\Column(name="max_body_measurement", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $max_body_measurement=0;
    /**
     * @var float $max_calculated
     *
     * @ORM\Column(name="max_calculated", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $max_calculated=0;
    /**
     * @var float $vertical_stretch
     *
     * @ORM\Column(name="vertical_stretch", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $vertical_stretch=0;
    /**
     * @var float $horizontal_stretch
     *
     * @ORM\Column(name="horizontal_stretch", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $horizontal_stretch=0;
    /**
     * @var float $stretch_type_percentage
     *
     * @ORM\Column(name="stretch_type_percentage", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $stretch_type_percentage=0;
     /**
     * @var float $garment_measurement_stretch_fit
     *
     * @ORM\Column(name="garment_measurement_stretch_fit", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $garment_measurement_stretch_fit=0;
    
    /**
     * @var float $ideal_body_size_high
     *
     * @ORM\Column(name="ideal_body_size_high", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $ideal_body_size_high=0;
    /**
     * @var float $ideal_body_size_low
     *
     * @ORM\Column(name="ideal_body_size_low", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $ideal_body_size_low=0;
    
    
    #-------------------------------------------------------------------

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
     * @return ProductSizeMeasurement
     */
    #-------------------------------------------------------------------
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

    #-------------------------------------------------------------------
    /**
     * Set grade_rule
     *
     * @param float $grade_rule
     * @return ProductSizeMeasurement
     */
    public function setGradeRule($grade_rule)
    {
        $this->grade_rule = $grade_rule;
    
        return $this;
    }

    /**
     * Get GradeRule
     *
     * @return float 
     */
    public function getGradeRule()
    {
        return $this->grade_rule;
    }
    #-------------------------------------------------------------------
    /**
     * Set garment_measurement_flat
     *
     * @param float $garmentMeasurementFlat
     * @return ProductSizeMeasurement
     */
    public function setGarmentMeasurementFlat($garmentMeasurementFlat)
    {
        $this->garment_measurement_flat = $garmentMeasurementFlat;
    
        return $this;
    }
#-------------------------------------------------------------------
    /**
     * Get garment_measurement_flat
     *
     * @return float 
     */
    public function getGarmentMeasurementFlat()
    {
        return $this->garment_measurement_flat;
    }

    /**
     * Set max_body_measurement
     *
     * @param float $maxBodyMeasurement
     * @return ProductSizeMeasurement
     */
    #-------------------------------------------------------------------
    public function setMaxBodyMeasurement($maxBodyMeasurement)
    {
        $this->max_body_measurement = $maxBodyMeasurement;
    
        return $this;
    }

    /**
     * Get max_body_measurement
     *
     * @return float 
     */
    public function getMaxBodyMeasurement()
    {
        return $this->max_body_measurement;
    }

    /**
     * Set max_calculated
     *
     * @param float $maxCalculated
     * @return ProductSizeMeasurement
     */
    #-------------------------------------------------------------------
    /**
     * Set min_body_measurement
     *
     * @param float $minBodyMeasurement
     * @return ProductSizeMeasurement
     */
    public function setMinCalculated($minCalculated)
    {
        $this->min_calculated = $minCalculated;
    
        return $this;
    }

    /**
     * Get min_calculated
     *
     * @return float 
     */
    public function getMinCalculated()
    {
        return $this->min_calculated;
    }
#-------------------------------------------------------------------
    public function setMaxCalculated($maxCalculated)
    {
        $this->max_calculated = $maxCalculated;
    
        return $this;
    }

    /**
     * Get max_calculated
     *
     * @return float 
     */
    public function getMaxCalculated()
    {
        return $this->max_calculated;
    }
    #-------------------------------------------------------------------
    /**
     * Set vertical_stretch
     *
     * @param float $verticalStretch
     * @return ProductSizeMeasurement
     */
    public function setVerticalStretch($verticalStretch)
    {
        $this->vertical_stretch = $verticalStretch;
    
        return $this;
    }

    /**
     * Get vertical_stretch
     *
     * @return float 
     */
    public function getVerticalStretch()
    {
        return $this->vertical_stretch;
    }
#-------------------------------------------------------------------
    /**
     * Set horizontal_stretch
     *
     * @param float $horizontalStretch
     * @return ProductSizeMeasurement
     */
    public function setHorizontalStretch($horizontalStretch)
    {
        $this->horizontal_stretch = $horizontalStretch;
    
        return $this;
    }

    /**
     * Get horizontal_stretch
     *
     * @return float 
     */
    public function getHorizontalStretch()
    {
        return $this->horizontal_stretch;
    }
#-------------------------------------------------------------------
    /**
     * Set stretch_type_percentage
     *
     * @param float $stretchTypePercentage
     * @return ProductSizeMeasurement
     */
    public function setStretchTypePercentage($stretchTypePercentage)
    {
        $this->stretch_type_percentage = $stretchTypePercentage;
    
        return $this;
    }

    /**
     * Get stretch_type_percentage
     *
     * @return float 
     */
    public function getStretchTypePercentage()
    {
        return $this->stretch_type_percentage;
    }
#-------------------------------------------------------------------
    /**
     * Set ideal_body_size_high
     *
     * @param float $idealBodySizeHigh
     * @return ProductSizeMeasurement
     */
    public function setIdealBodySizeHigh($idealBodySizeHigh)
    {
        $this->ideal_body_size_high = $idealBodySizeHigh;
    
        return $this;
    }

    /**
     * Get ideal_body_size_high
     *
     * @return float 
     */
    public function getIdealBodySizeHigh()
    {
        return $this->ideal_body_size_high;
    }
#-------------------------------------------------------------------
    /**
     * Set ideal_body_size_low
     *
     * @param float $idealBodySizeLow
     * @return ProductSizeMeasurement
     */
    public function setIdealBodySizeLow($idealBodySizeLow)
    {
        $this->ideal_body_size_low = $idealBodySizeLow;
    
        return $this;
    }

    /**
     * Get ideal_body_size_low
     *
     * @return float 
     */
    public function getIdealBodySizeLow()
    {
        return $this->ideal_body_size_low;
    }
#-------------------------------------------------------------------
    /**
     * Set product_size
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductSize $productSize
     * @return ProductSizeMeasurement
     */
    public function setProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSize = null)
    {
        $this->product_size = $productSize;
    
        return $this;
    }

    /**
     * Get product_size
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductSize 
     */
    public function getProductSize()
    {
        return $this->product_size;
    }
#-------------------------------------------------------------------
    /**
     * Set garment_measurement_stretch_fit
     *
     * @param float $garmentMeasurementStretchFit
     * @return ProductSizeMeasurement
     */
    public function setGarmentMeasurementStretchFit($garmentMeasurementStretchFit)
    {
        $this->garment_measurement_stretch_fit = $garmentMeasurementStretchFit;
    
        return $this;
    }

    /**
     * Get garment_measurement_stretch_fit
     *
     * @return float 
     */
    public function getGarmentMeasurementStretchFit()
    {
        return $this->garment_measurement_stretch_fit;
    }
#-------------------------------------------------------------------
    /**
     * Set min_body_measurement
     *
     * @param string $minBodyMeasurement
     * @return ProductSizeMeasurement
     */
    public function setMinBodyMeasurement($minBodyMeasurement)
    {
        $this->min_body_measurement = $minBodyMeasurement;
    
        return $this;
    }

    /**
     * Get min_body_measurement
     *
     * @return string 
     */
    public function getMinBodyMeasurement()
    {
        return $this->min_body_measurement;
    }
    
    #-------------------------------------------------------------------
    
    /**
     * Set fit_model_measurement
     *
     * @param string $fit_model_measurement
     * @return ProductSizeMeasurement
     */
    public function setFitModelMeasurement($fit_model_measurement)
    {
        $this->fit_model_measurement = $fit_model_measurement;
    
        return $this;
    }

    /**
     * Get fit_model_measurement
     *
     * @return string 
     */
    public function getFitModelMeasurement()
    {
        return $this->fit_model_measurement;
    }
    
    public function toArray() {
        return array(
            'title' => $this->title,
            'garment_measurement_flat' => $this->garment_measurement_flat,
            'max_body_measurement' => $this->max_body_measurement,
            'vertical_stretch' => $this->vertical_stretch,
            'horizontal_stretch' => $this->horizontal_stretch,
            'stretch_type_percentage' => $this->stretch_type_percentage,
            'ideal_body_size_high' => $this->ideal_body_size_high,
            'ideal_body_size_low' => $this->ideal_body_size_low,
            'garment_measurement_stretch_fit' => $this->garment_measurement_stretch_fit,
            'min_body_measurement' => $this->min_body_measurement,
            'fit_model_measurement' => $this->fit_model_measurement,
            'grade_rule' => $this->grade_rule,
            'min_calculated' => $this->min_calculated,
            'max_calculated' => $this->max_calculated,
        );
    }
}