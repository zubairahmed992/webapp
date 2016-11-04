<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductSpecificationMappingRepository")
 * @ORM\Table(name="product_specification_mapping")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductSpecificationMapping  {
    
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)     
     */
    protected $title;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $brand;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $gender;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $clothing_type;
/**
     * @var string $description
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string $mapping_json
     * @ORM\Column(name="mapping_json", type="text", nullable=true)
     */
    private $mapping_json;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

  
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @var string $disabled
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;
    #-------------------------------------------
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
#-------------------------------------------
    /**
     * Set title
     *
     * @param string $title
     * @return ProductSpecificationMapping
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
#-------------------------------------------
    /**
     * Set brand
     *
     * @param string $brand
     * @return ProductSpecificationMapping
     */
    public function setBrand($brand) {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand() {
        return $this->brand;
    }

    //----------------------------------------------------------
    /**
     * Set description
     *
     * @param string $description
     * @return ProductSpecificationMapping
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

#------------------------------------------------
    /**
     * Set mapping_json
     *
     * @param string $mapping_json
     * @return ProductSpecificationMapping
     */
    public function setMappingJson($mapping_json){
        $this->mapping_json = $mapping_json;    
        return $this;
    }

    /**
     * Get mapping_json
     *
     * @return string 
     */
    public function getMappingJson(){
        return $this->mapping_json;
    }


#----------------------------------------
    
   #-------------------------------------------
    
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ProductSpecificationMapping
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

    #-------------------------------------------
    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return ProductSpecificationMapping
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

   
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }   
        return true;   
    }
//---------------------------------------------------
    
  
    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    
}