<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
/**
 * LoveThatFit\UserBundle\Entity\User
 *  
 * @ORM\Table(name="ltf_users")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable {

    // ...
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="original_user")
     */
    private $duplicate_users;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\SaveLook", mappedBy="ltf_users", orphanRemoval=true)
     */

    protected $save_look;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\FNFUser", mappedBy="users", orphanRemoval=true)
     */

    protected $fnfusers;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="duplicate_users")
     * @ORM\JoinColumn(name="original_user_id", referencedColumnName="id", nullable=true)
     */
    private $original_user;
    #------------------------------------------------------------------
     /**
     * Set original
     *
     * @param LoveThatFit\UserBundle\Entity\User $original_user
     * @return User
     */
    public function setOriginalUser(\LoveThatFit\UserBundle\Entity\User $original_user = null) {
        $this->original_user = $original_user;        
        return $this;
    }

    /**
     * Get original_user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getOriginalUser() {
        return $this->original_user;
    }

    #------------------------------------------------------------------
    
       /**
     * Add duplicate_users
     *
     * @param LoveThatFit\UserBundle\Entity\User  $duplicate_users
     * @return User
     */
    public function addDuplicateUser($duplicate_user) {
        $this->duplicate_users[] = $duplicate_user;

        return $this;
    }

    /**
     * Remove duplicate_users
     *
     * @param LoveThatFit\UserBundle\Entity\User  $duplicate_user
     */
    public function removeDuplicateUser($duplicate_user) {
        $this->duplicate_users->removeElement($duplicate_user);
    }

    /**
     * Get duplicate_users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDuplicateUsers() {
        return $this->duplicate_users;
    } 
    #--------------------------------------------
    
   public $isApproved;
    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToOne(targetEntity="Measurement", mappedBy="user", cascade={"ALL"}, orphanRemoval=true)
     * */
    private $measurement;

	/**
	 * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\Cart", mappedBy="user")
	 */
	private $cart;


	/**
	 * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\Wishlist", mappedBy="user")
	 */
	private $wishlist;

	/**
	 * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\UserAddresses", mappedBy="user")
	 */
	private $user_addresses;

	/**
	 * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\UserOrder", mappedBy="user")
	 */
	private $user_orders;

    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToOne(targetEntity="UserParentChildLink", mappedBy="child", cascade={"ALL"}, orphanRemoval=true)
     * */
    private $userparentchildlink;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\SurveyUser", mappedBy="user")
     */
    protected $survey;
    
    
     /**
     * @ORM\OneToMany(targetEntity="UserDevices", mappedBy="user", orphanRemoval=true)
     */
    protected $user_devices;
    
      /**
     * @ORM\OneToMany(targetEntity="UserArchives", mappedBy="user", orphanRemoval=true)
     */
    protected $user_archives;
    
       /**
     * @ORM\OneToMany(targetEntity="Selfieshare", mappedBy="user", orphanRemoval=true)
     */
    protected $selfieshare;

    /**
     * @ORM\OneToMany(targetEntity="UserFeedback", mappedBy="user", orphanRemoval=true)
     */
    protected $user_feedback;

    // ...

    /**
     * @ORM\ManyToMany(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="users")
     * @ORM\JoinTable(name="users_product_items")
     * */
    private $product_items;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserFittingRoomItem", mappedBy="User")
     */
    private $useritemtryhistory;


    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemFavHistory", mappedBy="User")
     */
    private $user_item_fav_history;
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserFittingRoomItem", mappedBy="User")
     */
    private $userfittingroomitem;
    
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\RetailerSiteUser", mappedBy="user")
     */
    private $retailer_site_users;
    

    /**
     * @ORM\OneToOne(targetEntity="UserMarker", mappedBy="user")
     * */
    private $user_marker; 
    
     /**
     * @ORM\OneToOne(targetEntity="UserImageSpec", mappedBy="user")
     * */
    private $user_image_spec;

	/**
	 * @ORM\OneToOne(targetEntity="UserAppAccessLog", mappedBy="user")
	 * */
	private $user_app_access_log;


//---------------------------------------  implement the UserInterface
    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->product_items = new \Doctrine\Common\Collections\ArrayCollection();
	    $this->cart = new \Doctrine\Common\Collections\ArrayCollection();
        $this->duplicate = new \Doctrine\Common\Collections\ArrayCollection();
        $this->save_look = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fnfusers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     *      minMessage = "Password must contain at least 8 characters, including one number, one upper-case and one lower-case alphabet.",
     *      maxMessage = "Password cannot be longer than than {{ limit }} characters long",
     *      groups={"registration_step_one"}
     * )
     *      @Assert\NotBlank(groups={"registration_step_one"}, message="Password cannot be blank")
     */
    private $password;

    /**
     * @var string $pwd
     *
     * @ORM\Column(name="pwd", type="string", length=40, nullable=true)       
     *      
     */
    private $pwd;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true, nullable=false)
     * @Assert\Email(groups={"registration_step_one"}, message="Please provide a valid email")
     * @Assert\NotBlank(groups={"registration_step_one"}, message="Email cannot be blank")
     */
    private $email;

    /**
     * @var string $zipcode
     *
     * @ORM\Column(name="zipcode", type="string", length=60,nullable=false)
     * @Assert\NotBlank(groups={"registration_step_one"}, message="Zip code cannot be blank")
     */
    private $zipcode;

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
     * @Assert\NotBlank(groups={"registration_step_one","profile_settings"}, message="Choose a valid gender")  
     */
    private $gender;



    /**
     * @var string $phoneNumber
     * @ORM\Column(name="phone_number", type="string", length=100, nullable=true)

     */
    private $phoneNumber;

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
     * @var string $image_device_type
     *
     * @ORM\Column(name="image_device_type", type="string", length=255, nullable=true)
     * )
     */
    private $image_device_type;

    /**
     * @var string $image_device_model
     *
     * @ORM\Column(name="image_device_model", type="string", length=255, nullable=true)
     * )
     */
    private $image_device_model;
    
    /**
     * @var string $image_updated_at
     *
     * @ORM\Column(name="image_updated_at", type="datetime", nullable=true)
     * )
     */
    private $image_updated_at;
    
     /**
     * @var string $device_tokens
     *
     * @ORM\Column(name="device_tokens", type="text", nullable=true)
     * )
     */
    private $device_tokens;
    

    /**
     * @var string $avatar
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)

     */
    private $avatar;
    
    /**
     * @var string $iphoneImage
     * @ORM\Column(name="iphoneImage", type="string", length=255, nullable=true)
    */
    private $iphoneImage;


    /**
     * @var string $release_name
     *
     * @ORM\Column(name="release_name", type="text", nullable=true)
     * )
     */
    private $release_name;

    /**
     * @var string $event_name
     *
     * @ORM\Column(name="event_name", type="string", length=255, nullable=true)
     * )
     */
    private $event_name;

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
     * @Assert\File()
     */
    public $file;
    public $temp_image;

    /**
     * @var string $authToken
     *
     * @ORM\Column(name="auth_token", type="string", length=50, nullable=true)
     * 
     */
    private $authToken;

    /**
     * @var string $authTokenWebService
     *
     * @ORM\Column(name="auth_token_web_service", type="string", length=50, nullable=true)
     * 
     */
    private $authTokenWebService;

    /**
     * @var dateTime $authTokenCreatedAt
     *
     * @ORM\Column(name="auth_token_created_at", type="datetime", nullable=true)
     */
    private $authTokenCreatedAt;
    
            
    /**
     * @var string $secretQuestion
     *
     * @ORM\Column(name="secret_question", type="string", length=50, nullable=true)         
     */
    private $secretQuestion;
    
    
    /**
     * @var string $secretAnswer
     *
     * @ORM\Column(name="secret_answer", type="string", length=50, nullable=true)  
     */
    private $secretAnswer;
    
    
    /**
     * @var dateTime $timeSpent
     *
     * @ORM\Column(name="time_spent", type="string", nullable=true)
     */
    private $timeSpent;
    
     /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer", nullable=true,options={"default" = 0})
       
     */
    private $status;


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
     * Set pwd
     *
     * @param string $pwd
     * @return User
     */
    public function setPwd($pwd) {
        $this->pwd = $pwd;

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

    public function getFullName() {
        return $this->firstName . " " . $this->lastName;
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
    public function setBirthDate($birthDate) {
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
#-------------------------------------------
        /**
     * Set image_device_type
     *
     * @param string $image_device_type
     * @return User
     */
    public function setImageDeviceType($image_device_type) {
        $this->image_device_type = $image_device_type;

        return $this;
    }

    /**
     * Get image_device_type
     *
     * @return string 
     */
    public function getImageDeviceType() {
        return $this->image_device_type;
    }
    #-------------------------------------------
        /**
     * Set image_device_model
     *
     * @param string $image_device_model
     * @return User
     */
    public function setImageDeviceModel($image_device_model) {
        $this->image_device_model = $image_device_model;

        return $this;
    }

    /**
     * Get image_device_model
     *
     * @return string 
     */
    public function getImageDeviceModel() {
        return $this->image_device_model;
    }
    public function extractImageDeviceModel() {
        return $this->image_device_model ? $this->image_device_model : $this->image_device_type;
    }

    #-------------------------------------------
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

    #-------------------------------------------
    /**
     * Set phoneNumber
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Get avatar
     * @return string
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
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
    public function getPwd() {
        return $this->pwd;
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
                    $this->email,
                ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->email,
                ) = unserialize($serialized);
    }

    /**
     * Set measurement
     *
     * @param LoveThatFit\UserBundle\Entity\Measurement $measurement
     * @return User
     */
    public function setMeasurement(\LoveThatFit\UserBundle\Entity\Measurement $measurement = null) {
        $this->measurement = $measurement;
        $measurement->setUser($this);
        return $this;
    }

    /**
     * Get measurement
     *
     * @return LoveThatFit\UserBundle\Entity\Measurement 
     */
    public function getMeasurement() {
        return $this->measurement;
    }
     public function getMeasurementArray() {
        return $this->measurement->getArray();
    }

    /**
     * Add survey
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyUser $survey
     * @return User
     */
    public function addSurvey(\LoveThatFit\AdminBundle\Entity\SurveyUser $survey) {
        $this->survey[] = $survey;

        return $this;
    }

    /**
     * Remove survey
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyUser $survey
     */
    public function removeSurvey(\LoveThatFit\AdminBundle\Entity\SurveyUser $survey) {
        $this->survey->removeElement($survey);
    }

    /**
     * Get survey
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSurvey() {
        return $this->survey;
    }

    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return User
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items[] = $productItems;

        return $this;
    }

    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items->removeElement($productItems);
    }

    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems() {
        return $this->product_items;
    }
#-----------------------------------------
    /**
     * Set device_tokens
     *
     * @param string $device_tokens
     * @return User
     */
    public function setDeviceTokens($device_tokens) {
        $this->device_tokens = $device_tokens;

        return $this;
    }

    /**
     * Get device_tokens
     *
     * @return string 
     */
    public function getDeviceTokens() {
        return $this->device_tokens;
    }
#-----------------------------------------

#-----------------------------------------
  /**
   * Set release_name
   *
   * @param string $release_name
   * @return User
   */
  public function setReleaseName($release_name) {
    $this->release_name = $release_name;

    return $this;
  }

  /**
   * Get release_name
   *
   * @return string
   */
  public function getReleaseName() {
    return $this->release_name;
  }
  
  #-----------------------------------------

  #-----------------------------------------
   /**
   * Set event_name
   *
   * @param string $event_name
   * @return User
   */
  public function setEventName($event_name) {
    $this->event_name = $event_name;

    return $this;
  }

  /**
   * Get event_name
   *
   * @return string
   */
  public function getEventName() {
    return $this->event_name;
  }


#-----------------------------------------
    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return User
     */
    public function setZipcode($zipcode) {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode() {
        return $this->zipcode;
    }
#-----------------------------------------
       /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return User
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

#-----------------------------------------    
    public function getMyClosetListArray($product_item_id) {
        $productitem = $this->getProductItems();
        foreach ($productitem as $ps) {
            if ($ps->getId() == $product_item_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add useritemtryhistory
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $useritemtryhistory
     * @return User
     */
    public function addUseritemtryhistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $useritemtryhistory) {
        $this->useritemtryhistory[] = $useritemtryhistory;

        return $this;
    }

    /**
     * Remove useritemtryhistory
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $useritemtryhistory
     */
    public function removeUseritemtryhistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $useritemtryhistory) {
        $this->useritemtryhistory->removeElement($useritemtryhistory);
    }

    /**
     * Get useritemtryhistory
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUseritemtryhistory() {
        return $this->useritemtryhistory;
    }

    /**
     * Set authTokenWebService
     *
     * @param string $authTokenWebService
     * @return User
     */
    public function setAuthTokenWebService($authTokenWebService) {
        $this->authTokenWebService = $authTokenWebService;

        return $this;
    }

    /**
     * Get authTokenWebService
     *
     * @return string 
     */
    public function getAuthTokenWebService() {
        return $this->authTokenWebService;
    }

//--------------------- Public methods --------------------------

    public function getAge() {
        if ($this->birthDate) {
            $birthDate = $this->birthDate->format('d/m/Y');
            $birthDate = explode("/", $birthDate);
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
            return $age;
        } else {
            return "";
        }
    }
    
    //-------------------------------------------------
    public function getAccuracyIndicator() {
        $accuracy=0;
        if($this->firstName)
        {
            $accuracy=$accuracy+1;
        }
        if($this->lastName)
        {
            $accuracy=$accuracy+1;
        }
        if($this->email)
        {
            $accuracy=$accuracy+1;
        }
        if($this->zipcode)
        {
            $accuracy=$accuracy+1;
        }
        if($this->image)
        {
            $accuracy=$accuracy+1;
        }
        if($this->avatar)
        {
            $accuracy=$accuracy+1;
        }
        if($this->secretQuestion)
        {
            $accuracy=$accuracy+1;
        }
        if($this->secretAnswer)
        {
            $accuracy=$accuracy+1;
        }
        if ($this->birthDate) {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getWeight())
             {
            $accuracy=$accuracy+1;
        } 
         if($this->measurement->getHeight())
             {
            $accuracy=$accuracy+1;
        } 
         
        if($this->measurement->getThigh())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBodyTypes())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBodyShape())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getInseam())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getOutseam())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getNeck())
             {
            $accuracy=$accuracy+1;
        } if($this->measurement->getSleeve())
             {
            $accuracy=$accuracy+1;
        } if($this->measurement->getChest())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getShoulderAcrossFront())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getShoulderAcrossBack())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBicep())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getTricep())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getWrist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getCenterFrontWaist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBackWaist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getWaistHip())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getKnee())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getCalf())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getAnkle())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBrasize())
             {
            $accuracy=$accuracy+1;
        } 
        return round(($accuracy/38)*100,2);
    }
    //-------------------------------------------------
    public function getMeasurementStatistics()
    {
        $accuracy=0;        
        if($this->measurement->getWeight())
             {
            $accuracy=$accuracy+1;
        } 
         if($this->measurement->getHeight())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBustHeight())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getThigh())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBodyTypes())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBodyShape())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getInseam())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getOutseam())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getNeck())
             {
            $accuracy=$accuracy+1;
        } if($this->measurement->getSleeve())
             {
            $accuracy=$accuracy+1;
        } if($this->measurement->getChest())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getShoulderAcrossFront())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getShoulderAcrossBack())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBicep())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getTricep())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getWrist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getCenterFrontWaist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBackWaist())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getWaistHip())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getKnee())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getCalf())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getAnkle())
             {
            $accuracy=$accuracy+1;
        } 
        if($this->measurement->getBrasize())
             {
            $accuracy=$accuracy+1;
        } 
        return round(($accuracy/29)*100,2);
    }
    
    //-------------------------------------------------
    public function generateAuthenticationToken() {
        $this->authTokenCreatedAt = new \DateTime('now');
        $this->authToken = md5($this->salt . $this->email . $this->authTokenCreatedAt->format('r'));
        return $this->authToken;
    }

    //-------------------------------------------------
    //-------------- Image Upload ---------------------


     public function upload() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->image = 'cropped.' . $ext;
        $this->temp_image = 'original.' . $ext;
        $this->file->move(
                $this->getUploadRootDir(), $this->temp_image
        );
        $this->file = null;
        return $this->temp_image;
    }
//----------------------------------------------------
    public function writeImageFromCanvas($raw_data) {
        $data = substr($raw_data, strpos($raw_data, ",") + 1);
        $decodedData = base64_decode($data);
        $fp = fopen($this->getAbsolutePath(), 'wb');
        @fwrite($fp, $decodedData);
        @fclose($fp);
        #$this->copyTempToOriginalImage();
        $cropped_image_url=$this->getWebPath();        
       return json_encode(array("status"=>"check", "url"=>$cropped_image_url));
        
    }
    //----------------------------------------------------
    public function writeImage($raw_data) {
        $data = substr($raw_data, strpos($raw_data, ",") + 1);
        $decodedData = base64_decode($data);        
        $file_name= uniqid() . '.png';
        $abs_path = $this->getUploadRootDir() . '/' . $file_name;        
        $fp = fopen($abs_path, 'wb');
        @fwrite($fp, $decodedData);
        @fclose($fp);      
        return $this->getUploadDir() . '/' . $file_name;        
    }
    //----------------------------------------------------
    public function writeBGCroppedFromCanvas($raw_data) {
        $data = substr($raw_data, strpos($raw_data, ",") + 1);
        $decodedData = base64_decode($data);
        $fp = fopen($this->getBGCroppedImageAbsolutePath(), 'wb');
        @fwrite($fp, $decodedData);
        @fclose($fp);
        $cropped_image_url=$this->getBGCroppedImageWebPath();        
       return json_encode(array("status"=>"check", "url"=>$cropped_image_url));
        
    }
    //----------------------------------------------------
    public function copyDefaultImage($device_type=null) {
        $this->image='cropped.png';
        
        if (!is_dir($this->getUploadRootDir())) {
                    mkdir($this->getUploadRootDir(), 0700);
                }
        copy($this->getDummyUserImageRootPath($device_type), $this->getAbsolutePath());        
    }
    //----------------------------------------------------
    private function copyTempToOriginalImage() {
        @rename($this->getTempImageAbsolutePath(), $this->getOriginalImageAbsolutePath());
    }

//----------------------------------------------------
    public function uploadTempImage() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);

        $temp_name = 'temp.' . $ext;
        $this->file->move(
                $this->getUploadRootDir(), $temp_name
        );

        $this->file = null;
        return $this->getUploadDir() . '/' . $temp_name;
    }

      //------------------------------------------------------    
    public function getTempImageAbsolutePath() {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image ? null : $this->getUploadRootDir() . '/temp.' . $ext;
    }
    
    //----------------------------------------------------------
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

//----------------------------------------------------------
    public function getWebPath($rand=true) {
        if ($rand)
            return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image . '?rand=' . uniqid();
        else
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
    public function getDummyUserImageRootPath($udt=null) {
        if($udt)
            return __DIR__ . '../../../../../web/uploads/ltf/dummy_user/'.$udt.'_'.$this->gender.'_cropped.png';        
        else
            return __DIR__ . '../../../../../web/uploads/ltf/dummy_user/'.$this->gender.'_cropped.png';        
    }
//----------------------------------------------------------
    public function getUploadDir() {
        return 'uploads/ltf/users/' . $this->id;
    }

    //------------------------------------------------------    
    public function getOriginalImageAbsolutePath() {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image ? null : $this->getUploadRootDir() . '/original.' . $ext;
    }

//----------------------------------------------------------
    public function getOriginalImageWebPath() {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image ? null : $this->getUploadDir() . '/original.' . $ext;
    }
  //------------------------------------------------------    
    public function getBGCroppedImageAbsolutePath() {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image ? null : $this->getUploadRootDir() . '/bg_cropped.' . $ext;
    }

//----------------------------------------------------------
    public function getBGCroppedImageWebPath() {
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        return null === $this->image ? null : $this->getUploadDir() . '/bg_cropped.' . $ext;
    }

    //-------------------------------------------------------------------
    //------------------------- Avatar upload ---------------------------

    public function uploadAvatar() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->avatar = 'avatar.' . $ext;
        $this->file->move(
                $this->getUploadRootDir(), $this->avatar
        );
        $filename = $this->getAbsoluteAvatarPath();
        $image_info = getimagesize($filename); //Get the size of an image
        $image_type = $image_info[2];
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filename);  //create jpg image
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filename); //create gif image
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filename); //create png image
                break;
        }
        $img_new = imagecreatetruecolor(220, 250);
        imagealphablending($img_new, false);
        imagesavealpha($img_new, true);
        $transparent = imagecolorallocatealpha($img_new, 255, 255, 255, 127);
        imagefilledrectangle($img_new, 0, 0, 220, 255, $transparent);
        imagecopyresampled($img_new, $source, 0, 0, 0, 0, 220, 250, imagesx($source), imagesy($source));
        $img_path = $this->getUploadRootDir() . '/' . $this->avatar;
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($img_new, $img_path, 75);
                break;
            case IMAGETYPE_GIF:
                imagegif($img_new, $img_path);
                break;
            case IMAGETYPE_PNG:
                imagepng($img_new, $img_path);
                break;
        }
        $this->file = null;
    }

    //----------------------------------------------------------
    public function getAvatarWebPath() {
        $ext = pathinfo($this->avatar, PATHINFO_EXTENSION);
        return null === $this->avatar ? null : $this->getUploadDir() . '/' . 'avatar' . '.' . $ext;
    }

//------------------------------------------------------    
    public function getAbsoluteAvatarPath() {
        return null === $this->avatar ? null : $this->getUploadRootDir() . '/' . $this->avatar;
    }

     //----------------------------------------------------------
    public function getIphoneWebPath() {
        $ext = pathinfo($this->iphoneImage, PATHINFO_EXTENSION);
        return null === $this->iphoneImage ? null : $this->getUploadDir() . '/' . 'iphone' . '.' . $ext;
    }

//------------------------------------------------------    
    public function getAbsoluteIphonePath() {
        return null === $this->iphoneImage ? null : $this->getUploadRootDir() . '/' . $this->iphoneImage;
    }


    /**
     * Set iphoneImage
     *
     * @param string $iphoneImage
     * @return User
     */
    public function setIphoneImage($iphoneImage)
    {
        $this->iphoneImage = $iphoneImage;
    
        return $this;
    }

    /**
     * Get iphoneImage
     *
     * @return string 
     */
    public function getIphoneImage()
    {
        return $this->iphoneImage;
    }

    
    #-----------------------------------------------------
    

    /**
     * Add user_devices
     *
     * @param \LoveThatFit\UserBundle\Entity\UserDevices $userDevices
     * @return User
     */
    public function addUserDevice(\LoveThatFit\UserBundle\Entity\UserDevices $userDevices)
    {
        $this->user_devices[] = $userDevices;
    
        return $this;
    }

    /**
     * Remove user_devices
     *
     * @param \LoveThatFit\UserBundle\Entity\UserDevices $userDevices
     */
    public function removeUserDevice(\LoveThatFit\UserBundle\Entity\UserDevices $userDevices)
    {
        $this->user_devices->removeElement($userDevices);
    }

    /**
     * Get user_devices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserDevices()
    {
        return $this->user_devices;
    }
#---------------------------------------------------------------------------------
  
    /**
     * Add user_archives
     *
     * @param \LoveThatFit\UserBundle\Entity\UserArchives $userArchives
     * @return User
     */
    public function addUserArchives(\LoveThatFit\UserBundle\Entity\UserArchives $userArchives)
    {
        $this->user_archives[] = $userArchives;
    
        return $this;
    }

    /**
     * Remove user_archives
     *
     * @param \LoveThatFit\UserBundle\Entity\UserArchives $userArchives
     */
    public function removeUserArchives(\LoveThatFit\UserBundle\Entity\UserArchives $userArchives)
    {
        $this->user_archives->removeElement($userArchives);
    }

    /**
     * Get user_archives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserArchives()
    {
        return $this->user_archives;
    }
#---------------------------------------------------------------------------------

    /**
     * Add selfieshare
     *
     * @param \LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare
     * @return User
     */
    public function addSelfieshare(\LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare)
    {
        $this->selfieshare[] = $selfieshare;    
        return $this;
    }

    /**
     * Remove selfieshare
     *
     * @param \LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare
     */
    public function removeSelfieshare(\LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare)
    {
        $this->selfieshare->removeElement($selfieshare);
    }

    /**
     * Get selfieshare
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSelfieshare()
    {
        return $this->selfieshare;
    }
#---------------------------------------------------------------------------------    
    /**
     * Add user_feedback
     *
     * @param \LoveThatFit\UserBundle\Entity\UserFeedback $userFeedback
     * @return User
     */
    public function addUserFeedback(\LoveThatFit\UserBundle\Entity\UserFeedback $userFeedback)
    {
        $this->user_feedback[] = $userFeedback;
    
        return $this;
    }

    /**
     * Remove user_feedback
     *
     * @param \LoveThatFit\UserBundle\Entity\UserFeedback $user_feedback
     */
    public function removeUserFeedback(\LoveThatFit\UserBundle\Entity\UserFeedback $userFeedback)
    {
        $this->user_feedback->removeElement($userFeedback);
    }

    /**
     * Get user_feedback
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserFeedback()
    {
        return $this->user_feedback;
    }
    
    #----------------------------------------------------------------------------
    /**
     * Set userparentchildlink
     *
     * @param \LoveThatFit\UserBundle\Entity\UserParentChildLink $userparentchildlink
     * @return User
     */
    public function setUserparentchildlink(\LoveThatFit\UserBundle\Entity\UserParentChildLink $userparentchildlink = null)
    {
        $this->userparentchildlink = $userparentchildlink;
    
        return $this;
    }

    /**
     * Get userparentchildlink
     *
     * @return \LoveThatFit\UserBundle\Entity\UserParentChildLink 
     */
    public function getUserparentchildlink()
    {
        return $this->userparentchildlink;
    }

    
    

    /**
     * Set secretQuestion
     *
     * @param string $secretQuestion
     * @return User
     */
    public function setSecretQuestion($secretQuestion)
    {
        $this->secretQuestion = $secretQuestion;
    
        return $this;
    }

    /**
     * Get secretQuestion
     *
     * @return string 
     */
    public function getSecretQuestion()
    {
        return $this->secretQuestion;
    }

    /**
     * Set secretAnswer
     *
     * @param string $secretAnswer
     * @return User
     */
    public function setSecretAnswer($secretAnswer)
    {
        $this->secretAnswer = $secretAnswer;
    
        return $this;
    }

    /**
     * Get secretAnswer
     *
     * @return string 
     */
    public function getSecretAnswer()
    {
        return $this->secretAnswer;
    }

    /**
     * Add retailer_site_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerSiteUser $retailerSiteUsers
     * @return User
     */
    public function addRetailerSiteUser(\LoveThatFit\AdminBundle\Entity\RetailerSiteUser $retailerSiteUsers)
    {
        $this->retailer_site_users[] = $retailerSiteUsers;
    
        return $this;
    }

    /**
     * Remove retailer_site_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerSiteUser $retailerSiteUsers
     */
    public function removeRetailerSiteUser(\LoveThatFit\AdminBundle\Entity\RetailerSiteUser $retailerSiteUsers)
    {
        $this->retailer_site_users->removeElement($retailerSiteUsers);
    }

    /**
     * Get retailer_site_users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailerSiteUsers()
    {
        return $this->retailer_site_users;
    }

    /**
     * Add userfittingroomitem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userfittingroomitem
     * @return User
     */
    public function addUserfittingroomitem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userfittingroomitem)
    {
        $this->userfittingroomitem[] = $userfittingroomitem;
    
        return $this;
    }

    /**
     * Remove userfittingroomitem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userfittingroomitem
     */
    public function removeUserfittingroomitem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userfittingroomitem)
    {
        $this->userfittingroomitem->removeElement($userfittingroomitem);
    }

    /**
     * Get userfittingroomitem
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserfittingroomitem()
    {
        return $this->userfittingroomitem;
    }

    /**
     * Set user_marker
     *
     * @param \LoveThatFit\UserBundle\Entity\User $userMarker
     * @return User
     */
    public function setUserMarker(\LoveThatFit\UserBundle\Entity\User $userMarker = null)
    {
        $this->user_marker = $userMarker;
    
        return $this;
    }

    /**
     * Get user_marker
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUserMarker()
    {
        return $this->user_marker;
    }
    
    
    /**
     * Set user_marker
     *
     * @param \LoveThatFit\UserBundle\Entity\User $userImageSpec
     * @return User
     */
    public function setUserImageSpec(\LoveThatFit\UserBundle\Entity\User $user_image_spec = null)
    {
        $this->user_image_spec = $user_image_spec;    
        return $this;
    }

    /**
     * Get user_image_spec
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUserImageSpec()
    {
        return $this->user_image_spec;
    }

	/**
	 * Set user_app_access_log
	 *
	 * @param \LoveThatFit\UserBundle\Entity\User $userAppAccessLog
	 * @return User
	 */
	public function setUserAppAccessLog(\LoveThatFit\UserBundle\Entity\User $user_app_access_log = null)
	{
	  $this->user_app_access_log = $user_app_access_log;
	  return $this;
	}

	/**
	 * Get user_app_access_log
	 *
	 * @return \LoveThatFit\UserBundle\Entity\User
	 */
	public function getUserAppAccessLog()
	{
	  return $this->user_app_access_log;
	}


  /**
     * Set timeSpent
     *
     * @param \string $timeSpent
     * @return User
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
    
        return $this;
    }

    /**
     * Get timeSpent
     *
     * @return \string
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }


    #---------------------------------
    public function getDeviceSpecs($device_type = null) {
        $us = $this->getUserDevices();

        if ($device_type == null) {
            if ($us) {
                return $us->first();
            } else {
                return null;
            }
        } else {
            if ($us) {
                foreach ($us as $userdevice) {
                    if (strtolower($device_type) == strtolower($userdevice->getDeviceType())) {
                        return $userdevice;
                    }
                }
            }
            return null;
        }
    }

#---------------------------------------------------
    

    /**
     * Set image_updated_at
     *
     * @param \DateTime $imageUpdatedAt
     * @return User
     */
    public function setImageUpdatedAt($imageUpdatedAt)
    {
        $this->image_updated_at = $imageUpdatedAt;
    
        return $this;
    }

    /**
     * Get image_updated_at
     *
     * @return \DateTime 
     */
    public function getImageUpdatedAt()
    {
        return $this->image_updated_at;
    }
    #---------------------------------------------------
    public function isFavouriteItem($product_item){
        foreach($this->product_items as $pi){
            if ($pi->getId()==$product_item->getId()){
                return true;
            }
        }
        return false;
    }
      #---------------------------------------------------
    public function getFavouriteItemIdArray(){
        $arr=array();
        foreach($this->product_items as $pi){
            array_push($arr, $pi->getId());
        }
        return $arr;
    }
    #---------------------------------------------------
    public function compareUserDevicesDate()
    {
         $us= $this->getUserDevices();
         $image_updated_at=null;
         $ud=null;        
         foreach($us as $userdevice)
         {
            if($image_updated_at==null or date_timestamp_get($image_updated_at)<date_timestamp_get($userdevice->getImageUpdatedAt())){
                $image_updated_at=$userdevice->getImageUpdatedAt();                
                $ud=$userdevice;
            } 
         }  
         if($this->image_updated_at==null or $image_updated_at==null){
            return false;
         }else{
         if(date_timestamp_get($image_updated_at) >  date_timestamp_get($this->getImageUpdatedAt())){
             return $ud;
         }else{
             return false;
         }
       }
         
    }
    #---------------------------------------------------

    public function getDeviceWithLatestUpdatedImage() {
        $us = $this->getUserDevices();
        $ud = null;
        if ($us) {
            foreach ($us as $userdevice) {
                if ($ud == null or date_timestamp_get($ud->getImageUpdatedAt()) < date_timestamp_get($userdevice->getImageUpdatedAt())) {
                    $ud = $userdevice;
                }
            }
        }
        return $ud;
    }

    #---------------------------------------------------

    public function getLatestImageFromDevices() {
        $ud = $this->getDeviceWithLatestUpdatedImage();
        if ($ud)
            return $ud->getWebPath();
        else
            return null;
    }
 #---------------------------------------------------
  #---------------------------------------------------
  public function toArray($all=false, $base_path=null){

	$obj = array();

	$obj['id'] = $this->getId();
	$obj['email'] = $this->getEmail();
	$obj['first_name'] = $this->getFirstName();
	$obj['last_name'] = $this->getLastName();
	$obj['zipcode'] = $this->getZipcode();
    $obj['phone_number'] = $this->getPhoneNumber();
	$obj['gender'] = $this->getGender();
	$obj['birth_date']=$this->getBirthDate()?$this->getBirthDate()->format('Y-m-d'):null;
	$obj['auth_token'] = $this->getAuthToken();
	$obj['auth_token_web_service'] = $this->getAuthTokenWebService();
        $obj['path']= $base_path . $this->getDirWebPath();
	if($all){
	  $obj['image'] = $this->getImage();
	  $obj['avatar'] = $this->getAvatar();
	  $obj['web_path'] = $this->getDirWebPath();
	  $obj['secret_question'] = $this->getSecretQuestion();
	  $obj['secret_answer'] = $this->getSecretAnswer();
	  $obj['created_at']=$this->getCreatedAt()?$this->getCreatedAt()->format('Y-m-d'):null;
	  $obj['updated_at']=$this->getUpdatedAt()?$this->getUpdatedAt()->format('Y-m-d'):null;
	  $obj['image_updated_at']=$this->getImageUpdatedAt()?$this->getImageUpdatedAt()->format('Y-m-d'):null;
	}
	return $obj;

  }

  public function 
          toDataArray($key = true, $device_type = null, $base_path = null, $device_config = null) {
        if ($key) {
            #$device_specs=$this->getDeviceSpecs($device_type);
            $resize_ratio_jt = is_array($device_config) && array_key_exists('resize_ratio_jt', $device_config) ? $device_config['resize_ratio_jt'] : 0;
            $device_conversion_ratio = is_array($device_config) && array_key_exists('conversion_ratio', $device_config) ? $device_config['conversion_ratio'] : 0;
            $iphone_resize_ratio = is_array($device_config) && array_key_exists('resize_ratio', $device_config) ? $device_config['resize_ratio'] : 0;
            $neck_exclusion_px = is_array($device_config) && array_key_exists('neck_exlusion_px', $device_config) ? $device_config['neck_exlusion_px'] : 0;
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $this->measurement->calculatePlacementPositions($device_conversion_ratio);
            
            $this->measurement->top_placement = $this->measurement->top_placement - ($iphone_resize_ratio * $neck_exclusion_px);
            #$this->measurement->top_placement = strpos($device_type, 'iphone6') === false ? $this->measurement->top_placement : ($this->measurement->top_placement * $resize_ratio_jt)-2.048867;            
            
            $this->measurement->top_placement = strpos($device_type, 'iphone6') === false ? $this->measurement->top_placement : ($this->measurement->top_placement * $resize_ratio_jt);

            ##added by umer on 06-10-2016 as per ibrahim bhai instructions
            ##$this->measurement->top_placement + 7;
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $this->measurement->bottom_placement = $this->measurement->bottom_placement - 94;
            if ($device_type=='iphone6' || $device_type=='iphone6s'){
                $x_calculation=($this->measurement->bottom_placement * ($resize_ratio_jt-1)); # 0.08% value calculation
                #$this->measurement->bottom_placement = ($hip_height  * $resize_ratio_jt) + 6;
                $this->measurement->bottom_placement = ($this->measurement->bottom_placement  - $x_calculation) + 16.5;
            }
            if ($device_type=='iphone5' || $device_type=='iphone5s' || $device_type=='iphone5c'){
                
                ///// Test row for ip5s account test
                //$this->measurement->top_placement = $this->measurement->top_placement + 8;
                ///// END - Test row for ip5s account test
                
                $x_calculation=($this->measurement->bottom_placement * ($resize_ratio_jt-1)); # 0.08% value calculation
                #$this->measurement->bottom_placement = ($hip_height  * $resize_ratio_jt) + 6;
                $this->measurement->bottom_placement = ($this->measurement->bottom_placement  - $x_calculation) + 8;
            }
            ##added by umer on 06-10-2016 as per ibrahim bhai instructions
            ##$this->measurement->bottom_placement + 10.5;
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $measurement_json = $this->measurement && $this->measurement->getJSONMeasurement('actual_user') ? $this->measurement->getJSONMeasurement('actual_user') : '';
            return array(
                'id' => $this->getId(),
                'user_id' => $this->getId(),
                'email' => $this->getEmail(),
                'name' => $this->getFullName(),
                'first_name' => $this->getFirstName(),
                'last_name' => $this->getLastName(),
                'zipcode' => $this->getZipcode(),
                'phone_number' => $this->getPhoneNumber(),
                'gender' => $this->getGender(),
                'auth_token' => $this->getAuthToken(),
                'birth_date' => $this->getBirthDate() ? $this->getBirthDate()->format('Y-m-d') : null,
                'weight' => $this->measurement ? $this->measurement->getWeight() : 0,
                'height' => $this->measurement ? $this->measurement->getHeight() : 0,
                'waist' => $this->measurement ? $this->measurement->getWaist() : 0,
                'belt' => $this->measurement ? $this->measurement->getBelt() : 0,
                'top_placement' => $this->measurement ? $this->measurement->top_placement : 0,
                'bottom_placement' => $this->measurement ? $this->measurement->bottom_placement : 0,
                'device_conversion_ratio' => $device_conversion_ratio,
                'hip' => $this->measurement ? $this->measurement->getHip() : 0,
                'bust' => $this->measurement ? $this->measurement->getBust() : 0,
                'chest' => $this->measurement ? $this->measurement->getChest() : 0,
                'arm' => $this->measurement ? $this->measurement->getArm() : 0,
                'inseam' => $this->measurement ? $this->measurement->getInseam() : 0,
                'shoulder_height' => $this->measurement ? $this->measurement->getShoulderHeight() : 0,
                'shoulder_length' => $this->measurement ? $this->measurement->getShoulderLength() : 0,
                'outseam' => $this->measurement ? $this->measurement->getOutseam() : 0,
                'sleeve' => $this->measurement ? $this->measurement->getSleeve() : 0,
                'neck' => $this->measurement ? $this->measurement->getNeck() : 0,
                'thigh' => $this->measurement ? $this->measurement->getThigh() : 0,
                'center_front_waist' => $this->measurement ? $this->measurement->getCenterFrontWaist() : 0,
                'shoulder_across_front' => $this->measurement ? $this->measurement->getShoulderAcrossFront() : 0,
                'shoulder_across_back' => $this->measurement ? $this->measurement->getShoulderAcrossBack() : 0,
                'bicep' => $this->measurement ? $this->measurement->getBicep() : 0,
                'tricep' => $this->measurement ? $this->measurement->getTricep() : 0,
                'wrist' => $this->measurement ? $this->measurement->getWrist() : 0,
                'back_waist' => $this->measurement ? $this->measurement->getBackWaist() : 0,
                'waist_hip' => $this->measurement ? $this->measurement->getWaistHip() : 0,
                'knee' => $this->measurement ? $this->measurement->getKnee() : 0,
                'calf' => $this->measurement ? $this->measurement->getCalf() : 0,
                'ankle' => $this->measurement ? $this->measurement->getAnkle() : 0,
                'image' => $this->image,
                'avatar' => $this->avatar,
                'path' => $base_path . $this->getDirWebPath(),
                'body_type' => $this->measurement ? $this->measurement->getBodyTypes() : '',
                'body_shape' => $this->measurement ? $this->measurement->getBodyShape() : '',
                'bra_size' => $this->measurement ? $this->measurement->getBraSize() : '',
                'bust_height' => $this->measurement ? $this->measurement->getBustHeight() : 0,
                'waist_height' => $this->measurement ? $this->measurement->getWaistHeight() : 0,
                'hip_height' => $this->measurement ? $this->measurement->getHipHeight() : 0,
                'iphone_foot_height' => $this->measurement ? $this->measurement->getIphoneFootHeight() : 0,
                'iphone_head_height' => $this->measurement ? $this->measurement->getIphoneHeadHeight() : 0,
                'top_brand_id' => $this->measurement && $this->measurement->getTopBrand() ? $this->measurement->getTopBrand()->getId() : 0,
                'top_brand' => $this->measurement && $this->measurement->getTopBrand() ? $this->measurement->getTopBrand()->getName() : '',
                'top_fitting_size_chart_id' => $this->measurement && $this->measurement->getTopFittingSizeChart() ? $this->measurement->getTopFittingSizeChart()->getId() : 0,
                'top_fitting_size' => $this->measurement && $this->measurement->getTopFittingSizeChart() ? $this->measurement->getTopFittingSizeChart()->getTitle() : '',
                'bottom_brand_id' => $this->measurement && $this->measurement->getBottomBrand() ? $this->measurement->getBottomBrand()->getId() : 0,
                'bottom_brand' => $this->measurement && $this->measurement->getBottomBrand() ? $this->measurement->getBottomBrand()->getName() : '',
                'bottom_fitting_size_chart_id' => $this->measurement && $this->measurement->getBottomFittingSizeChart() ? $this->measurement->getBottomFittingSizeChart()->getId() : 0,
                'bottom_fitting_size' => $this->measurement && $this->measurement->getBottomFittingSizeChart() ? $this->measurement->getBottomFittingSizeChart()->getTitle() : '',
                'dress_brand_id' => $this->measurement && $this->measurement->getDressBrand() ? $this->measurement->getDressBrand()->getId() : 0,
                'dress_brand' => $this->measurement && $this->measurement->getDressBrand() ? $this->measurement->getDressBrand()->getName() : '',
                'dress_fitting_size_chart_id' => $this->measurement && $this->measurement->getDressFittingSizeChart() ? $this->measurement->getDressFittingSizeChart()->getId() : 0,
                'dress_fitting_size' => $this->measurement && $this->measurement->getDressFittingSizeChart() ? $this->measurement->getDressFittingSizeChart()->getTitle() : '',
                'image_device_type' => $this->image_device_type,
                'device_type' => $device_type,
                #'height_per_inch'=>$device_specs?$device_specs->getDeviceUserPerInchPixelHeight():0,
                'height_per_inch' => is_array($device_config) && array_key_exists('pixel_per_inch', $device_config) ? $device_config['pixel_per_inch'] : 0,
                'device_type' => $device_type,
                'default_user' => $this->user_marker ? $this->user_marker->getDefaultUser() : false,
                'status' => $this->status ? $this->status : 0,
                'measurement_json' => $measurement_json,
            );
        } else {
            return array(
                $this->getId(),
                $this->getEmail(),
                $this->getFullName(),
                $this->getZipcode(),
                $this->getPhoneNumber(),
                $this->getGender(),
                $this->getBirthDate() ? $this->getBirthDate()->format('Y-m-d') : null,
                $this->measurement ? $this->measurement->getWeight() : 0,
                $this->measurement ? $this->measurement->getHeight() : 0,
                $this->measurement ? $this->measurement->getWaist() : 0,
                $this->measurement ? $this->measurement->getBelt() : 0,
                $this->measurement ? $this->measurement->top_placement : 0,
                $this->measurement ? $this->measurement->bottom_placement : 0,
                $this->measurement ? $this->measurement->getHip() : 0,
                $this->measurement ? $this->measurement->getBust() : 0,
                $this->measurement ? $this->measurement->getChest() : 0,
                $this->measurement ? $this->measurement->getArm() : 0,
                $this->measurement ? $this->measurement->getInseam() : 0,
                $this->measurement ? $this->measurement->getShoulderHeight() : 0,
                $this->measurement ? $this->measurement->getOutseam() : 0,
                $this->measurement ? $this->measurement->getSleeve() : 0,
                $this->measurement ? $this->measurement->getNeck() : 0,
                $this->measurement ? $this->measurement->getThigh() : 0,
                $this->measurement ? $this->measurement->getCenterFrontWaist() : 0,
                $this->measurement ? $this->measurement->getShoulderAcrossFront() : 0,
                $this->measurement ? $this->measurement->getShoulderAcrossBack() : 0,
                $this->measurement ? $this->measurement->getBicep() : 0,
                $this->measurement ? $this->measurement->getTricep() : 0,
                $this->measurement ? $this->measurement->getWrist() : 0,
                $this->measurement ? $this->measurement->getBackWaist() : 0,
                $this->measurement ? $this->measurement->getWaistHip() : 0,
                $this->measurement ? $this->measurement->getKnee() : 0,
                $this->measurement ? $this->measurement->getCalf() : 0,
                $this->measurement ? $this->measurement->getAnkle() : 0,
            );
        }
    }

    #-------------------------------------------------------
      public function toDetailArray($options) {
        $a = array();
        if (in_array('user', $options)) {
            $a = array_merge($a, $this->toArray());
        }

        if (in_array('measurement', $options)) {
            if ($this->measurement) {
                $a = array_merge($a, $this->measurement->getArray());
            }
        }

        if (in_array('mask_marker', $options)) {
            if ($this->user_marker) {
                $mma = $this->user_marker->toDataArray();
                unset($mma['id']);
                $a = array_merge($a, $mma);
            }
        }
        if (in_array('device', $options)) {
            $ud = $this->getDeviceSpecs();
            if ($ud) {
                $ud_array = $ud->toArray();
                unset($ud_array['id']);
                $a = array_merge($a, $ud_array);
            }
        }
        return $a;
    }
  #---------------------------
  //---------------------------
     public function resize_image() {

        $filename = $this->getAbsolutePath();
        $image_info = @getimagesize($filename);
        $image_type = $image_info[2];

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filename);
                break;
        }
        #------------ Need dimensions
        
        $width = $image_info[0] * 0.50;
        $height = $image_info [1] * 0.50;
        
        #$width = 320;
        #$height = 568;
                
        $img_new = imagecreatetruecolor($width, $height);
        imagealphablending($img_new, false);
        imagesavealpha($img_new,true);
        $transparent = imagecolorallocatealpha($img_new, 255, 255, 255, 127);
        imagefilledrectangle($img_new, 0, 0, $width, $height, $transparent);
        imagecopyresampled($img_new, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($img_new, $filename, 75);
                break;
            case IMAGETYPE_GIF:
                imagegif($img_new, $filename);
                break;
            case IMAGETYPE_PNG:
                imagepng($img_new, $filename);
                break;
        }
      
        
    }
  ####################  Cart Methods ###################################
  /**
   * Add cart
   *
   * @param \LoveThatFit\CartBundle\Entity\Cart $cart
   * @return User
   */
  public function addCart(\LoveThatFit\CartBundle\Entity\Cart $cart)
  {
	$this->cart[] = $cart;

	return $this;
  }

  /**
   * Remove cart
   *
   * @param \LoveThatFit\CartBundle\Entity\Cart $cart
   */
  public function removeCart(\LoveThatFit\CartBundle\Entity\Cart $cart)
  {
	$this->cart->removeElement($cart);
  }

  /**
   * Get cart
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getCart()
  {
	return $this->cart;
  }


    ####################  Wishlist Methods ###################################
    /**
     * Add wishlist
     *
     * @param \LoveThatFit\CartBundle\Entity\Wishlist $wishlist
     * @return User
     */
    public function addWishlist(\LoveThatFit\CartBundle\Entity\Wishlist $wishlist)
    {
        $this->wishlist[] = $wishlist;

        return $this;
    }

    /**
     * Remove wishlist
     *
     * @param \LoveThatFit\CartBundle\Entity\Wishlist $wishlist
     */
    public function removeWishlist(\LoveThatFit\CartBundle\Entity\Wishlist $wishlist)
    {
        $this->wishlist->removeElement($wishlist);
    }

    /**
     * Get wishlist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }
  ##############################################################################
  
  public function addDeviceToken($device_type, $token) {
      if (strpos(strtolower($device_type), 'iphone') !== false || strpos(strtolower($device_type), 'ipad') !== false ) {
    $device_type='iphone';
        }  
      if ($this->device_tokens) {
            $temp = json_decode($this->device_tokens);
            if (is_array($temp)) {
                if (!in_array($token, $temp[$device_type])) {
                    array_push($temp[$device_type], $token);
                    $this->device_tokens = json_encode($temp);
                }
            } else {
                $this->device_tokens = json_encode(array($device_type => array($token)));
            }
        } else {
            $this->device_tokens = json_encode(array($device_type => array($token)));
        }
    }
     ##############################################################################
  
  public function removeDeviceToken($token) {
      if ($this->device_tokens) {
            $temp = json_decode($this->device_tokens);
            if (is_array($temp)) {
                foreach ($temp as $device => $tokens) {
                    foreach ($tokens as $t) {
                        if($t==$token){
                            unset($t); 
                        }
                    }
                }
            } 
        } 
    }
    #------------------------------------------------
    public function getDeviceTokenArrayByDevice($device_type) {
        #device_type = iphone or android
        if ($this->device_tokens) {
            $temp = json_decode($this->device_tokens);
            if (is_object($temp)) {
                return $temp->$device_type;
            }
        }
        return;
    }
    #------------------------------------------------
    public function getDeviceTokenArray() {
        if ($this->device_tokens) {
            $temp = json_decode($this->device_tokens);
            if (is_array($temp)) {
                return $temp;
            }
        }
        return;
    }
  
    /**
     * Add user_addresses
     *
     * @param \LoveThatFit\CartBundle\Entity\UserAddresses $userAddresses
     * @return User
     */
    public function addUserAddresse(\LoveThatFit\CartBundle\Entity\UserAddresses $userAddresses)
    {
        $this->user_addresses[] = $userAddresses;
    
        return $this;
    }

    /**
     * Remove user_addresses
     *
     * @param \LoveThatFit\CartBundle\Entity\UserAddresses $userAddresses
     */
    public function removeUserAddresse(\LoveThatFit\CartBundle\Entity\UserAddresses $userAddresses)
    {
        $this->user_addresses->removeElement($userAddresses);
    }

    /**
     * Get user_addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserAddresses()
    {
        return $this->user_addresses;
    }

    /**
     * Add user_orders
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrder $userOrders
     * @return User
     */
    public function addUserOrder(\LoveThatFit\CartBundle\Entity\UserOrder $userOrders)
    {
        $this->user_orders[] = $userOrders;
    
        return $this;
    }

    /**
     * Remove user_orders
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrder $userOrders
     */
    public function removeUserOrder(\LoveThatFit\CartBundle\Entity\UserOrder $userOrders)
    {
        $this->user_orders->removeElement($userOrders);
    }

    /**
     * Get user_orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserOrders()
    {
        return $this->user_orders;
    }

    /**
     * Add user_archives
     *
     * @param \LoveThatFit\UserBundle\Entity\UserArchives $userArchives
     * @return User
     */
    public function addUserArchive(\LoveThatFit\UserBundle\Entity\UserArchives $userArchives)
    {
        $this->user_archives[] = $userArchives;
    
        return $this;
    }

    /**
     * Remove user_archives
     *
     * @param \LoveThatFit\UserBundle\Entity\UserArchives $userArchives
     */
    public function removeUserArchive(\LoveThatFit\UserBundle\Entity\UserArchives $userArchives)
    {
        $this->user_archives->removeElement($userArchives);
    }

    /**
     * Add save_look
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLook $saveLook
     * @return User
     */
    public function addSaveLook(\LoveThatFit\AdminBundle\Entity\SaveLook $saveLook)
    {
        $this->save_look[] = $saveLook;
    
        return $this;
    }

    /**
     * Remove save_look
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLook $saveLook
     */
    public function removeSaveLook(\LoveThatFit\AdminBundle\Entity\SaveLook $saveLook)
    {
        $this->save_look->removeElement($saveLook);
    }

    /**
     * Get save_look
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSaveLook()
    {
        return $this->save_look;
    }

    /**
     * Add fnfusers
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFUser $fnfusers
     * @return User
     */
    public function addFnfuser(\LoveThatFit\AdminBundle\Entity\FNFUser $fnfusers)
    {
        $this->fnfusers[] = $fnfusers;
    
        return $this;
    }

    /**
     * Remove fnfusers
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFUser $fnfusers
     */
    public function removeFnfuser(\LoveThatFit\AdminBundle\Entity\FNFUser $fnfusers)
    {
        $this->fnfusers->removeElement($fnfusers);
    }

    /**
     * Get fnfusers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFnfusers()
    {
        return $this->fnfusers;
    }

    public function getUniqueName()
    {
        return sprintf('%s - %s', $this->id, $this->email);
    }

    public function __toString()
    {
        return $this->getId() ." (" .$this->email." )";
    }
	
	/**
     * Add user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     * @return User
     */
    public function addUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history[] = $userItemFavHistory;
    
        return $this;
    }

    /**
     * Remove user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     */
    public function removeUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history->removeElement($userItemFavHistory);
    }

    /**
     * Get user_item_fav_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserItemFavHistory()
    {
        return $this->user_item_fav_history;
    }
}