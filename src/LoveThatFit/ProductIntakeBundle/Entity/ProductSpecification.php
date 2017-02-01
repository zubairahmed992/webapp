<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\ProductIntakeBundle\Entity\ProductSpecificationRepository")
 * @ORM\Table(name="product_specification")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductSpecification {
    
      
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Title!")
     */
    protected $title;

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;
    
    
      /**
     * @var string $specs_json
     * @ORM\Column(type="text", nullable=true)
     */
    protected $specs_json;  
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    
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
     * @return ProductSpecification
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

#--------------------------------------------------------
  /**
     * Set description
     *
     * @param string description
     * @return ProductSpecification
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
     * Set specs_json
     *
     * @param string specs_json
     * @return ProductSpecification
     */
    public function setSpecsJson($specs_json) {
        $this->specs_json = $specs_json;
        return $this;
    }

    /**
     * Get specs_json
     *
     * @return string 
     */
    public function getSpecsJson() {
        return $this->specs_json;
    }
    
#--------------------------------------------------------
    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return ProductSpecification
     */
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;

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
     * @return ProductSpecification
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


######################################################################
    
      /**
     * Constructor
     */
    public function __construct()
    {
        
    }
    
 #--------------------------------------------
  public function toArray(){
      return array(
          'title' =>  $this->getTitle(),
          'description' =>  $this->getDescription(),
          'specs_json' =>  $this->getSpecsJson(),
          'created_at' =>  $this->getCreatedAt(),          
          'updated_at' =>  $this->getUpdatedAt(),          
      );
  }
  
  public function getFitPointArray() {
        $deco = json_decode($this->getSpecsJson(), true);
        $fit_points = array();
        if (is_array($deco) && array_key_exists('sizes', $deco)) {
            foreach ($deco['sizes'] as $s => $fp) {
                if (is_array($fp)) {
                    foreach ($fp as $fit_point => $ranges) {
                        $fit_points[$fit_point] = $fit_point;
                    }
                }
            }
        }
        return $fit_points;
    }
    
}