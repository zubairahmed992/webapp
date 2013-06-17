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
    
    
      public function __construct()
    {
        $this->product_items = new ArrayCollection();
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
     * @var float $inseam
     *
     * @ORM\Column(name="inseam", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $inseam;
    /**
     * @var float $inseam_min
     *
     * @ORM\Column(name="inseam_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $inseam_min;
    /**
     * @var float $inseam_max
     *
     * @ORM\Column(name="inseam_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $inseam_max;

    /**
     * @var float $outseam
     *
     * @ORM\Column(name="outseam", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $outseam;
     /**
     * @var float $outseam_min
     *
     * @ORM\Column(name="outseam_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $outseam_min;
     /**
     * @var float $outseam_max
     *
     * @ORM\Column(name="outseam_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $outseam_max;
    /**
     * @var float $hip
     *
     * @ORM\Column(name="hip", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $hip;
     
     /**
     * @var float $hip_min
     *
     * @ORM\Column(name="hip_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $hip_min;
     /**
     * @var float $hip_max
     *
     * @ORM\Column(name="hip_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $hip_max;

    /**
     * @var float $bust
     *
     * @ORM\Column(name="bust", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $bust;
    /**
     * @var float $bust_min
     *
     * @ORM\Column(name="bust_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $bust_min;
    /**
     * @var float $bust_max
     *
     * @ORM\Column(name="bust_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $bust_max;

    /**
     * @var float $back
     *
     * @ORM\Column(name="back", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $back;
     /**
     * @var float $back_min
     *
     * @ORM\Column(name="back_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $back_min;
     /**
     * @var float $back_max
     *
     * @ORM\Column(name="back_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $back_max;

   


    /**
     * @var float $hem
     *
     * @ORM\Column(name="hem", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $hem;

      /**
     * @var float $length
     *
     * @ORM\Column(name="length", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $length;
    
    /**
     * @var float $waist
     *
     * @ORM\Column(name="waist", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $waist;
     /**
     * @var float $waist_min
     *
     * @ORM\Column(name="waist_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $waist_min; /**
     * @var float $waist_max
     *
     * @ORM\Column(name="waist_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $waist_max;

    
    /**
     * @var float $chest
     *
     * @ORM\Column(name="chest", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $chest;

   /**
     * @var float $chest_min
     *
     * @ORM\Column(name="chest_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $chest_min;
     
     /**
     * @var float $chest_max
     *
     * @ORM\Column(name="chest_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $chest_max;

     
     /**
     * @var float $neck
     *
     * @ORM\Column(name="neck", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $neck;
     
      /**
     * @var float $neck_min
     *
     * @ORM\Column(name="neck_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $neck_min;
     
      /**
     * @var float $neck_max
     *
     * @ORM\Column(name="neck_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
      private $neck_max;
      
     /**
     * @var float $sleeve
     *
     * @ORM\Column(name="sleeve", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $sleeve;
     
      /**
     * @var float $sleeve_min
     *
     * @ORM\Column(name="sleeve_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $sleeve_min;
      /**
     * @var float $sleeve_max
     *
     * @ORM\Column(name="sleeve_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $sleeve_max;     
     /**
     * @var float $thigh
     *
     * @ORM\Column(name="thigh", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $thigh;
     
      /**
     * @var float $thigh_min
     *
     * @ORM\Column(name="thigh_min", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $thigh_min;
      /**
     * @var float $thigh_max
     *
     * @ORM\Column(name="thigh_max", type="float",nullable=true)
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
     private $thigh_max;
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

    /**
     * Set inseam
     *
     * @param float $inseam
     * @return ProductSize
     */
    public function setInseam($inseam)
    {
        $this->inseam = $inseam;
    
        return $this;
    }

    /**
     * Get inseam
     *
     * @return float 
     */
    public function getInseam()
    {
        return $this->inseam;
    }

    /**
     * Set outseam
     *
     * @param float $outseam
     * @return ProductSize
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
     * Set hip
     *
     * @param float $hip
     * @return ProductSize
     */
    public function setHip($hip)
    {
        $this->hip = $hip;
    
        return $this;
    }

    /**
     * Get hip
     *
     * @return float 
     */
    public function getHip()
    {
        return $this->hip;
    }

    /**
     * Set bust
     *
     * @param float $bust
     * @return ProductSize
     */
    public function setBust($bust)
    {
        $this->bust = $bust;
    
        return $this;
    }

    /**
     * Get bust
     *
     * @return float 
     */
    public function getBust()
    {
        return $this->bust;
    }

    /**
     * Set back
     *
     * @param float $back
     * @return ProductSize
     */
    public function setBack($back)
    {
        $this->back = $back;
    
        return $this;
    }

    /**
     * Get back
     *
     * @return float 
     */
    public function getBack()
    {
        return $this->back;
    }

   
   
    /**
     * Set hem
     *
     * @param float $hem
     * @return ProductSize
     */
    public function setHem($hem)
    {
        $this->hem = $hem;
    
        return $this;
    }

    /**
     * Get hem
     *
     * @return float 
     */
    public function getHem()
    {
        return $this->hem;
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
     * Set length
     *
     * @param float $length
     * @return ProductSize
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return float 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set waist
     *
     * @param float $waist
     * @return ProductSize
     */
    public function setWaist($waist)
    {
        $this->waist = $waist;
    
        return $this;
    }

    /**
     * Get waist
     *
     * @return float 
     */
    public function getWaist()
    {
        return $this->waist;
    }

    /**
     * Set inseam_min
     *
     * @param float $inseamMin
     * @return ProductSize
     */
    public function setInseamMin($inseamMin)
    {
        $this->inseam_min = $inseamMin;
    
        return $this;
    }

    /**
     * Get inseam_min
     *
     * @return float 
     */
    public function getInseamMin()
    {
        return $this->inseam_min;
    }

    /**
     * Set inseam_max
     *
     * @param float $inseamMax
     * @return ProductSize
     */
    public function setInseamMax($inseamMax)
    {
        $this->inseam_max = $inseamMax;
    
        return $this;
    }

    /**
     * Get inseam_max
     *
     * @return float 
     */
    public function getInseamMax()
    {
        return $this->inseam_max;
    }

    /**
     * Set outseam_min
     *
     * @param float $outseamMin
     * @return ProductSize
     */
    public function setOutseamMin($outseamMin)
    {
        $this->outseam_min = $outseamMin;
    
        return $this;
    }

    /**
     * Get outseam_min
     *
     * @return float 
     */
    public function getOutseamMin()
    {
        return $this->outseam_min;
    }

    /**
     * Set outseam_max
     *
     * @param float $outseamMax
     * @return ProductSize
     */
    public function setOutseamMax($outseamMax)
    {
        $this->outseam_max = $outseamMax;
    
        return $this;
    }

    /**
     * Get outseam_max
     *
     * @return float 
     */
    public function getOutseamMax()
    {
        return $this->outseam_max;
    }

    /**
     * Set hip_min
     *
     * @param float $hipMin
     * @return ProductSize
     */
    public function setHipMin($hipMin)
    {
        $this->hip_min = $hipMin;
    
        return $this;
    }

    /**
     * Get hip_min
     *
     * @return float 
     */
    public function getHipMin()
    {
        return $this->hip_min;
    }

    /**
     * Set hip_max
     *
     * @param float $hipMax
     * @return ProductSize
     */
    public function setHipMax($hipMax)
    {
        $this->hip_max = $hipMax;
    
        return $this;
    }

    /**
     * Get hip_max
     *
     * @return float 
     */
    public function getHipMax()
    {
        return $this->hip_max;
    }

    /**
     * Set bust_min
     *
     * @param float $bustMin
     * @return ProductSize
     */
    public function setBustMin($bustMin)
    {
        $this->bust_min = $bustMin;
    
        return $this;
    }

    /**
     * Get bust_min
     *
     * @return float 
     */
    public function getBustMin()
    {
        return $this->bust_min;
    }

    /**
     * Set bust_max
     *
     * @param float $bustMax
     * @return ProductSize
     */
    public function setBustMax($bustMax)
    {
        $this->bust_max = $bustMax;
    
        return $this;
    }

    /**
     * Get bust_max
     *
     * @return float 
     */
    public function getBustMax()
    {
        return $this->bust_max;
    }

    /**
     * Set back_min
     *
     * @param float $backMin
     * @return ProductSize
     */
    public function setBackMin($backMin)
    {
        $this->back_min = $backMin;
    
        return $this;
    }

    /**
     * Get back_min
     *
     * @return float 
     */
    public function getBackMin()
    {
        return $this->back_min;
    }

    /**
     * Set back_max
     *
     * @param float $backMax
     * @return ProductSize
     */
    public function setBackMax($backMax)
    {
        $this->back_max = $backMax;
    
        return $this;
    }

    /**
     * Get back_max
     *
     * @return float 
     */
    public function getBackMax()
    {
        return $this->back_max;
    }

    /**
     * Set waist_min
     *
     * @param float $waistMin
     * @return ProductSize
     */
    public function setWaistMin($waistMin)
    {
        $this->waist_min = $waistMin;
    
        return $this;
    }

    /**
     * Get waist_min
     *
     * @return float 
     */
    public function getWaistMin()
    {
        return $this->waist_min;
    }

    /**
     * Set waist_max
     *
     * @param float $waistMax
     * @return ProductSize
     */
    public function setWaistMax($waistMax)
    {
        $this->waist_max = $waistMax;
    
        return $this;
    }

    /**
     * Get waist_max
     *
     * @return float 
     */
    public function getWaistMax()
    {
        return $this->waist_max;
    }

    /**
     * Set chest
     *
     * @param float $chest
     * @return ProductSize
     */
    public function setChest($chest)
    {
        $this->chest = $chest;
    
        return $this;
    }

    /**
     * Get chest
     *
     * @return float 
     */
    public function getChest()
    {
        return $this->chest;
    }

    /**
     * Set chest_min
     *
     * @param float $chestMin
     * @return ProductSize
     */
    public function setChestMin($chestMin)
    {
        $this->chest_min = $chestMin;
    
        return $this;
    }

    /**
     * Get chest_min
     *
     * @return float 
     */
    public function getChestMin()
    {
        return $this->chest_min;
    }

    /**
     * Set chest_max
     *
     * @param float $chestMax
     * @return ProductSize
     */
    public function setChestMax($chestMax)
    {
        $this->chest_max = $chestMax;
    
        return $this;
    }

    /**
     * Get chest_max
     *
     * @return float 
     */
    public function getChestMax()
    {
        return $this->chest_max;
    }

    /**
     * Set neck
     *
     * @param float $neck
     * @return ProductSize
     */
    public function setNeck($neck)
    {
        $this->neck = $neck;
    
        return $this;
    }

    /**
     * Get neck
     *
     * @return float 
     */
    public function getNeck()
    {
        return $this->neck;
    }

    /**
     * Set neck_min
     *
     * @param float $neckMin
     * @return ProductSize
     */
    public function setNeckMin($neckMin)
    {
        $this->neck_min = $neckMin;
    
        return $this;
    }

    /**
     * Get neck_min
     *
     * @return float 
     */
    public function getNeckMin()
    {
        return $this->neck_min;
    }

    /**
     * Set neck_max
     *
     * @param float $neckMax
     * @return ProductSize
     */
    public function setNeckMax($neckMax)
    {
        $this->neck_max = $neckMax;
    
        return $this;
    }

    /**
     * Get neck_max
     *
     * @return float 
     */
    public function getNeckMax()
    {
        return $this->neck_max;
    }

    /**
     * Set sleeve
     *
     * @param float $sleeve
     * @return ProductSize
     */
    public function setSleeve($sleeve)
    {
        $this->sleeve = $sleeve;
    
        return $this;
    }

    /**
     * Get sleeve
     *
     * @return float 
     */
    public function getSleeve()
    {
        return $this->sleeve;
    }

    /**
     * Set sleeve_min
     *
     * @param float $sleeveMin
     * @return ProductSize
     */
    public function setSleeveMin($sleeveMin)
    {
        $this->sleeve_min = $sleeveMin;
    
        return $this;
    }

    /**
     * Get sleeve_min
     *
     * @return float 
     */
    public function getSleeveMin()
    {
        return $this->sleeve_min;
    }

    /**
     * Set sleeve_max
     *
     * @param float $sleeveMax
     * @return ProductSize
     */
    public function setSleeveMax($sleeveMax)
    {
        $this->sleeve_max = $sleeveMax;
    
        return $this;
    }

    /**
     * Get sleeve_max
     *
     * @return float 
     */
    public function getSleeveMax()
    {
        return $this->sleeve_max;
    }

    /**
     * Set thigh
     *
     * @param float $thigh
     * @return ProductSize
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
     * Set thigh_min
     *
     * @param float $thighMin
     * @return ProductSize
     */
    public function setThighMin($thighMin)
    {
        $this->thigh_min = $thighMin;
    
        return $this;
    }

    /**
     * Get thigh_min
     *
     * @return float 
     */
    public function getThighMin()
    {
        return $this->thigh_min;
    }

    /**
     * Set thigh_max
     *
     * @param float $thighMax
     * @return ProductSize
     */
    public function setThighMax($thighMax)
    {
        $this->thigh_max = $thighMax;
    
        return $this;
    }

    /**
     * Get thigh_max
     *
     * @return float 
     */
    public function getThighMax()
    {
        return $this->thigh_max;
    }
}