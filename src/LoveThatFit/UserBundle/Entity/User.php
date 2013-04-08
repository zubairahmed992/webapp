<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\User
 *  
 * @ORM\Table(name="ltf_users")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserRepository")
 */

class User  implements UserInterface, \Serializable{

    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToOne(targetEntity="Measurement", mappedBy="user", cascade={"ALL"}, orphanRemoval=true)
     **/
    private $measurement;
    

//---------------------------------------------------------------------

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=40)       
     *      @Assert\Length(
     *      min = "6",
     *      max = "50",
     *      minMessage = "Password must be at least {{ limit }} characters long",
     *      maxMessage = "Password cannot be longer than than {{ limit }} characters long",
     *      groups={"registration_step_one"}
     * )
     *      @Assert\NotBlank(groups={"registration_step_one"})
     */
    private $password;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true, nullable=false)
     * @Assert\Email(groups={"registration_step_one"})
     * @Assert\NotBlank(groups={"registration_step_one"})
     */
    private $email;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = "3",
     *      max = "50",
     *      minMessage = "First Name must be at least {{ limit }} characters long",
     *      maxMessage = "First Name cannot be longer than than {{ limit }} characters long" , 
     *      groups={"profile_settings"}   
     * )
     * @Assert\NotBlank(groups={"profile_settings"})  
     * @Assert\Regex(pattern= "/[a-zA-Z]+$/",message="Only Character Require",
     * groups={"profile_settings"}
     * ) 
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     * 
     * @Assert\Length(
     *      min = "3",
     *      max = "50",
     *      minMessage = "Last Name must be at least {{ limit }} characters long",
     *      maxMessage = "Last Name cannot be longer than than {{ limit }} characters long"   ,  
     *      groups={"profile_settings"}   
     * )
     * @Assert\NotBlank(groups={"profile_settings"}) 
     * @Assert\Regex(pattern= "/[a-zA-Z]+$/",message="Only Character Require",
     * groups={"profile_settings"}
     * )  
     */
    private $lastName;

    /**
     * @var string $gender
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     * @Assert\NotBlank(groups={"profile_settings"})  
     */
    private $gender;

    /**
     * @var datetime $birthDate
     *
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     * @Assert\NotBlank(groups={"profile_settings"})  
     */
    private $birthDate;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
    * )
     */
    private $image;

    /**
     * @var string $avatar
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;

    
    /**
     * @var dateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var dateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

     /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    

      /**
     * @var string $authToken
     *
     * @ORM\Column(name="auth_token", type="string", length=50, nullable=true)
     * 
     */
    private $authToken;
    
    /**
     * @var dateTime $authTokenCreatedAt
     *
     * @ORM\Column(name="auth_token_created_at", type="datetime", nullable=true)
     */
    private $authTokenCreatedAt;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

  
    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
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

    /**
     * Set birthDate
     *
     * @param datetime $birthDate
     * @return User
     */
    public function setBirthDate(\dateTime $birthDate) {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return datetime 
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return User
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
     * Set avatar
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Get avatar
     * @return string 
     */
    public function getAvatar() {
        return $this->avatar;
    }

    
    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return User
     */
    public function setCreatedAt(\dateTime $createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     * @return User
     */
    public function setUpdatedAt(\dateTime $updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

 /**
     * Get authToken
     *
     * @return string 
     */
    public function getAuthToken() {
        return $this->authToken;
    }

/**
     * Set authToken
     *
     * @param string $authToken
     * @return User
     */
    public function setAuthToken($authToken) {
        $this->authToken = $authToken;

        return $this;
    }

    
      /**
     * Set authTokenCreatedAt
     *
     * @param datetime $authTokenCreatedAt
     * @return User
     */
    public function setAuthTokenCreatedAt(\dateTime $authTokenCreatedAt) {
        $this->authTokenCreatedAt = $authTokenCreatedAt;

        return $this;
    }



/**
     * Get authTokenCreatedAt
     *
     * @return datetime 
     */
    public function getAuthTokenCreatedAt() {
        return $this->authTokenCreatedAt;
    }

    
    
    
//----------------------- Old password field used for resetting password only
    
    public $old_password;
    
    public function getOldpassword() {
       return $this->old_password;
    }

//---------------------------------------  implement the UserInterface
    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
                    $this->id,
                ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                ) = unserialize($serialized);
    }

    
    /**
     * Set measurement
     *
     * @param LoveThatFit\UserBundle\Entity\Measurement $measurement
     * @return User
     */
    public function setMeasurement(\LoveThatFit\UserBundle\Entity\Measurement $measurement = null)
    {
        $this->measurement = $measurement;
    
        return $this;
    }

    /**
     * Get measurement
     *
     * @return LoveThatFit\UserBundle\Entity\Measurement 
     */
    public function getMeasurement()
    {
        return $this->measurement;
    }
    //--------------------- Public methods --------------------------
    
    public function getAge(){
    if($this->birthDate)
    {
        $birthDate = $this->birthDate->format('d/m/Y');
        $birthDate = explode("/", $birthDate);
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[2])-1):(date("Y")-$birthDate[2]));
        return $age;   
    }
    else
    {
        return "";
    }
    }
            


    public function generateAuthenticationToken()
    {
        $this->authTokenCreatedAt= new \DateTime('now');
        $this->authToken = md5($this->salt.$this->email.$this->authTokenCreatedAt->format('r'));
        return $this->authToken;
    }

    

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    
    
    public function upload() {
        
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        //$unique_number=uniqid();
   
        $this->image = $this->id .'_cropped.'. $ext;
        $original_name = $this->id . '_original.'. $ext;        
        $this->file->move(
                $this->getUploadRootDir(), $this->image
        );
        
        $this->file = null;             
        copy($this->getAbsolutePath(),$this->getUploadRootDir().'/'.$original_name);
        
    }
    //----------------------------------------------------
       public function uploadTempImage() {
        
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        $temp_name = $this->id . '_temp.'. $ext;        
        $this->file->move(
                $this->getUploadRootDir(), $temp_name
        );
        
        $this->file = null;             
        return $temp_name;
        
    }
    
    //------------------------- avatar upload ---------------------------
   
     public function uploadAvatar() {
        
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->avatar = $this->id .'_avatar.'. $ext;
        $this->file->move(
                $this->getUploadRootDir(), $this->avatar
        );
        
        $this->file = null;             
    }
    
    //----------------------------------------------------------
  public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->image;
    }
//----------------------------------------------------------
    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->image;
    }
    //----------------------------------------------------------
      public function getDirWebPath()
    {
        return $this->getUploadDir().'/';
    }
//----------------------------------------------------------
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
//----------------------------------------------------------
    protected function getUploadDir()
    {
        return 'uploads/ltf/users/'.$this->id;
    }
//----------------------------------------------------------
    public function getOriginalImageWebPath()
    {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'. '_ltf_user_'.'.'.$ext;    
        
        
    }
    
    //----------------------------------------------------------
    public function getAvatarWebPath()
    {
        $ext = pathinfo($this->avatar, PATHINFO_EXTENSION);
        return null === $this->avatar
            ? null
            : $this->getUploadDir().'/'.$this->id. '_avatar'.'.'.$ext;    
        
    }

    
    
}