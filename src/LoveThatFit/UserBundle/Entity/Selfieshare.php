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
     * @ORM\OneToMany(targetEntity="SelfieshareFeedback", mappedBy="selfieshare", orphanRemoval=true)
     */
    protected $selfieshare_feedback;

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="selfieshare" , cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE" )
     *  */
    private $user;
    
    
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
     * @var string $comments
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private $comments;
    /**
     * @var integer $rating
     *
     * @ORM\Column(name="rating", type="integer", nullable=true)     
     */
    private $rating;
     /**
     * @var boolean $favourite
     *
     * @ORM\Column(name="favourite", type="boolean", nullable=true)
     */
    private $favourite;
/**
     * @var string $message
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;
 
     /**
     * @var string $friend_name
     *
     * @ORM\Column(name="friend_name", type="string", length=60, nullable=true)
     */
    private $friend_name;
     /**
     * @var string $friend_email
     *
     * @ORM\Column(name="friend_email", type="string", length=60, nullable=true)
     */
    private $friend_email;
     /**
     * @var string $friend_phone
     *
     * @ORM\Column(name="friend_phone", type="string", length=60, nullable=true)
     */
    private $friend_phone;
     
   /**
     * @var string $ref
     *
     * @ORM\Column(name="ref", type="string", length=60, nullable=true)
     */
    private $ref;
     /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;
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

 #------------------------------------------------   message
         /**
     * Set message
     *
     * @param string $message
     * @return Selfieshare
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage() {
        return $this->message;
    }
    
    
 #------------------------------------------------   comments
         /**
     * Set comments
     *
     * @param string $comments
     * @return Selfieshare
     */
    public function setComments($comments) {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments() {
        return $this->comments;
    }
  #------------------------------------------------   rating
       

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Selfieshare
     */
    public function setRating($rating) {
        $this->rating = $rating != null ? $rating : 0;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating() {
        return $this->rating != null ? $this->rating : 0;
    }
    #------------------------------------------------   like

       /**
     * Set favourite
     *
     * @param boolean $favourite
     * @return Selfieshare
     */
    public function setFavourite($favourite) {
        $this->favourite = $favourite;
        return $this;
    }

    /**
     * Get favourite
     *
     * @return boolean 
     */
    public function getFavourite() {
        return $this->favourite;
    }     
#------------------------------------------------   name
        /**
     * Set friend_name
     *
     * @param string $friend_name
     * @return Selfieshare
     */
    public function setFriendName($friend_name) {
        $this->friend_name = $friend_name;

        return $this;
    }

    /**
     * Get friend_name
     *
     * @return string 
     */
    public function getFriendName() {
        return $this->friend_name;
    }
    #------------------------------------------------   email
    /**
     * Set friend_email
     *
     * @param string $friend_email
     * @return Selfieshare
     */
    public function setFriendEmail($friend_email) {
        $this->friend_email = $friend_email;

        return $this;
    }

    /**
     * Get friend_email
     *
     * @return string 
     */
    public function getFriendEmail() {
        return $this->friend_email;
    }

    #------------------------------------------------   phone
         /**
     * Set friend_phone
     *
     * @param string $friend_phone
     * @return Selfieshare
     */
    public function setFriendPhone($friend_phone) {
        $this->friend_phone = $friend_phone;

        return $this;
    }

    /**
     * Get friend_phone
     *
     * @return string 
     */
    public function getFriendPhone() {
        return $this->friend_phone;
    }
    #------------------------------------------------   ref
             /**
     * Set ref
     *
     * @param string $ref
     * @return Selfieshare
     */
    public function setRef($ref) {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef() {
        return $this->ref;
    }
    #------------------------------------------------   
   
    /**
     * Set updatedAt
     *
     * @param datetime $updated_at
     * @return Selfieshare
     */
    public function setUpdatedAt(\dateTime $updated_at) {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }
    #-----------------------------------------------------------

    /**
     * Add selfieshare_feedback
     *
     * @param \LoveThatFit\UserBundle\Entity\SelfieshareFeedback $selfieshare_feedback
     * @return User
     */
    public function addSelfieshare(\LoveThatFit\UserBundle\Entity\SelfieshareFeedback $selfieshare_feedback)
    {
        $this->selfieshare_feedback[] = $selfieshare_feedback;
        return $this;
    }

    /**
     * Remove selfieshare_feedback
     *
     * @param \LoveThatFit\UserBundle\Entity\SelfieshareFeedback $selfieshare_feedback
     */
    public function removeSelfieshare(\LoveThatFit\UserBundle\Entity\SelfieshareFeedback $selfieshare_feedback)
    {
        $this->selfieshare_feedback->removeElement($selfieshare_feedback);
    }

    /**
     * Get selfieshare_feedback
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSelfieshare()
    {
        return $this->selfieshare_feedback;
    }
#---------------------------------------------------------------------------------
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