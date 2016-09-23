<?php


namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="clothing_type")
 * 
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ClothingTypeRepository")
 */

 
class ClothingType
{
 
  /**
  * @ORM\OneToMany(targetEntity="Product", mappedBy="clothing_type")
  */
    
    
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)     
     */    
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $image;


    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload brand logo image!")
     */
    public $file;
    /**
     * @ORM\Column(type="string", length=255)
     */
    
    protected $target;
    
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)     
     */    
    protected $gender;
     
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;
    
    
    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;

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
     * Set name
     *
     * @param string $name
     * @return ClothingType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return ClothingType
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ClothingType
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return ClothingType
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    
     

   

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return ClothingType
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
    //----------------------------------- sample code for validation
    public function isValid(){
        $msg_array=array();
        $valid=true;
        if($this->name==''){
            array_push($msg_array, array('valid'=>false, 'message'=>'Invalid name'));
            $valid=false;
        }
        
        if($this->target!='Top' || $this->target!='Bottom' || $this->target='Dress' ){
            array_push($msg_array, array('valid'=>false, 'message'=>'Invalid Target'));
            $valid=false;
        }
          
        array_push($msg_array, array('valid'=>$valid));
        
        return $msg_array;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return ClothingType
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
     * Set image
     *
     * @param string $image
     * @return Brand
     */
    public function setImage($image) {
      $this->image = $image;

      return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage() {
      return $this->image;
    }
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
      // the file property can be empty if the field is not required
      if (null === $this->file) {
        return;
      }

      $ih=new ImageHelper('clothing_type', $this);
      $ih->upload();
    }
  //---------------------------------------------------

    public function getAbsolutePath()
    {
      return null === $this->image
          ? null
          : $this->getUploadRootDir().'/'.$this->image;
    }
  //---------------------------------------------------
    public function getWebPath()
    {
      return null === $this->image
          ? null
          : $this->getUploadDir().'/'.$this->image;
    }
  //---------------------------------------------------
    protected function getUploadRootDir()
    {
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
  //---------------------------------------------------
    protected function getUploadDir($type='iphone')
    {# the path will be changed to 'uploads/ltf/brands/web'
      return 'uploads/ltf/clothing_type/'.$type;
    }
    //---------------------------------------------------
    public function getImagePaths() {
      $ih = new ImageHelper('clothing_type', $this);
      return $ih->getImagePaths();
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages()
    {
      $ih=new ImageHelper('clothing_type', $this);
      $ih->deleteImages($this->image);
    }
}