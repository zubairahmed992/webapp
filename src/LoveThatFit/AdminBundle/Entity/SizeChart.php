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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(groups={"size_chart"})
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
     * @var integer $waist
     *
     * @ORM\Column(name="waist", type="integer", length=22)
     */
    private $waist;

    /**
     * @var integer $hip
     *
     * @ORM\Column(name="hip", type="integer", length=22)
     */
    private $hip;

    /**
     * @var integer $bust
     *
     * @ORM\Column(name="bust", type="integer", length=22)
     */
    private $bust;

    /**
     * @var integer $chest
     *
     * @ORM\Column(name="chest", type="integer", length=22)
     */
    private $chest;

    /**
     * @var integer $inseam
     *
     * @ORM\Column(name="inseam", type="integer", length=22)
     */
    private $inseam;

    /**
     * @var integer $neck
     *
     * @ORM\Column(name="neck", type="integer", length=22)
     */
    private $neck;

    /**
     * @var integer $sleeve
     *
     * @ORM\Column(name="sleeve", type="integer", length=22)
     */
    private $sleeve;


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
}