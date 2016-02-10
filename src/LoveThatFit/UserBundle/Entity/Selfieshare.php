<?php

namespace LoveThatFit\UserBundle\Entity;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LoveThatFit\UserBundle\Entity\Selfieshare
 *  
 * @ORM\Table(name="selfieshare")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\SelfieshareRepository")
 */
class Selfieshare  {

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="selfieshare" , cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE" )
     *  */
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="SelfieshareFeedback", mappedBy="selfieshare", orphanRemoval=true)
     */
    protected $selfieshare_feedback;  
    
    /**
     * @ORM\OneToMany(targetEntity="SelfieshareItem", mappedBy="selfieshare", orphanRemoval=true)
     */
    protected $selfieshare_item;  

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
     /**
     * @var string $device_type
     *
     * @ORM\Column(name="device_type", type="string", length=60, nullable=true)
     */
    private $device_type;
    
     /**
     * @var string $image
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
    */
    private $image; 
    
    /**
     * @var dateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;
    /**
     * @Assert\File()
     */
    public $file;
#------------------------------------
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
#------------------------------------
    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return Selfieshare
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
#------------------------------------
  

    /**
     * Set device_type
     *
     * @param string $device_type
     * @return Selfieshare
     */
    public function setDeviceType($device_type)
    {
        $this->device_type = $device_type;    
        return $this;
    }

    /**
     * Get device_type
     *
     * @return string 
     */
    public function getDeviceType()
    {
        return $this->device_type;
    }

  #------------------------------------

    /**
     * Set image
     *
     * @param string $image
     * @return Selfieshare
     */
    public function setImage($image)
    {
        $this->image = $image;    
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }
#--------------------------------------------
    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return Selfieshare
     */
    
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    
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

 
    #-----------------------------------------------------------
    
   
     public function upload() {

      if (null === $this->file) {
          return;
      }
      
      $ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
      $this->image = 'selfieshare'.substr(uniqid(),0,10) .'.'. $ext;      
            
      if (!is_dir($this->getUploadRootDir())) {
                @mkdir($this->getUploadRootDir(), 0700);
            }
        move_uploaded_file($this->file["tmp_name"], $this->getAbsolutePath());
      #$this->file->move($this->getUploadRootDir(), $this->image);
      
      $this->file = null;    
      return $this->image;
    } 
    
     //----------------------------------------------------------
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

//----------------------------------------------------------
    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

    //----------------------------------------------------------
    public function getDirWebPath() {
        return $this->getUploadDir() . '/';
    }

//----------------------------------------------------------
    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

//----------------------------------------------------------
    public function getUploadDir() {
        $user=$this->getUser();
        return 'uploads/ltf/users/' . $user->getId();
    }
#----------------------------------------------------
    public function toArray() {        
            return array(
                'id' => $this->id,
                'device_type' => $this->device_type,
                'image' => $this->image,
                'created_at' => $this->created_at,                
            );        
    }
   
}