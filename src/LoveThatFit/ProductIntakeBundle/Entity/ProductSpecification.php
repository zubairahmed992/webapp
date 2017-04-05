<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $brand_name;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $style_id_number;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $style_name;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $clothing_type;
    
      /**
     * @var string $specs_json
     * @ORM\Column(type="text", nullable=true)
     */
    protected $specs_json;  
    
    /**
    * @var string $undo_specs_json
    * @ORM\Column(name="undo_specs_json", type="text", nullable=true)
    */
     private $undo_specs_json;
     
    /**
    * @ORM\Column(type="integer", length=9, nullable=true)
    */
    protected $status;
    
       /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $spec_file_name;
    
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
     * Set style_id_number
     *
     * @param string style_id_number
     * @return ProductSpecification
     */
    public function setStyleIdNumber($style_id_number) {
        $this->style_id_number = $style_id_number;
        return $this;
    }

    /**
     * Get style_id_number
     *
     * @return string 
     */
    public function getStyleIdNumber() {
        return $this->style_id_number;
    }
    
#--------------------------------------------------------
  
        /**
     * Set style_name
     *
     * @param string style_name
     * @return ProductSpecification
     */
    public function setStyleName($style_name) {
        $this->style_name = $style_name;
        return $this;
    }

    /**
     * Get style_name
     *
     * @return string 
     */
    public function getStyleName() {
        return $this->style_name;
    }    
#--------------------------------------------------------
  
        /**
     * Set brand_name
     *
     * @param string brand_name
     * @return ProductSpecification
     */
    public function setBrandName($brand_name) {
        $this->brand_name = $brand_name;
        return $this;
    }

    /**
     * Get brand_name
     *
     * @return string 
     */
    public function getBrandName() {
        return $this->brand_name;
    }    
#--------------------------------------------------------
  
        /**
     * Set clothing_type
     *
     * @param string clothing_type
     * @return ProductSpecification
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
    
    public function getSpecsArray() {
        return json_decode($this->specs_json, true);
    }
    
    #------------------------------------------------
    /**
     * Set undo_specs_json
     *
     * @param string $undo_specs_json
     * @return ProductSpecificationMapping
     */

    public function setUndoSpecsJson($undo_specs_json) {
        $this->undo_specs_json = $undo_specs_json;
        return $this;
    }

    /**
     * Get undo_specs_json
     *
     * @return string 
     */
    public function getUndoSpecsJson() {
        return $this->undo_specs_json;
    }

     #------------------------------------------------
    /**
     * Set status
     *
     * @param interger $status
     * @return ProductSpecificationMapping
     */

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
    }
    
    #----------------------------------------
    /**
     * Set spec_file_name
     *
     * @param string $spec_file_name
     * @return ProductSpecificationMapping
     */

    public function setSpecFileName($file_name) {
        $this->spec_file_name = $file_name;
        return $this;
    }

    /**
     * Get spec_file_name
     *
     * @return string 
     */
    public function getSpecFileName() {
        return $this->spec_file_name;
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
     
    public function getFitPointStretchArray() {
        $fit_points =  $this->getFitPointArray();
        $deco = json_decode($this->getSpecsJson(), true);
        $fp_stretch = is_array($deco)&& array_key_exists('fit_point_stretch', $deco)?$deco['fit_point_stretch']:array();
        $stretch = array();
        foreach ($fit_points as $fp => $title) {                        
                        $stretch[$title] = array_key_exists($title, $fp_stretch)?$fp_stretch[$title]:'';
        }
        return $stretch;
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
        $this->spec_file_name = uniqid() . '.' . $ext;
        $this->file->move(
                $this->getUploadRootDir(), $this->spec_file_name
        );
    }

    //---------------------------------------------------

    public function getAbsolutePath() {      
        return null === $this->spec_file_name ? null : $this->getUploadRootDir() . '/' . $this->spec_file_name;
    }

    //---------------------------------------------------
    public function getWebPath() {
       return null === $this->spec_file_name ? null : $_SERVER['HTTP_HOST'].'/../../../../'.$this->getUploadDir() . '/' . $this->spec_file_name;
    }

    //---------------------------------------------------
    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    //---------------------------------------------------
    protected function getUploadDir() {
        $directory_path = 'uploads/ltf/products/product_spec_csv';
        if (!file_exists($directory_path)) {
            mkdir($directory_path, 0777, true);
        }
        return $directory_path;
    }
    #------------------------------------------------------------
    public function fill($parsed){        
        array_key_exists('style_id_number', $parsed) ? $this->style_id_number = ($parsed['style_id_number']) : '';
        array_key_exists('style_name', $parsed) ? $this->style_name = ($parsed['style_name']) : '';
        array_key_exists('title', $parsed) ? $this->title = ($parsed['title']) : '';
        array_key_exists('description', $parsed) ? $this->description = ($parsed['description']) : '';
        array_key_exists('clothing_type', $parsed) ? $this->clothing_type = ($parsed['clothing_type']) : '';
        array_key_exists('brand', $parsed) ? $this->brand_name = ($parsed['brand']) : '';
        #----------------
        $specs = json_decode($this->specs_json,true);
        #----------------
        array_key_exists('style_id_number', $parsed) ? $specs['style_id_number'] = $parsed['style_id_number'] : '';
        array_key_exists('style_name', $parsed) ? $specs['style_name'] = ($parsed['style_name']) : '';
        array_key_exists('title', $parsed) ? $specs['title'] = ($parsed['title']) : '';
        array_key_exists('description', $parsed) ? $specs['description'] = ($parsed['description']) : '';
        array_key_exists('clothing_type', $parsed) ? $specs['clothing_type'] = ($parsed['clothing_type']) : '';
        array_key_exists('brand', $parsed) ? $specs['brand'] = ($parsed['brand']) : '';
        array_key_exists('styling_type', $parsed) ? $specs['styling_type'] = ($parsed['styling_type']) : '';
        array_key_exists('hem_length', $parsed) ? $specs['hem_length'] = ($parsed['hem_length']) : '';
        array_key_exists('neckline', $parsed) ? $specs['neckline'] = ($parsed['neckline']) : '';
        array_key_exists('sleeve_styling', $parsed) ? $specs['sleeve_styling'] = ($parsed['sleeve_styling']) : '';
        array_key_exists('rise', $parsed) ? $specs['rise'] = ($parsed['rise']) : '';
        array_key_exists('stretch_type', $parsed) ? $specs['stretch_type'] = ($parsed['stretch_type']) : '';
        array_key_exists('fabric_weight', $parsed) ? $specs['fabric_weight'] = ($parsed['fabric_weight']) : '';
        array_key_exists('layering', $parsed) ? $specs['layering'] = ($parsed['layering']) : '';
        array_key_exists('structural_detail', $parsed) ? $specs['structural_detail'] = ($parsed['structural_detail']) : '';
        array_key_exists('fit_type', $parsed) ? $specs['fit_type'] = ($parsed['fit_type']) : '';
        array_key_exists('control_number', $parsed) ? $specs['control_number'] = ($parsed['control_number']) : '';
        array_key_exists('colors', $parsed) ? $specs['colors'] = ($parsed['colors']) : '';
        array_key_exists('measuring_unit', $parsed) ? $specs['measuring_unit'] = ($parsed['measuring_unit']) : '';        
        $this->undo_specs_json = $this->specs_json;
        $this->specs_json =  json_encode($specs);
    }

    
}