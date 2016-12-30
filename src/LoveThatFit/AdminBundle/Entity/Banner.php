<?php


namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\BannerRepository")
 */

class Banner
{

    // ...
    /**
     * One Banner has Many Banners.
     * @ORM\OneToMany(targetEntity="Banner", mappedBy="parent")
     */
    private $children;

    /**
     * Many Banners have One Banner.
     * @ORM\ManyToOne(targetEntity="Banner", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Banner", inversedBy="id")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent_id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image_position;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false)
     */
    protected $banner_type;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload brand logo image!")
     */
    public $file;
    /**
     * @ORM\Column(type="string", length=255)
     */

    protected $display_screen;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    protected $cat_id;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sorting;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $price_min;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $price_max;

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
     * @return Banner
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
     * Set display_screen
     *
     * @param string $display_screen
     * @return Banner
     */
    public function setDisplayScreen($display_screen)
    {
        $this->display_screen = $display_screen;

        return $this;
    }

    /**
     * Get display_screen
     *
     * @return string
     */
    public function getDisplayScreen()
    {
        return $this->display_screen;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Banner
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
     * @return Banner
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
     * @return Banner
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

        array_push($msg_array, array('valid'=>$valid));

        return $msg_array;
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
     * Set image_position
     *
     * @param string $image_position
     * @return Brand
     */
    public function setImagePosition($image_position) {
        $this->image_position = $image_position;

        return $this;
    }

    /**
     * Get image_position
     *
     * @return string
     */
    public function getImagePosition() {
        return $this->image_position;
    }



    /**
     * Set banner_type
     *
     * @param string $banner_type
     * @return Brand
     */
    public function setBannerType($banner_type) {
        $this->banner_type = $banner_type;

        return $this;
    }

    /**
     * Get banner_type
     *
     * @return string
     */
    public function getBannerType() {
        return $this->banner_type;
    }


    /**
     * Set description
     *
     * @param string $description
     * @return Banner
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get cat_id
     *
     * @return string
     */
    public function getCatId()
    {
        return $this->cat_id;
    }

    /**
     * Set cat_id
     *
     * @param float $cat_id
     * @return Banner
     */
    public function setCatId($cat_id)
    {
        $this->cat_id = $cat_id;

        return $this;
    }


    /**
     * Get parent_id
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set parent_id
     *
     * @param float $parent_id
     * @return Banner
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }


    /**
     * Get sorting
     *
     * @return integer
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return Banner
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }


    /**
     * Get price_min
     *
     * @return float
     */
    public function getPriceMin()
    {
        return $this->price_min;
    }

    /**
     * Set price_min
     *
     * @param float $price_min
     * @return Categories
     */
    public function setPriceMin($price_min)
    {
        $this->price_min = $price_min;

        return $this;
    }

    /**
     * Get price_max
     *
     * @return float
     */
    public function getPriceMax()
    {
        return $this->price_max;
    }

    /**
     * Set price_max
     *
     * @param float $price_max
     * @return Categories
     */
    public function setPriceMax($price_max)
    {
        $this->price_max = $price_max;

        return $this;
    }
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        $ih=new ImageHelper('banner', $this);
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
        return 'uploads/ltf/banner/'.$type;
    }
    //---------------------------------------------------
    public function getImagePaths() {
        $ih = new ImageHelper('banner', $this);
        return $ih->getImagePaths();
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages()
    {
        $ih=new ImageHelper('banner', $this);
        $ih->deleteImages($this->image);
    }
}