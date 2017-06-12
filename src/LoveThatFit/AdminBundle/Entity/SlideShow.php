<?php


namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="slide_show")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SlideShowRepository")
 */

class SlideShow
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload slide show image!")
     */
    public $file;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $button_title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $button_action;

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
     * Set title
     *
     * @param string $title
     * @return SlideShow
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return SlideShow
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
     * @return SlideShow
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
     * @return SlideShow
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
        if($this->title==''){
            array_push($msg_array, array('valid'=>false, 'message'=>'Invalid title'));
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
     * Set description
     *
     * @param string $description
     * @return SlideShow
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
     * @return SlideShow
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Set button_title
     *
     * @param string $button_title
     * @return SlideShow
     */
    public function setButtonTitle($button_title)
    {
        $this->button_title = $button_title;

        return $this;
    }

    /**
     * Get button_title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->button_title;
    }

    /**
     * Set button_action
     *
     * @param string $button_action
     * @return SlideShow
     */
    public function setButtonAction($button_action)
    {
        $this->button_action = $button_action;

        return $this;
    }

    /**
     * Get button_action
     *
     * @return string
     */
    public function getButtonAction()
    {
        return $this->button_action;
    }
    
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        $ih=new ImageHelper('slide_show', $this);
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
        return 'uploads/ltf/slide_show/'.$type;
    }
    //---------------------------------------------------
    public function getImagePaths() {
        $ih = new ImageHelper('slide_show', $this);
        return $ih->getImagePaths();
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages()
    {
        $ih=new ImageHelper('slide_show', $this);
        $ih->deleteImages($this->image);
    }
}