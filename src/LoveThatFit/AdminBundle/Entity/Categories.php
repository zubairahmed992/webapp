<?php


namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\CategoriesRepository")
 */

class Categories
{

    // ...
    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="Categories", mappedBy="parent")
     */
    private $children;
    
    /**
     * @ORM\ManyToMany(targetEntity="LoveThatFit\AdminBundle\Entity\Product", inversedBy="categories")
     * @ORM\JoinTable(name="category_products")
     * */
    private $category_products;

    
    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    // ...

    public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Categories", mappedBy="parent_id")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="id")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent_id;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;


    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload brand logo image!")
     */
    public $file;



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
     * @var integer $top_id
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $top_id;

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
     * @return Categories
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Categories
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
     * @return Categories
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
     * @return Categories
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
     * Set gender
     *
     * @param string $gender
     * @return Categories
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


    /**
     * Set Topid
     *
     * @param integer $topid
     * @return topod
     */
    public function setTopId($topid) {
        $this->top_id = $image;

        return $this;
    }

    /**
     * Get Topid
     *
     * @return string
     */
    public function getTopId() {
        return $this->top_id;
    }


    /**
     * Set $parent_id
     *
     * @param int $parent_id
     * @return int
     */
    public function setParentId($parent_id = 0) {
        $this->parent_id = $parent_id;
        return $this;
    }

    /**
     * Get parent_id
     *
     * @return int
     */
    public function getParentId() {
        return $this->parent_id;
    }

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        $ih=new ImageHelper('categories', $this);
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
        return 'uploads/ltf/categories/'.$type;
    }
    //---------------------------------------------------
    public function getImagePaths() {
        $ih = new ImageHelper('categories', $this);
        return $ih->getImagePaths();
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages()
    {
        $ih=new ImageHelper('categories', $this);
        $ih->deleteImages($this->image);
    }

}