<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\BrandRepository")
 * @ORM\Table(name="brand")
 * @ORM\HasLifecycleCallbacks()
 */
class Brand {
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="brand")
     */
     
     /**
     * @ORM\OneToMany(targetEntity="SizeChart", mappedBy="brand")
     */
    protected $sizechart;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Brand name!")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $image;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload brand logo image!") 
     */
    public $file;

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
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Brand
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
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

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Brand
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
     * @return Brand
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

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
       $ih=new ImageHelper('brand', $this);
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
    protected function getUploadDir()
    {
        return 'uploads/ltf/brands';
    }
    //---------------------------------------------------
    
 /**
 * @ORM\PostRemove
 */
public function deleteImages()
{
     $ih=new ImageHelper('brand', $this);
     $ih->deleteImages($this->image);
}

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizechart = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    /**
     * Add sizechart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $sizechart
     * @return Brand
     */
    public function addSizechart(\LoveThatFit\AdminBundle\Entity\SizeChart $sizechart)
    {
        $this->sizechart[] = $sizechart;
    
        return $this;
    }

    

    /**
     * Get sizechart
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSizechart()
    {
        return $this->sizechart;
    }

   

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Brand
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

    /**
     * Remove sizechart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $sizechart
     */
    public function removeSizechart(\LoveThatFit\AdminBundle\Entity\SizeChart $sizechart)
    {
        $this->sizechart->removeElement($sizechart);
    }
}