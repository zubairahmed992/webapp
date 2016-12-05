<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\FitModelMeasurementRepository")
 * @ORM\Table(name="fit_model_measurement")
 * @ORM\HasLifecycleCallbacks()
 */
class FitModelMeasurement {
    
    /**     
     * Bidirectional (OWNING SIDE - FK)
     * 
     * @ORM\OneToOne(targetEntity="Brand", inversedBy="fit_model_measurements")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $brand;  
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Title!")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Gender!")
     */
    protected $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $size;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $size_title_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $clothing_type;

      /**
     * @var string $measurement_json
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $measurement_json;  
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    
    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=true)
     */
    private $disabled;
    
    
###############################################################
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
#--------------------------------------------------------
  
        /**
     * Set title
     *
     * @param string title
     * @return FitModelMeasurement
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }
#------------------------------------------------------    
    
     /**
     * Set gender
     * @param string $gender
     * @return User
     */
    public function setGender($gender) {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender() {
        return $this->gender;
    }
    
#--------------------------------------------------------
  /**
     * Set size
     *
     * @param string size
     * @return FitModelMeasurement
     */
    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize() {
        return $this->size;
    }
#--------------------------------------------------------
  /**
     * Set size_title_type
     *
     * @param string size_title_type
     * @return FitModelMeasurement
     */
    public function setSizeTitleType($size_title_type) {
        $this->size_title_type = $size_title_type;
        return $this;
    }

    /**
     * Get size_title_type
     *
     * @return string 
     */
    public function getSizeTitleType() {
        return $this->size_title_type;
    }
#--------------------------------------------------------
  /**
     * Set description
     *
     * @param string description
     * @return FitModelMeasurement
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }
    #--------------------------------------------------------
    /**
     * Set measurement_json
     *
     * @param string measurement_json
     * @return FitModelMeasurement
     */
    public function setMeasurementJson($measurement_json) {
        $this->measurement_json = $measurement_json;
        return $this;
    }

    /**
     * Get measurement_json
     *
     * @return string 
     */
    public function getMeasurementJson() {
        return $this->measurement_json;
    }
    #--------------------------------------------------------
    
  /**
     * Set clothing_type
     *
     * @param string clothing_type
     * @return FitModelMeasurement
     */
    public function setClothingType($clothing_type) {
        $this->clothing_type = $clothing_type;
        return $this;
    }

    /**
     * Get clothing_type
     *
     * @return string 
     */
    public function getClothingType() {
        return $this->clothing_type;
    }
    
#--------------------------------------------------------
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return FitModelMeasurement
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
#--------------------------------------------------------
    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return FitModelMeasurement
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

#--------------------------------------------------------
  

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return FitModelMeasurement
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
    
#--------------------------------------------------------
    
      /**
     * Set brand
     *
     * @param \LoveThatFit\AdminBundle\Entity\Brand $brand
     * @return FitModelMeasurement
     */
    public function setBrand(\LoveThatFit\AdminBundle\Entity\Brand $brand = null){
        $this->brand = $brand;    
        return $this;
    }

    /**
     * Get brand
     *
     * @return \LoveThatFit\AdminBundle\Entity\Brand 
     */
    public function getBrand(){
        return $this->brand;
    }


######################################################################
    
      /**
     * Constructor
     */
    public function __construct()
    {
        $this->retailers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sizechart = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
 
    
}