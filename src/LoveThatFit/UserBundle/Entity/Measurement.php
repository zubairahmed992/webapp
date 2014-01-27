<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\Measurement
 *
 * @ORM\Table(name="ltf_measurements")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\MeasurementRepository")
 */
class Measurement {

    public $bra_letters;
    public $bra_numbers;
    
    /**
     * Bidirectional (OWNING SIDE - FK)
     * 
     * @ORM\OneToOne(targetEntity="User", inversedBy="measurement")
     * @ORM\JoinColumn(name="user_id", onDelete="CASCADE", referencedColumnName="id")
     * */
    private $user;

 /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\SizeChart",inversedBy="measurement")
     * @ORM\JoinColumn(name="top_fitting_size_chart_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $top_fitting_size_chart;

 /** 
 * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\SizeChart", inversedBy="measurement")
 * @ORM\JoinColumn(name="bottom_fitting_size_chart_id", onDelete="CASCADE", referencedColumnName="id")
 */
    private $bottom_fitting_size_chart;
 
 /** 
 * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\SizeChart", inversedBy="measurement")
 * @ORM\JoinColumn(name="dress_fitting_size_chart_id", onDelete="CASCADE", referencedColumnName="id")
 */
    private $dress_fitting_size_chart;
 

//---------------------------------------------------------------------    

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**

     * @var float $weight
     *
     * @ORM\Column(name="weight", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "350",
     *      minMessage = "You must Enter the your weight",
     *      maxMessage = "You cannot weight more than 300 lbs",
     *      groups={"registration_step_two","profile_measurement"}
     * )     
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female","profile_measurement_female"}) 
     */
    private $weight=0;
    /**
     * @var float $height
     *
     * @ORM\Column(name="height", type="float", nullable=true,options={"default" = 0})
     * 
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "96",
     *      minMessage = "You must be at least 20 tall inches",
     *      maxMessage = "You cannot taller than 96 inches",
     *      groups={"registration_step_two","profile_measurement"}
    
     * )      
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $height=0;

    /**
     * @var float $waist
     *
     * @ORM\Column(name="waist", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "70",
     *      minMessage = "You must have at least 10 waist inches",
     *      maxMessage = "You cannot have more than 70 waist inches",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $waist=0;

    /**
     * @var float $hip
     *
     * @ORM\Column(name="hip", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "70",
     *      minMessage = "You must have at least 10 inches ",
     *      maxMessage = "You cannot have more than 70 inches ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $hip=0;

    /**
     * @var float $bust
     *
     * @ORM\Column(name="bust", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "70",
     *      minMessage = "You must have at least 10 inches ",
     *      maxMessage = "You cannot have more than 70 inches ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $bust=0;
       /**
     * @var float $chest
     *
     * @ORM\Column(name="chest", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "70",
     *      minMessage = "You must have at least 10 inches ",
     *      maxMessage = "You cannot have more than 70 inches  ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $chest=0;


    /**
     * @var float $arm
     *
     * @ORM\Column(name="arm", type="float", nullable=true,options={"default" = 0})\
     *      
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0 inches ",
     *      maxMessage = "You cannot have more than 300 inches  ",
     *      groups={"profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $arm=0;
    

    /**
     * @var float $inseam
     *
     * @ORM\Column(name="inseam", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "50",
     *      minMessage = "You must have at least 6 inches ",
     *      maxMessage = "You cannot have more than 50 inches ",
     *      groups={"profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $inseam=0;
    /**
     * @var float $shoulder_height
     *
     * @ORM\Column(name="shoulder_height", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $shoulder_height=0;
    
    
    /**
     * @var float $shoulder_width
     *
     * @ORM\Column(name="shoulder_width", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $shoulder_width=0;
    
    
    /**
     * @var float $bust_height
     *
     * @ORM\Column(name="bust_height", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $bust_height=0;
    
    
    /**
     * @var float $waist_height
     *
     * @ORM\Column(name="waist_height", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $waist_height=0;
    
    
    
    
    
    /**
     * @var float $hip_height
     *
     * @ORM\Column(name="hip_height", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $hip_height=0;
    
    
    /**
     * @var float $bust_width
     *
     * @ORM\Column(name="bust_width", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $bust_width=0;
    
    
    
    /**
     * @var float $waist_width
     *
     * @ORM\Column(name="waist_width", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $waist_width=0;
    
    /**
     * @var float $hip_width
     *
     * @ORM\Column(name="hip_width", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $hip_width=0;
    
    
    /**
     * @var float $outseam
     *
     * @ORM\Column(name="outseam", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "60",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 60 inches  "
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $outseam=0;
     /**
     * @var float $sleeve
     *
     * @ORM\Column(name="sleeve", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0  inches  ",
     *      maxMessage = "You cannot have more than 300 inches ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $sleeve=0;
    /**
     * @var float $neck
     *
     * @ORM\Column(name="neck", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0 inches  ",
     *      maxMessage = "You cannot have more than 300 inches  ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $neck=0;
    /**
     * @var float $iphone_shoulder_height
     *
     * @ORM\Column(name="iphone_shoulder_height", type="float", nullable=true,options={"default" = 0})
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0  inches  ",
     *      maxMessage = "You cannot have more than 300 inches "
     * )
     */
    private $iphone_shoulder_height=0;
    
    /**
     * @var float $iphone_outseam
     *
     * @ORM\Column(name="iphone_outseam", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0  inches  ",
     *      maxMessage = "You cannot have more than 300 inches "
     * )
     */
    private $iphone_outseam=0;
    /**
     * @var string $bra_size
     *
     * @ORM\Column(name="bra_size", type="string", length=50, nullable=true)
     * 
     */
    private $bra_size;
    
    /**
     * @var string $bodyTypes
     *
     * @ORM\Column(name="body_types", type="string", length=50, nullable=true)
     * 
     */
    private $body_types;
    
    /**
     * @var string $bodyShape
     *
     * @ORM\Column(name="body_shape", type="string", length=50, nullable=true)
     * 
     */
    private $body_shape;
    
    
    /**
     * @var \DateTime $created_at
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;
    
    /**
     * @var float $thigh
     *
     * @ORM\Column(name="thigh", type="float", nullable=true,options={"default" = 0})
     * 
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "96",
     *      minMessage = "You must be at least 20 tall inches",
     *      maxMessage = "You cannot taller than 96 inches",
     *      groups={"registration_step_two","profile_measurement"}
    
     * )      
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $thigh=0;
    
    /**
     * @var float $shoulderAcrossFront
     *
     * @ORM\Column(name="shoulder_across_front", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $shoulder_across_front=0;
    
    /**
     * @var float $shoulderAcrossBack
     *
     * @ORM\Column(name="shoulder_across_back", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $shoulder_across_back=0;
    /**
     * @var float $bicep
     *
     * @ORM\Column(name="bicep", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $bicep=0;
    
     /**
     * @var float $tricep
     *
     * @ORM\Column(name="tricep", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $tricep=0;
    
    /**
     * @var float $wrist
     *
     * @ORM\Column(name="wrist", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $wrist=0;
    
     /**
     * @var float $center_front_waist
     *
     * @ORM\Column(name="center_front_waist", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $center_front_waist=0;
    
     /**
     * @var float $back_waist
     *
     * @ORM\Column(name="backWaist", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $back_waist=0;
    
    /**
     * @var float $waist_hip
     *
     * @ORM\Column(name="waist_hip", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $waist_hip=0;
    
    /**
     * @var float $knee
     *
     * @ORM\Column(name="knee", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $knee=0;
    
    /**
     * @var float $calf
     *
     * @ORM\Column(name="calf", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $calf=0;
    
    /**
     * @var float $ankle
     *
     * @ORM\Column(name="ankle", type="float", nullable=true,options={"default" = 0})
     * @Assert\Blank(groups={"registration_measurement_male","registration_measurement_female"})
     * @Assert\Regex(pattern="/[0-9]/",message="Require number only",groups={"registration_measurement_male","registration_measurement_female"}) 
     */
    private $ankle=0;
    
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return Measurement
     */
    public function setWeight($weight) {
        if($weight!=null)
        {
           $this->weight = $weight;
            return $this;
        }else
        {
          return $this->weight =0;
        
        }
    }

    /**
     * Get weight
     *
     * @return float 
     */
    public function getWeight() {
        if($this->weight!=null)
        {
           return $this->weight;
        }else
        {
          return $this->weight =0;        
        }
        
    }

    /**
     * Set height
     *
     * @param float $height
     * @return Measurement
     */
    public function setHeight($height) {
        if($height!=null)
        {
           $this->height = $height;
            return $this;
        }else
        {
          return $this->height =0;        
        }
    }

    /**
     * Get height
     *
     * @return float 
     */
    public function getHeight() {
        if($this->height!=null)
        {
          return $this->height;         
        }else
        {
          return $this->height =0;
        
        }
        
    }

    /**
     * Set waist
     *
     * @param float $waist
     * @return Measurement
     */
    public function setWaist($waist) {
       if($waist!=null)
        {
           $this->waist = $waist;
            return $this;
        }else
        {
          return $this->waist =0;        
        }
    }

    /**
     * Get waist
     *
     * @return float 
     */
    public function getWaist() {
        if($this->waist!=null)
        {
           return $this->waist;
        }else
        {
          return $this->waist =0;        
        }
        
        
    }

    /**
     * Set hip
     *
     * @param float $hip
     * @return Measurement
     */
    public function setHip($hip) {
        if($hip!=null)
        {
           $this->hip = $hip;
            return $this;
        }else
        {
          return $this->hip =0;
        
        }     
    }

    /**
     * Get hip
     *
     * @return float 
     */
    public function getHip() {
        if($this->hip!=null)
        {
           return $this->hip;
        }else
        {
          return $this->hip =0;        
        }
    }

    /**
     * Set bust
     *
     * @param float $bust
     * @return Measurement
     */
    public function setBust($bust) {
        if($bust!=null)
        {
           $this->bust = $bust;
            return $this;
        }else
        {
          return $this->bust =0;
        
        }       
    }

    /**
     * Get bust
     *
     * @return float 
     */
    public function getBust() {
        if($this->bust!=null)
        {
           return $this->bust;
        }else
        {
          return $this->bust =0;        
        }
    }

    /**
     * Set arm
     *
     * @param float $arm
     * @return Measurement
     */
    public function setArm($arm) {
        if($arm!=null)
        {
           $this->arm = $arm;
            return $this;
        }else
        {
          return $this->arm =0;
        
        }       
    }

    /**
     * Get arm
     *
     * @return float 
     */
    public function getArm() {
        if($this->arm!=null)
        {
           return $this->arm;
        }else
        {
          return $this->arm =0;        
        }
    }

   
    /**
     * Set inseam
     *
     * @param float $inseam
     * @return Measurement
     */
    public function setInseam($inseam) {
        if($inseam!=null)
        {
           $this->inseam = $inseam;
            return $this;
        }else
        {
          return $this->inseam =0;
        
        }       
    }

    /**
     * Get inseam
     *
     * @return float 
     */
    public function getInseam() {
        if($this->inseam!=null)
        {
           return $this->inseam;
        }else
        {
          return $this->inseam =0;        
        }
    }
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Measurement
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Measurement
     */
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set user
     *
     * @param LoveThatFit\UserBundle\Entity\User $user
     * @return Measurement
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }


    /**
     * Set shoulder_height
     *
     * @param float $shoulderHeight
     * @return Measurement
     */
    public function setShoulderHeight($shoulderHeight)
    {
        if($shoulderHeight!=null)
        {
           $this->shoulder_height = $shoulderHeight;
            return $this;
        }else
        {
          return $this->shoulder_height =0;        
        }
        
    }

    /**
     * Get shoulder_height
     *
     * @return float 
     */
    public function getShoulderHeight()
    {
        if($this->shoulder_height!=null)
        {
           return $this->shoulder_height;
        }else
        {
          return $this->shoulder_height =0;        
        }
    }

   
    /*
     * 
English BMI Formula
BMI = ( Weight in Pounds / ( Height in inches x Height in inches ) ) x 703
Metric BMI Formula
BMI = ( Weight in Kilograms / ( Height in Meters x Height in Meters ) )
     */
    
    public function getBMI()
    {
        if ($this->height && $this->height > 0){
            return round((($this->weight / ($this->height * $this->height)) * 703),2);
        }
        else{
            return 0;
        }
            
    }

    

    /**
     * Set chest
     *
     * @param float $chest
     * @return Measurement
     */
    public function setChest($chest)
    {
        if($chest!=null)
        {
           $this->chest = $chest;
            return $this;
        }else
        {
          return $this->chest =0;
        
        }        
    }

    /**
     * Get chest
     *
     * @return float 
     */
    public function getChest()
    {
        if($this->chest!=null)
        {
           return $this->chest;
        }else
        {
          return $this->chest =0;        
        }       
    }

    /**
     * Set sleeve
     *
     * @param float $sleeve
     * @return Measurement
     */
    public function setSleeve($sleeve)
    {
        if($sleeve!=null)
        {
           $this->sleeve = $sleeve;
            return $this;
        }else
        {
          return $this->sleeve =0;
        
        }
    }

    /**
     * Get sleeve
     *
     * @return float 
     */
    public function getSleeve()
    {
        if($this->sleeve!=null)
        {
           return $this->sleeve;
        }else
        {
          return $this->sleeve =0;        
        }
    }

    /**
     * Set neck
     *
     * @param float $neck
     * @return Measurement
     */
    public function setNeck($neck)
    {
        if($neck!=null)
        {
           $this->neck = $neck;
            return $this;
        }else
        {
          return $this->neck =0;
        
        }
       
    }

    /**
     * Get neck
     *
     * @return float 
     */
    public function getNeck()
    {
        if($this->neck!=null)
        {
           return $this->neck;
        }else
        {
          return $this->neck =0;        
        }
    }

    /**
     * Set outseam
     *
     * @param float $outseam
     * @return Measurement
     */
    public function setOutseam($outseam)
    {
        if($outseam!=null)
        {
           $this->outseam = $outseam;
            return $this;
        }else
        {
          return $this->outseam =0;        
        }        
    }

    /**
     * Get outseam
     *
     * @return float 
     */
    public function getOutseam()
    {
        if($this->outseam!=null)
        {
           return $this->outseam;
        }else
        {
          return $this->outseam =0;        
        }
    }
    public $top_brand;
    public $bottom_brand;
    public $dress_brand;
    public $top_size;
    public $bottom_size;
    public $dress_size;
    //public $body_types;

   

    /**
     * Set top_fitting_size_chart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $topFittingSizeChart
     * @return Measurement
     */
    public function setTopFittingSizeChart(\LoveThatFit\AdminBundle\Entity\SizeChart $topFittingSizeChart = null)
    {
        $this->top_fitting_size_chart = $topFittingSizeChart;
    
        return $this;
    }

    /**
     * Get top_fitting_size_chart
     *
     * @return LoveThatFit\AdminBundle\Entity\SizeChart 
     */
    public function getTopFittingSizeChart()
    {
        return $this->top_fitting_size_chart;
    }

    /**
     * Set bottom_fitting_size_chart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $bottomFittingSizeChart
     * @return Measurement
     */
    public function setBottomFittingSizeChart(\LoveThatFit\AdminBundle\Entity\SizeChart $bottomFittingSizeChart = null)
    {
        $this->bottom_fitting_size_chart = $bottomFittingSizeChart;
    
        return $this;
    }

    /**
     * Get bottom_fitting_size_chart
     *
     * @return LoveThatFit\AdminBundle\Entity\SizeChart 
     */
    public function getBottomFittingSizeChart()
    {
        return $this->bottom_fitting_size_chart;
    }

    /**
     * Set dress_fitting_size_chart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $dressFittingSizeChart
     * @return Measurement
     */
    public function setDressFittingSizeChart(\LoveThatFit\AdminBundle\Entity\SizeChart $dressFittingSizeChart = null)
    {
        $this->dress_fitting_size_chart = $dressFittingSizeChart;
    
        return $this;
    }

    /**
     * Get dress_fitting_size_chart
     *
     * @return LoveThatFit\AdminBundle\Entity\SizeChart 
     */
    public function getDressFittingSizeChart()
    {
        return $this->dress_fitting_size_chart;
    }

    /**
     * Set iphone_neck
     *
     * @param float $iphoneNeck
     * @return Measurement
     */
    public function setIphoneNeck($iphoneNeck)
    {
        $this->iphone_neck = $iphoneNeck;
    
        return $this;
    }

    /**
     * Get iphone_neck
     *
     * @return float 
     */
    public function getIphoneNeck()
    {
        return $this->iphone_neck;
    }

    /**
     * Set iphone_outseam
     *
     * @param float $iphoneOutseam
     * @return Measurement
     */
    public function setIphoneOutseam($iphoneOutseam)
    {
        $this->iphone_outseam = $iphoneOutseam;
    
        return $this;
    }

    /**
     * Get iphone_outseam
     *
     * @return float 
     */
    public function getIphoneOutseam()
    {
        return $this->iphone_outseam;
    }

    /**
     * Set iphone_shoulder_height
     *
     * @param float $iphoneShoulderHeight
     * @return Measurement
     */
    public function setIphoneShoulderHeight($iphoneShoulderHeight)
    {
        $this->iphone_shoulder_height = $iphoneShoulderHeight;
    
        return $this;
    }

    /**
     * Get iphone_shoulder_height
     *
     * @return float 
     */
    public function getIphoneShoulderHeight()
    {
        return $this->iphone_shoulder_height;
    }

    

    

    /**
     * Set body_shape
     *
     * @param string $bodyShape
     * @return Measurement
     */
    public function setBodyShape($bodyShape)
    {
        $this->body_shape = $bodyShape;
    
        return $this;
    }

    /**
     * Get body_shape
     *
     * @return string 
     */
    public function getBodyShape()
    {
        return $this->body_shape;
    }

    /**
     * Set body_types
     *
     * @param string $bodyTypes
     * @return Measurement
     */
    public function setBodyTypes($bodyTypes)
    {
        $this->body_types = $bodyTypes;
    
        return $this;
    }

    /**
     * Get body_types
     *
     * @return string 
     */
    public function getBodyTypes()
    {
        return $this->body_types;
    }

    

    /**
     * Set thigh
     *
     * @param float $thigh
     * @return Measurement
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
    
    
    public function getArray() {
        $u=$this->getUser();
        return array(
            'gender' => $u->getGender(),
            'weight' => $this->weight,
            'height' => $this->height,
            'waist' => $this->waist,
            'hip' => $this->hip,
            'bust' => $this->bust,
            'chest' => $this->chest,
            'arm' => $this->arm,
            'inseam' => $this->inseam,            
            'shoulder_height' => $this->shoulder_height,
            'outseam' => $this->outseam,
            'sleeve' => $this->sleeve,
            'neck' => $this->neck,
            'thigh' => $this->thigh,
            'center_front_waist' => $this->center_front_waist,
            'shoulder_across_front' => $this->shoulder_across_front,
            'shoulder_across_back' => $this->shoulder_across_back,
            'bicep' => $this->bicep,
            'tricep' => $this->tricep,
            'wrist' => $this->wrist,
            'back_waist' => $this->back_waist,
            'waist_hip' => $this->waist_hip,
            'knee' => $this->knee,
            'calf' => $this->calf,
            'ankle' => $this->ankle,
        );
    }

    /**
     * Set shoulder_width
     *
     * @param float $shoulderWidth
     * @return Measurement
     */
    public function setShoulderWidth($shoulderWidth)
    {
        $this->shoulder_width = $shoulderWidth;
    
        return $this;
    }

    /**
     * Get shoulder_width
     *
     * @return float 
     */
    public function getShoulderWidth()
    {
        return $this->shoulder_width;
    }

    /**
     * Set bust_height
     *
     * @param float $bustHeight
     * @return Measurement
     */
    public function setBustHeight($bustHeight)
    {
        $this->bust_height = $bustHeight;
    
        return $this;
    }

    /**
     * Get bust_height
     *
     * @return float 
     */
    public function getBustHeight()
    {
        return $this->bust_height;
    }

    /**
     * Set waist_height
     *
     * @param float $waistHeight
     * @return Measurement
     */
    public function setWaistHeight($waistHeight)
    {
        $this->waist_height = $waistHeight;
    
        return $this;
    }

    /**
     * Get waist_height
     *
     * @return float 
     */
    public function getWaistHeight()
    {
        return $this->waist_height;
    }

    /**
     * Set hip_height
     *
     * @param float $hipHeight
     * @return Measurement
     */
    public function setHipHeight($hipHeight)
    {
        $this->hip_height = $hipHeight;
    
        return $this;
    }

    /**
     * Get hip_height
     *
     * @return float 
     */
    public function getHipHeight()
    {
        return $this->hip_height;
    }

    /**
     * Set bust_width
     *
     * @param float $bustWidth
     * @return Measurement
     */
    public function setBustWidth($bustWidth)
    {
        $this->bust_width = $bustWidth;
    
        return $this;
    }

    /**
     * Get bust_width
     *
     * @return float 
     */
    public function getBustWidth()
    {
        return $this->bust_width;
    }

    /**
     * Set waist_width
     *
     * @param float $waistWidth
     * @return Measurement
     */
    public function setWaistWidth($waistWidth)
    {
        $this->waist_width = $waistWidth;
    
        return $this;
    }

    /**
     * Get waist_width
     *
     * @return float 
     */
    public function getWaistWidth()
    {
        return $this->waist_width;
    }

    /**
     * Set hip_width
     *
     * @param float $hipWidth
     * @return Measurement
     */
    public function setHipWidth($hipWidth)
    {
        $this->hip_width = $hipWidth;
    
        return $this;
    }

    /**
     * Get hip_width
     *
     * @return float 
     */
    public function getHipWidth()
    {
        return $this->hip_width;
    }

    /**
     * Set shoulderAcrossFront
     *
     * @param float $shoulderAcrossFront
     * @return Measurement
     */
    public function setShoulderAcrossFront($shoulderAcrossFront)
    {
        $this->shoulder_across_front = $shoulderAcrossFront;
    
        return $this;
    }

    /**
     * Get shoulderAcrossFront
     *
     * @return float 
     */
    public function getShoulderAcrossFront()
    {
        return $this->shoulder_across_front;
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
     * Set bicep
     *
     * @param float $bicep
     * @return Measurement
     */
    public function setBicep($bicep)
    {
        $this->bicep = $bicep;
    
        return $this;
    }

    /**
     * Get bicep
     *
     * @return float 
     */
    public function getBicep()
    {
        return $this->bicep;
    }

    /**
     * Set tricep
     *
     * @param float $tricep
     * @return Measurement
     */
    public function setTricep($tricep)
    {
        $this->tricep = $tricep;
    
        return $this;
    }

    /**
     * Get tricep
     *
     * @return float 
     */
    public function getTricep()
    {
        return $this->tricep;
    }

    /**
     * Set wrist
     *
     * @param float $wrist
     * @return Measurement
     */
    public function setWrist($wrist)
    {
        $this->wrist = $wrist;
    
        return $this;
    }

    /**
     * Get wrist
     *
     * @return float 
     */
    public function getWrist()
    {
        return $this->wrist;
    }

    /**
     * Set centerFrontWaist
     *
     * @param float $centerFrontWaist
     * @return Measurement
     */
    public function setCenterFrontWaist($centerFrontWaist)
    {
        $this->center_front_waist = $centerFrontWaist;
    
        return $this;
    }

    /**
     * Get centerFrontWaist
     *
     * @return float 
     */
    public function getCenterFrontWaist()
    {
        return $this->center_front_waist;
    }

    /**
     * Set backWaist
     *
     * @param float $backWaist
     * @return Measurement
     */
    public function setBackWaist($backWaist)
    {
        $this->back_waist = $backWaist;
    
        return $this;
    }

    /**
     * Get backWaist
     *
     * @return float 
     */
    public function getBackWaist()
    {
        return $this->back_waist;
    }

    /**
     * Set waistHip
     *
     * @param float $waistHip
     * @return Measurement
     */
    public function setWaistHip($waistHip)
    {
        $this->waist_hip = $waistHip;
    
        return $this;
    }

    /**
     * Get waistHip
     *
     * @return float 
     */
    public function getWaistHip()
    {
        return $this->waist_hip;
    }

    /**
     * Set knee
     *
     * @param float $knee
     * @return Measurement
     */
    public function setKnee($knee)
    {
        $this->knee = $knee;
    
        return $this;
    }

    /**
     * Get knee
     *
     * @return float 
     */
    public function getKnee()
    {
        return $this->knee;
    }

    /**
     * Set calf
     *
     * @param float $calf
     * @return Measurement
     */
    public function setCalf($calf)
    {
        $this->calf = $calf;
    
        return $this;
    }

    /**
     * Get calf
     *
     * @return float 
     */
    public function getCalf()
    {
        return $this->calf;
    }

    /**
     * Set ankle
     *
     * @param float $ankle
     * @return Measurement
     */
    public function setAnkle($ankle)
    {
        $this->ankle = $ankle;
    
        return $this;
    }

    /**
     * Get ankle
     *
     * @return float 
     */
    public function getAnkle()
    {
        return $this->ankle;
    }
    

    /**
     * Set bra_size
     *
     * @param string $bra_size
     * @return Measurement
     */
    public function setBrasize($brasize)
    {
        $this->bra_size = $brasize;
    
        return $this;
    }

    /**
     * Get brasize
     *
     * @return string 
     */
    public function getBrasize()
    {
        return $this->bra_size;
    }
    /**
     * Get brasizeletters
     *
     * @return string 
     */
    
    
    public function getBraSizes()
    {
        if ($this->bra_size==null) return null;
      $bra_letters=  explode(' ',$this->bra_size);      
      return $bra_letters[0];
    }
    /**
     * Get brasizeCup
     *
     * @return string 
     */
    
    public function getBraCup()
    {
        if ($this->bra_size==null) return null;
        $bra_cup=  explode(' ',$this->bra_size);      
        if (array_key_exists(1, $bra_cup)) {
        return $bra_cup[1];}
        else{
            return null;
        }
    }

    
}