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

    /**
     * Bidirectional (OWNING SIDE - FK)
     * 
     * @ORM\OneToOne(targetEntity="User", inversedBy="measurement")
     * @ORM\JoinColumn(name="user_id", onDelete="CASCADE", referencedColumnName="id")
     * */
    private $user;

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
     * @ORM\Column(name="weight", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "90",
     *      max = "350",
     *      minMessage = "You must weight at least 90 inches",
     *      maxMessage = "You cannot weight more than 300 inches",
     *      groups={"registration_step_two","profile_measurement"}
     
     * )
     * @Assert\NotBlank(groups={"registration_step_two","profile_measurement"}) 
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $weight=0;

    /**
     * @var float $height
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     * 
     * 
     * @Assert\Range(
     *      min = "20",
     *      max = "96",
     *      minMessage = "You must be at least 20 tall inches",
     *      maxMessage = "You cannot taller than 96 inches",
     *      groups={"registration_step_two","profile_measurement"}
    
     * )      
     * @Assert\NotBlank(groups={"registration_step_two","profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $height=0;

    /**
     * @var float $waist
     *
     * @ORM\Column(name="waist", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "10",
     *      max = "70",
     *      minMessage = "You must have at least 10 waist inches",
     *      maxMessage = "You cannot have more than 70 waist inches",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $waist=0;

    /**
     * @var float $hip
     *
     * @ORM\Column(name="hip", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "10",
     *      max = "70",
     *      minMessage = "You must have at least 10 inches ",
     *      maxMessage = "You cannot have more than 70 inches",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     * @Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $hip=0;

    /**
     * @var float $bust
     *
     * @ORM\Column(name="bust", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "10",
     *      max = "70",
     *      minMessage = "You must have at least 10 inches ",
     *      maxMessage = "You cannot have more than 70 inches ",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     *@Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $bust=0;

    /**
     * @var float $arm
     *
     * @ORM\Column(name="arm", type="float", nullable=true)\
     *      
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0 inches ",
     *      maxMessage = "You cannot have more than 300 inches ",
     *      groups={"profile_measurement"}
     * )
     *@Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $arm=0;

    /**
     * @var float $leg
     *
     * @ORM\Column(name="leg", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0 inches ",
     *      maxMessage = "You cannot have more than 300 inches ",
     *      groups={"profile_measurement"}
     * )
     * @Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $leg=0;

    /**
     * @var float $inseam
     *
     * @ORM\Column(name="inseam", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "6",
     *      max = "50",
     *      minMessage = "You must have at least 6 inches",
     *      maxMessage = "You cannot have more than 50 inches",
     *      groups={"profile_measurement"}
     * )
     * @Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $inseam=0;

    /**
     * @var float $back
     *
     * @ORM\Column(name="back", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "300",
     *      minMessage = "You must have at least 0 inches ",
     *      maxMessage = "You cannot have more than 300 inches",
     *      groups={"registration_step_two","profile_measurement"}
     * )
     *@Assert\NotBlank(groups={"profile_measurement"})  
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $back=0;

    /**
     * @var float $shoulder_height
     *
     * @ORM\Column(name="shoulder_height", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "80",
     *      minMessage = "You must have at least 0 inches",
     *      maxMessage = "You cannot have more than 80 inches "
     * )
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $shoulder_height=0;
    
    /**
     * @var float $waist_height
     *
     * @ORM\Column(name="waist_height", type="float", nullable=true)
     * 
     * @Assert\Range(
     *      min = "0",
     *      max = "60",
     *      minMessage = "You must have at least 0 inches ",
     *      maxMessage = "You cannot have more than 60 inches "
     * )
     * @Assert\Regex(pattern= "/[0-9]/",message="Require number only") 
     */
    private $waist_height=0;
    
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
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float 
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return Measurement
     */
    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float 
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Set waist
     *
     * @param float $waist
     * @return Measurement
     */
    public function setWaist($waist) {
        $this->waist = $waist;

        return $this;
    }

    /**
     * Get waist
     *
     * @return float 
     */
    public function getWaist() {
        return $this->waist;
    }

    /**
     * Set hip
     *
     * @param float $hip
     * @return Measurement
     */
    public function setHip($hip) {
        $this->hip = $hip;

        return $this;
    }

    /**
     * Get hip
     *
     * @return float 
     */
    public function getHip() {
        return $this->hip;
    }

    /**
     * Set bust
     *
     * @param float $bust
     * @return Measurement
     */
    public function setBust($bust) {
        $this->bust = $bust;

        return $this;
    }

    /**
     * Get bust
     *
     * @return float 
     */
    public function getBust() {
        return $this->bust;
    }

    /**
     * Set arm
     *
     * @param float $arm
     * @return Measurement
     */
    public function setArm($arm) {
        $this->arm = $arm;

        return $this;
    }

    /**
     * Get arm
     *
     * @return float 
     */
    public function getArm() {
        return $this->arm;
    }

    /**
     * Set leg
     *
     * @param float $leg
     * @return Measurement
     */
    public function setLeg($leg) {
        $this->leg = $leg;

        return $this;
    }

    /**
     * Get leg
     *
     * @return float 
     */
    public function getLeg() {
        return $this->leg;
    }

    /**
     * Set inseam
     *
     * @param float $inseam
     * @return Measurement
     */
    public function setInseam($inseam) {
        $this->inseam = $inseam;

        return $this;
    }

    /**
     * Get inseam
     *
     * @return float 
     */
    public function getInseam() {
        return $this->inseam;
    }

    /**
     * Set back
     *
     * @param float $back
     * @return Measurement
     */
    public function setBack($back) {
        $this->back = $back;

        return $this;
    }

    /**
     * Get back
     *
     * @return float 
     */
    public function getBack() {
        return $this->back;
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
        $this->shoulder_height = $shoulderHeight;
    
        return $this;
    }

    /**
     * Get shoulder_height
     *
     * @return float 
     */
    public function getShoulderHeight()
    {
        return $this->shoulder_height;
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
            return (($this->weight / ($this->height * $this->height)) * 703);
        }
        else{
            return 0;
        }
            
    }

    
}