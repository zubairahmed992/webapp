<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SizeChartRepository") 
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class SizeChart
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="sizechart")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $brand;
    
    
    
   /* 
 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\Measurement", mappedBy="sizechart")
 *@ORM\JoinColumn(name="top_fitting_size_chart_id", onDelete="CASCADE", referencedColumnName="id")
 */
    protected $top_fitting_size_chart;
    
   /* 
 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\Measurement", mappedBy="sizechart")
 *@ORM\JoinColumn(name="bottom_fitting_size_chart_id", onDelete="CASCADE", referencedColumnName="id")
 */ 
   protected $bottom_fitting_size_chart;
   
   /* 
 * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\Measurement", mappedBy="sizechart")
 *@ORM\JoinColumn(name="dress_fitting_size_chart_id", onDelete="CASCADE", referencedColumnName="id")
 */
   protected $dress_fitting_size_chart;
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
     * @var string $gender
     *
     * @ORM\Column(name="gender", type="string", length=255)
     */
    private $gender;

    /**
     * @var string $target
     *
     * @ORM\Column(name="target", type="string", length=255)
     */
    private $target;

    /**
     * @var string $target
     *
     * @ORM\Column(name="bodytype", type="string", length=255)
     */
    private $bodytype;
    /**
     * @var float $waist
     * @ORM\Column(name="waist", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $waist=0;

    /**
     * @var float $hip
     *
     * @ORM\Column(name="hip", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $hip=0;

    /**
     * @var float $bust
     *
     * @ORM\Column(name="bust", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $bust=0;

    /**
     * @var float $chest
     *
     * @ORM\Column(name="chest", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $chest=0;

    /**
     * @var float $inseam
     *
     * @ORM\Column(name="inseam", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $inseam=0;
    
    /**
     * @var float $outseam
     *
     * @ORM\Column(name="outseam", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $outseam=0;

    /**
     * @var float $neck
     *
     * @ORM\Column(name="neck", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $neck=0;

    /**
     * @var float $sleeve
     *
     * @ORM\Column(name="sleeve", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $sleeve=0;
    
    /**
     * @var float $shoulder_across_back
     *
     * @ORM\Column(name="shoulder_across_back", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
   private $shoulder_across_back=0;
    
    /**
     * @var float $thigh
     *
     * @ORM\Column(name="thigh", type="float")
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    private $thigh=0;

/**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    public $disabled;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $size_title_type;
    
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
     * @return SizeChart
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
     * Set gender
     *
     * @param string $gender
     * @return SizeChart
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return SizeChart
     */
    public function setTarget($target)
    {
        $this->target = $target;
    
        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set waist
     *
     * @param integer $waist
     * @return SizeChart
     */
    public function setWaist($waist)
    {
        $this->waist = $waist;
    
        return $this;
    }

    /**
     * Get waist
     *
     * @return integer 
     */
    public function getWaist()
    {
        return $this->waist;
    }

    /**
     * Set hip
     *
     * @param integer $hip
     * @return SizeChart
     */
    public function setHip($hip)
    {
        $this->hip = $hip;
    
        return $this;
    }

    /**
     * Get hip
     *
     * @return integer 
     */
    public function getHip()
    {
        return $this->hip;
    }

    /**
     * Set bust
     *
     * @param integer $bust
     * @return SizeChart
     */
    public function setBust($bust)
    {
        $this->bust = $bust;
    
        return $this;
    }

    /**
     * Get bust
     *
     * @return integer 
     */
    public function getBust()
    {
        return $this->bust;
    }

    /**
     * Set chest
     *
     * @param integer $chest
     * @return SizeChart
     */
    public function setChest($chest)
    {
        $this->chest = $chest;
    
        return $this;
    }

    /**
     * Get chest
     *
     * @return integer 
     */
    public function getChest()
    {
        return $this->chest;
    }

    /**
     * Set inseam
     *
     * @param integer $inseam
     * @return SizeChart
     */
    public function setInseam($inseam)
    {
        $this->inseam = $inseam;
    
        return $this;
    }

    /**
     * Get inseam
     *
     * @return integer 
     */
    public function getInseam()
    {
        return $this->inseam;
    }

    /**
     * Set neck
     *
     * @param integer $neck
     * @return SizeChart
     */
    public function setNeck($neck)
    {
        $this->neck = $neck;
    
        return $this;
    }

    /**
     * Get neck
     *
     * @return integer 
     */
    public function getNeck()
    {
        return $this->neck;
    }

    /**
     * Set sleeve
     *
     * @param integer $sleeve
     * @return SizeChart
     */
    public function setSleeve($sleeve)
    {
        $this->sleeve = $sleeve;
    
        return $this;
    }

    /**
     * Get sleeve
     *
     * @return integer 
     */
    public function getSleeve()
    {
        return $this->sleeve;
    }

   

    

    /**
     * Set brand
     *
     * @param LoveThatFit\AdminBundle\Entity\Brand $brand
     * @return SizeChart
     */
    public function setBrand(\LoveThatFit\AdminBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return LoveThatFit\AdminBundle\Entity\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }

  

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return SizeChart
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    
        return $this;
    }

    

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set bodytype
     *
     * @param string $bodytype
     * @return SizeChart
     */
    public function setBodytype($bodytype)
    {
        $this->bodytype = $bodytype;
    
        return $this;
    }

    /**
     * Get bodytype
     *
     * @return string 
     */
    public function getBodytype()
    {
        return $this->bodytype;
    }

    /**
     * Set outseam
     *
     * @param float $outseam
     * @return SizeChart
     */
    public function setOutseam($outseam)
    {
        $this->outseam = $outseam;
    
        return $this;
    }

    /**
     * Get outseam
     *
     * @return float 
     */
    public function getOutseam()
    {
        return $this->outseam;
    }

    /**
     * Set shoulderAcrossBack
     *
     * @param float $shoulderAcrossBack
     * @return Measurement
     */
    public function setShoulderAcrossBack($shoulderAcrossBack)
    {
        $this->shoulder_across_back = $shoulderAcrossBack;
    
        return $this;
    }

    /**
     * Get shoulderAcrossBack
     *
     * @return float 
     */
    public function getShoulderAcrossBack()
    {
        return $this->shoulder_across_back;
    }

    /**
     * Set thigh
     *
     * @param float $thigh
     * @return SizeChart
     */
    public function setThigh($thigh)
    {
        $this->thigh = $thigh;
    
        return $this;
    }

    /**
     * Get thigh
     *
     * @return float 
     */
    public function getThigh()
    {
        return $this->thigh;
    }

    /**
     * Set size_title_type
     *
     * @param string $sizeTitleType
     * @return SizeChart
     */
    public function setSizeTitleType($sizeTitleType)
    {
        $this->size_title_type = $sizeTitleType;
    
        return $this;
    }

    /**
     * Get size_title_type
     *
     * @return string 
     */
    public function getSizeTitleType()
    {
        return $this->size_title_type;
    }
    
    public function toArray(){
        return array(
        'id' => $this->id,        
        'title' => $this->title,
        'gender' => $this->gender,
        'target' => $this->target,
        'waist' => $this->waist,
        'hip' => $this->hip,
        'bust' => $this->bust,
        'chest' => $this->chest,
        'inseam' => $this->inseam,
        'neck' => $this->neck,
        'sleeve' => $this->sleeve,
        'brand' => $this->brand->getName(),
        'disabled' => $this->disabled,
        'bodytype' => $this->bodytype,
        'outseam' => $this->outseam,
        'title' => $this->title,
        'gender' => $this->gender,
        'size_title_type' => $this->size_title_type,
        'thigh' => $this->thigh,
        'shoulder_across_back' => $this->shoulder_across_back,           
        
        );
    }
}