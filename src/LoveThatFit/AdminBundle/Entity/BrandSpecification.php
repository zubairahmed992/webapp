<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/** 
 * LoveThatFit\AdminBundle\Entity\BrandSpecification
 * @ORM\Table(name="brand_specification")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\BrandSpecificationRepository")
 */
class BrandSpecification {       
    
    /**     
     * Bidirectional (OWNING SIDE - FK)
     * 
     * @ORM\OneToOne(targetEntity="Brand", inversedBy="brandspecification")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $brand;  

    /////////////////////////////////////////////////////////////////////////////////  

   
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**   
     * @var string  $gender
     * @ORM\Column(type="string", length=255, nullable=true)     
     */
    protected $gender;

    /**    
     * @var string $fit_type
     * @ORM\Column(type="string", length=1000 , nullable=true)
     */
    private $fit_type;

    /**     
     * @var string $size_title_type
     *  @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $size_title_type;

    /**
     * @var string $male_numbers
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_numbers;
    
    /**
     * @var string $male_letters
     * @ORM\Column( type="string", length=1000, nullable=true)
     */
    protected $male_letters;
    
    
    /**
     * @var string $male_waists
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_waists;
    
    /**
     * @var string $female_numbers
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_numbers;
    
    /**
     * @var string $female_letters
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_letters;
    
    /**
     * @var string $female_waists
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_waists;

   

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
     * Set gender
     *
     * @param string $gender
     * @return BrandSpecification
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
     * Set fit_type
     *
     * @param string $fitType
     * @return BrandSpecification
     */
    public function setFitType($fitType)
    {
        $this->fit_type = $fitType;
    
        return $this;
    }

    /**
     * Get fit_type
     *
     * @return string 
     */
    public function getFitType()
    {
        return $this->fit_type;
    }

    /**
     * Set size_title_type
     *
     * @param string $sizeTitleType
     * @return BrandSpecification
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

    /**
     * Set male_numbers
     *
     * @param string $maleNumbers
     * @return BrandSpecification
     */
    public function setMaleNumbers($maleNumbers)
    {
        $this->male_numbers = $maleNumbers;
    
        return $this;
    }

    /**
     * Get male_numbers
     *
     * @return string 
     */
    public function getMaleNumbers()
    {
        return $this->male_numbers;
    }

    /**
     * Set male_letters
     *
     * @param string $maleLetters
     * @return BrandSpecification
     */
    public function setMaleLetters($maleLetters)
    {
        $this->male_letters = $maleLetters;
    
        return $this;
    }

    /**
     * Get male_letters
     *
     * @return string 
     */
    public function getMaleLetters()
    {
        return $this->male_letters;
    }

    /**
     * Set male_waists
     *
     * @param string $maleWaists
     * @return BrandSpecification
     */
    public function setMaleWaists($maleWaists)
    {
        $this->male_waists = $maleWaists;
    
        return $this;
    }

    /**
     * Get male_waists
     *
     * @return string 
     */
    public function getMaleWaists()
    {
        return $this->male_waists;
    }

    /**
     * Set female_numbers
     *
     * @param string $femaleNumbers
     * @return BrandSpecification
     */
    public function setFemaleNumbers($femaleNumbers)
    {
        $this->female_numbers = $femaleNumbers;
    
        return $this;
    }

    /**
     * Get female_numbers
     *
     * @return string 
     */
    public function getFemaleNumbers()
    {
        return $this->female_numbers;
    }

    /**
     * Set female_letters
     *
     * @param string $femaleLetters
     * @return BrandSpecification
     */
    public function setFemaleLetters($femaleLetters)
    {
        $this->female_letters = $femaleLetters;
    
        return $this;
    }

    /**
     * Get female_letters
     *
     * @return string 
     */
    public function getFemaleLetters()
    {
        return $this->female_letters;
    }

    /**
     * Set female_waists
     *
     * @param string $femaleWaists
     * @return BrandSpecification
     */
    public function setFemaleWaists($femaleWaists)
    {
        $this->female_waists = $femaleWaists;
    
        return $this;
    }

    /**
     * Get female_waists
     *
     * @return string 
     */
    public function getFemaleWaists()
    {
        return $this->female_waists;
    }

    /**
     * Set brand
     *
     * @param \LoveThatFit\AdminBundle\Entity\Brand $brand
     * @return BrandSpecification
     */
    public function setBrand(\LoveThatFit\AdminBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return \LoveThatFit\AdminBundle\Entity\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    
}