<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoveThatFit\AdminBundle\Entity\ProductSize
 *
 * @ORM\Table(name="product_size")
 * @ORM\Entity
 */
class ProductSize
{
    
    
     /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_sizes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product; 
    
    
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
     * @var float $inseam
     *
     * @ORM\Column(name="inseam", type="float")
     */
    private $inseam;

    /**
     * @var float $outseam
     *
     * @ORM\Column(name="outseam", type="float")
     */
    private $outseam;

    /**
     * @var float $hip
     *
     * @ORM\Column(name="hip", type="float")
     */
    private $hip;

    /**
     * @var float $bust
     *
     * @ORM\Column(name="bust", type="float")
     */
    private $bust;

    /**
     * @var float $back
     *
     * @ORM\Column(name="back", type="float")
     */
    private $back;

    /**
     * @var float $arm
     *
     * @ORM\Column(name="arm", type="float")
     */
    private $arm;

    /**
     * @var float $leg
     *
     * @ORM\Column(name="leg", type="float")
     */
    private $leg;

    /**
     * @var float $hem
     *
     * @ORM\Column(name="hem", type="float")
     */
    private $hem;


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
     * Set arm
     *
     * @param float $arm
     * @return ProductSize
     */
    public function setArm($arm)
    {
        $this->arm = $arm;
    
        return $this;
    }

    /**
     * Get arm
     *
     * @return float 
     */
    public function getArm()
    {
        return $this->arm;
    }

    /**
     * Set leg
     *
     * @param float $leg
     * @return ProductSize
     */
    public function setLeg($leg)
    {
        $this->leg = $leg;
    
        return $this;
    }

    /**
     * Get leg
     *
     * @return float 
     */
    public function getLeg()
    {
        return $this->leg;
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
}