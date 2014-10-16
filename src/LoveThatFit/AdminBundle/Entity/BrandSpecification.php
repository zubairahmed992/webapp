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
     * @var string $male_fit_type
     * @ORM\Column(type="string", length=1000 , nullable=true)
     */
    protected $male_fit_type;
        /**    
     * @var string $female_fit_type
     * @ORM\Column(type="string", length=1000 , nullable=true)
     */
    protected $female_fit_type;
    /**     
     * @var string $size_title_type
     *  @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $size_title_type;

        /**     
     * @var string $male_size_title_type
     *  @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_size_title_type;
    
    
        /**     
     * @var string $female_size_title_type
     *  @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_size_title_type;
    /**
     * @var string $male_numbers
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_chest;
    
    /**
     * @var string $male_shirt
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_shirt;
    
    /**
     * @var string $male_letter
     * @ORM\Column( type="string", length=1000, nullable=true)
     */
    protected $male_letter;
    
    
    /**
     * @var string $male_waist
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_waist;
    
    /**
     * @var string $male_neck
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $male_neck;
    
    /**
     * @var string $female_number
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_number;
    
    /**
     * @var string $female_letter
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_letter;
    
    /**
     * @var string $female_waist
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_waist;
   
  /**
     * @var string $female_bra
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $female_bra;  

   

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

    

    /**
     * Set male_chest
     *
     * @param string $maleChest
     * @return BrandSpecification
     */
    public function setMaleChest($maleChest)
    {
        $this->male_chest = $maleChest;
    
        return $this;
    }

    /**
     * Get male_chest
     *
     * @return string 
     */
    public function getMaleChest()
    {
        return $this->male_chest;
    }

    /**
     * Set male_shirt
     *
     * @param string $maleShirt
     * @return BrandSpecification
     */
    public function setMaleShirt($maleShirt)
    {
        $this->male_shirt = $maleShirt;
    
        return $this;
    }

    /**
     * Get male_shirt
     *
     * @return string 
     */
    public function getMaleShirt()
    {
        return $this->male_shirt;
    }

    /**
     * Set male_letter
     *
     * @param string $maleLetter
     * @return BrandSpecification
     */
    public function setMaleLetter($maleLetter)
    {
        $this->male_letter = $maleLetter;
    
        return $this;
    }

    /**
     * Get male_letter
     *
     * @return string 
     */
    public function getMaleLetter()
    {
        return $this->male_letter;
    }

    /**
     * Set male_waist
     *
     * @param string $maleWaist
     * @return BrandSpecification
     */
    public function setMaleWaist($maleWaist)
    {
        $this->male_waist = $maleWaist;
    
        return $this;
    }

    /**
     * Get male_waist
     *
     * @return string 
     */
    public function getMaleWaist()
    {
        return $this->male_waist;
    }

    /**
     * Set male_neck
     *
     * @param string $maleNeck
     * @return BrandSpecification
     */
    public function setMaleNeck($maleNeck)
    {
        $this->male_neck = $maleNeck;
    
        return $this;
    }

    /**
     * Get male_neck
     *
     * @return string 
     */
    public function getMaleNeck()
    {
        return $this->male_neck;
    }

    /**
     * Set female_number
     *
     * @param string $femaleNumber
     * @return BrandSpecification
     */
    public function setFemaleNumber($femaleNumber)
    {
        $this->female_number = $femaleNumber;
    
        return $this;
    }

    /**
     * Get female_number
     *
     * @return string 
     */
    public function getFemaleNumber()
    {
        return $this->female_number;
    }

    /**
     * Set female_letter
     *
     * @param string $femaleLetter
     * @return BrandSpecification
     */
    public function setFemaleLetter($femaleLetter)
    {
        $this->female_letter = $femaleLetter;
    
        return $this;
    }

    /**
     * Get female_letter
     *
     * @return string 
     */
    public function getFemaleLetter()
    {
        return $this->female_letter;
    }

    /**
     * Set female_waist
     *
     * @param string $femaleWaist
     * @return BrandSpecification
     */
    public function setFemaleWaist($femaleWaist)
    {
        $this->female_waist = $femaleWaist;
    
        return $this;
    }

    /**
     * Get female_waist
     *
     * @return string 
     */
    public function getFemaleWaist()
    {
        return $this->female_waist;
    }

    /**
     * Set female_bra
     *
     * @param string $femaleBra
     * @return BrandSpecification
     */
    public function setFemaleBra($femaleBra)
    {
        $this->female_bra = $femaleBra;
    
        return $this;
    }

    /**
     * Get female_bra
     *
     * @return string 
     */
    public function getFemaleBra()
    {
        return $this->female_bra;
    }

    /**
     * Set male_fit_type
     *
     * @param string $maleFitType
     * @return BrandSpecification
     */
    public function setMaleFitType($maleFitType)
    {
        $this->male_fit_type = $maleFitType;
    
        return $this;
    }

    /**
     * Get male_fit_type
     *
     * @return string 
     */
    public function getMaleFitType()
    {
        return $this->male_fit_type;
    }

    /**
     * Set female_fit_type
     *
     * @param string $femaleFitType
     * @return BrandSpecification
     */
    public function setFemaleFitType($femaleFitType)
    {
        $this->female_fit_type = $femaleFitType;
    
        return $this;
    }

    /**
     * Get female_fit_type
     *
     * @return string 
     */
    public function getFemaleFitType()
    {
        return $this->female_fit_type;
    }

    /**
     * Set male_size_title_type
     *
     * @param string $maleSizeTitleType
     * @return BrandSpecification
     */
    public function setMaleSizeTitleType($maleSizeTitleType)
    {
        $this->male_size_title_type = $maleSizeTitleType;
    
        return $this;
    }

    /**
     * Get male_size_title_type
     *
     * @return string 
     */
    public function getMaleSizeTitleType()
    {
        return $this->male_size_title_type;
    }

    /**
     * Set female_size_title_type
     *
     * @param string $femaleSizeTitleType
     * @return BrandSpecification
     */
    public function setFemaleSizeTitleType($femaleSizeTitleType)
    {
        $this->female_size_title_type = $femaleSizeTitleType;
    
        return $this;
    }

    /**
     * Get female_size_title_type
     *
     * @return string 
     */
    public function getFemaleSizeTitleType()
    {
        return $this->female_size_title_type;
    }
    #------------------------------------------------------
    
   public function getArray() {
        return array(
            'id' => $this->id,
            'gender' => $this->gender,
            'male_fit_type' => $this->male_fit_type,
            'female_fit_type' => $this->female_fit_type,            
            'male_size_title_type' => $this->male_size_title_type,
            'female_size_title_type' => $this->female_size_title_type,
            'male_chest' => $this->male_chest,
            'male_shirt' => $this->male_shirt,
            'male_letter' => $this->male_letter,
            'male_waist' => $this->male_waist,
            'male_neck' => $this->male_neck,
            'female_number' => $this->female_number,
            'female_letter' => $this->female_letter,
            'female_waist' => $this->female_waist,
            'female_bra' => $this->female_bra,
        );
    }
    #------------------------------------------------------
    public function getSpecsArray() {
        return array(            
            'genders' => $this->gender,
            'fit_types' => array(
                'male' => $this->male_fit_type,
                'female' => $this->female_fit_type,            
                ),        
            'size_title_types' => array(
                'male' => $this->male_size_title_type,            
                'female' => $this->female_size_title_type,
                ),
            'sizes' => array(
                'male' => array(
                    'chest' => $this->male_chest,
                    'shirt' => $this->male_shirt,
                    'letter' => $this->male_letter,
                    'waist' => $this->male_waist,
                    'neck' => $this->male_neck,
                    ),
                'female' => array(
                    'number' => $this->female_number,
                    'letter' => $this->female_letter,
                    'waist' => $this->female_waist,
                    'bra' => $this->female_bra,
            )),
        );
    }
    
    #------------------------------------------------------
    public function getSizeArray($gender, $type) {        
        $fit_type=  $this->get_fit_type_array($gender);
        $size_array=  $this->get_size_array($gender, $type);
        $body_type_sizes=array();
        if (count($fit_type)>0){
            foreach ($fit_type as $ft => $v) {
                $body_type_sizes[$v]=$size_array;        
            }  
        }
        return $body_type_sizes;
    }
    #----------------------------------------------
    private function get_fit_type_array($gender){
        if ($gender=='m') return  json_decode ($this->male_fit_type);
        if ($gender=='f') return json_decode ($this->female_fit_type);
        return null;
    }
    #----------------------------------------------
    private function get_size_array($gender, $type){
        switch ($gender){
            case 'm':
                switch ($type){
                    case 'chest':          
                        return json_decode ($this->male_chest);
                        break;
                    case 'shirt':         
                        return json_decode ($this->male_shirt);
                        break;
                    case 'letter':        
                        return json_decode ($this->male_letter);
                        break;
                    case 'waist':          
                        return json_decode ($this->male_waist);
                        break;
                    case 'neck':          
                        return json_decode ($this->male_neck);
                        break;
                    default :                
                        break;
                }
                break;
            case 'f':
                switch ($type){
                    case 'number':       
                        return json_decode ($this->female_number);
                        break;
                    case 'letter':                
                        return json_decode ($this->female_letter);
                        break;
                    case 'waist':            
                        json_decode ($this->female_waist);
                        break;
                    case 'bra':             
                        return json_decode ($this->female_bra);
                        break;
                    default :                
                        break;                    
                }
                break;
            default:
                break;
        }
        return null;
    }
}