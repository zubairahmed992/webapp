<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\ProductIntakeBundle\Entity\ProductSpecificationMappingRepository")
 * @ORM\Table(name="product_specification_mapping")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductSpecificationMapping {

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
    protected $size_title_type;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $mapping_file_name;

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
     * Set clothing_type
     *
     * @param string $clothing_type
     * @return ProductSpecificationMapping
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

#-------------------------------------------
    /**
     * Set gender
     *
     * @param string $gender
     * @return ProductSpecificationMapping
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

#-------------------------------------------
    /**
     * Set size_title_type
     *
     * @param string $size_title_type
     * @return ProductSpecificationMapping
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

    public function setMappingJson($mapping_json) {
        $this->mapping_json = $mapping_json;
        return $this;
    }

    /**
     * Get mapping_json
     *
     * @return string 
     */
    public function getMappingJson() {
        return $this->mapping_json;
    }

#----------------------------------------
    /**
     * Set mapping_file_name
     *
     * @param string $mapping_file_name
     * @return ProductSpecificationMapping
     */

    public function setMappingFileName($file_name) {
        $this->mapping_file_name = $file_name;
        return $this;
    }

    /**
     * Get mapping_file_name
     *
     * @return string 
     */
    public function getMappingFileName() {
        return $this->mapping_file_name;
    }

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

    public function setDisabled($disabled) {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled() {
        return $this->disabled;
    }

//---------------------------------------------------

    /**
     * Constructor
     */
    public function __construct() {
        
    }

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->mapping_file_name = uniqid() . '.' . $ext;
        $this->file->move(
                $this->getUploadRootDir(), $this->mapping_file_name
        );
    }

    //---------------------------------------------------

    public function getAbsolutePath() {
        return null === $this->mapping_file_name ? null : $this->getUploadRootDir() . '/' . $this->mapping_file_name;
    }

    //---------------------------------------------------
    public function getWebPath() {
        return null === $this->mapping_file_name ? null : $this->getUploadDir() . '/' . $this->mapping_file_name;
    }

    //---------------------------------------------------
    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    //---------------------------------------------------
    protected function getUploadDir() {
        return 'uploads/ltf/products/product_csv';
    }

}