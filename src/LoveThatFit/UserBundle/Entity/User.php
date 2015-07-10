<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\User
 *  
 * @ORM\Table(name="ltf_users")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable {

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
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserFittingRoomItem", mappedBy="User")
     */
    private $userfittingroomitem;
    
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\RetailerSiteUser", mappedBy="user")
     */
    private $retailer_site_users;
    
    
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack", mappedBy="user")
     */
    private $retailer_order_track;
    
    
    
    
    /**
     * @ORM\OneToOne(targetEntity="UserMarker", mappedBy="user")
     * */
    private $user_marker; 
    

//---------------------------------------  implement the UserInterface
    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->product_items = new \Doctrine\Common\Collections\ArrayCollection();
	  	$this->cart = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var string $image_updated_at
     *
     * @ORM\Column(name="image_updated_at", type="datetime", nullable=true)
     * )
     */
    private $image_updated_at;

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
        $this->copyTempToOriginalImage();
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

    /**
     * Add retailer_order_track
     *
     * @param \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack
     * @return User
     */
    public function addRetailerOrderTrack(\LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack)
    {
        $this->retailer_order_track[] = $retailerOrderTrack;
    
        return $this;
    }

    /**
     * Remove retailer_order_track
     *
     * @param \LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack
     */
    public function removeRetailerOrderTrack(\LoveThatFit\ExternalSiteBundle\Entity\RetailerOrderTrack $retailerOrderTrack)
    {
        $this->retailer_order_track->removeElement($retailerOrderTrack);
    }

    /**
     * Get retailer_order_track
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailerOrderTrack()
    {
        return $this->retailer_order_track;
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
            foreach ($us as $userdevice) {
                if ($device_type == $userdevice->getDeviceType()) {
                    return $userdevice;
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
    public function toArray($all=false){
        
        $obj = array();
        
        $obj['id'] = $this->getId();
        $obj['email'] = $this->getEmail();
        $obj['first_name'] = $this->getFirstName();
        $obj['last_name'] = $this->getLastName();
        $obj['zipcode'] = $this->getZipcode();
        $obj['gender'] = $this->getGender();        
        $obj['birth_date']=$this->getBirthDate()?$this->getBirthDate()->format('Y-m-d'):null;        
        $obj['auth_token'] = $this->getAuthToken();
        $obj['auth_token_web_service'] = $this->getAuthTokenWebService();
        if($all){
            $obj['salt'] = $this->getSalt();
            $obj['password'] = $this->getPassword();
            $obj['image'] = $this->getImage();
            $obj['avatar'] = $this->getAvatar();                
            $obj['web_path'] = $this->getDirWebPath();
            $obj['secret_question'] = $this->getSecretQuestion();
            $obj['secret_answer'] = $this->getSecretAnswer();
            $obj['time_spent'] = $this->getTimeSpent();
            $obj['created_at']=$this->getCreatedAt()?$this->getCreatedAt()->format('Y-m-d'):null;        
            $obj['updated_at']=$this->getUpdatedAt()?$this->getUpdatedAt()->format('Y-m-d'):null;        
            $obj['image_updated_at']=$this->getImageUpdatedAt()?$this->getImageUpdatedAt()->format('Y-m-d'):null;                
            }
        return $obj;
        
    }
    
    public function toDataArray($key=true,$device_type=null){
        if($key){
            $device_specs=$this->getDeviceSpecs($device_type);
        return array(
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'name' => $this->getFullName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'zipcode' => $this->getZipcode(),
            'gender' => $this->getGender(),
            'authTokenWebService' => $this->getAuthToken(),
            'birth_date' => $this->getBirthDate()?$this->getBirthDate()->format('Y-m-d'):null,        
            'weight' => $this->measurement?$this->measurement->getWeight():0,
            'height' => $this->measurement?$this->measurement->getHeight():0,
            'waist' => $this->measurement?$this->measurement->getWaist():0,
            'belt' => $this->measurement?$this->measurement->getBelt():0,
            'hip' => $this->measurement?$this->measurement->getHip():0,
            'bust' => $this->measurement?$this->measurement->getBust():0,
            'chest' => $this->measurement?$this->measurement->getChest():0,
            'arm' => $this->measurement?$this->measurement->getArm():0,
            'inseam' => $this->measurement?$this->measurement->getInseam():0,            
            #'shoulder_height' => $this->measurement?$this->measurement->getShoulderHeight():0,
            'shoulder_height' => 0,
            'outseam' => $this->measurement?$this->measurement->getOutseam():0,
            'sleeve' => $this->measurement?$this->measurement->getSleeve():0,
            'neck' => $this->measurement?$this->measurement->getNeck():0,
            'thigh' => $this->measurement?$this->measurement->getThigh():0,
            'center_front_waist' => $this->measurement?$this->measurement->getCenterFrontWaist():0,
            'shoulder_across_front' => $this->measurement?$this->measurement->getShoulderAcrossFront():0,
            'shoulder_across_back' => $this->measurement?$this->measurement->getShoulderAcrossBack():0,
            'bicep' => $this->measurement?$this->measurement->getBicep():0,
            'tricep' =>$this->measurement?$this->measurement->getTricep():0,
            'wrist' => $this->measurement?$this->measurement->getWrist():0,
            'back_waist' => $this->measurement?$this->measurement->getBackWaist():0,
            'waist_hip' => $this->measurement?$this->measurement->getWaistHip():0,
            'knee' =>  $this->measurement?$this->measurement->getKnee():0,
            'calf' =>  $this->measurement?$this->measurement->getCalf():0,
            'ankle' => $this->measurement?$this->measurement->getAnkle():0,

            'image' => $this->image,
            'avatar' => $this->avatar,
            'path' => $this->getUploadDir(),
            'body_type' =>$this->measurement? $this->measurement->getBodyTypes():'',
            'body_shape' => $this->measurement?$this->measurement->getBodyShape():'',
            'bra_size' =>$this->measurement?$this->measurement->getBraSize():'',
            'bust_height' => $this->measurement?$this->measurement->getBustHeight():0,
            'waist_height' => $this->measurement?$this->measurement->getWaistHeight():0,
            'hip_height' => $this->measurement?$this->measurement->getHipHeight():0, 
            'iphone_foot_height' => $this->measurement?$this->measurement->getIphoneFootHeight():0,
            'iphone_head_height' => $this->measurement?$this->measurement->getIphoneHeadHeight():0,
            
            'top_brand_id' => $this->measurement && $this->measurement->getTopBrand()?$this->measurement->getTopBrand()->getId():0,
            'top_brand' => $this->measurement && $this->measurement->getTopBrand()?$this->measurement->getTopBrand()->getName():'',
            'top_fitting_size_chart_id' => $this->measurement && $this->measurement->getTopFittingSizeChart()?$this->measurement->getTopFittingSizeChart()->getId():0,
            'top_fitting_size' => $this->measurement && $this->measurement->getTopFittingSizeChart()?$this->measurement->getTopFittingSizeChart()->getTitle():'',
            'bottom_brand_id' => $this->measurement && $this->measurement->getBottomBrand()?$this->measurement->getBottomBrand()->getId():0,
            'bottom_brand' => $this->measurement && $this->measurement->getBottomBrand()?$this->measurement->getBottomBrand()->getName():'',
            'bottom_fitting_size_chart_id' => $this->measurement && $this->measurement->getBottomFittingSizeChart()?$this->measurement->getBottomFittingSizeChart()->getId():0,
            'bottom_fitting_size' => $this->measurement && $this->measurement->getBottomFittingSizeChart()?$this->measurement->getBottomFittingSizeChart()->getTitle():'',
            'dress_brand_id' => $this->measurement && $this->measurement->getDressBrand()?$this->measurement->getDressBrand()->getId():0,
            'dress_brand' => $this->measurement && $this->measurement->getDressBrand()?$this->measurement->getDressBrand()->getName():'',
            'dress_fitting_size_chart_id' => $this->measurement && $this->measurement->getDressFittingSizeChart()?$this->measurement->getDressFittingSizeChart()->getId():0,
            'dress_fitting_size' => $this->measurement && $this->measurement->getDressFittingSizeChart()?$this->measurement->getDressFittingSizeChart()->getTitle():'',
            
            'device_type'=>$device_type,
            'height_per_inch'=>$device_specs?$device_specs->getDeviceUserPerInchPixelHeight():0,
        );
        }else{
            return array(
                $this->getId(),
                $this->getEmail(),
                $this->getFullName(),
                $this->getZipcode(),
                $this->getGender(),        
                $this->getBirthDate()?$this->getBirthDate()->format('Y-m-d'):null,        
                $this->measurement?$this->measurement->getWeight():0,
                $this->measurement?$this->measurement->getHeight():0,
                $this->measurement?$this->measurement->getWaist():0,
                $this->measurement?$this->measurement->getBelt():0,
                $this->measurement?$this->measurement->getHip():0,
                $this->measurement?$this->measurement->getBust():0,
                $this->measurement?$this->measurement->getChest():0,
                $this->measurement?$this->measurement->getArm():0,
                $this->measurement?$this->measurement->getInseam():0,            
                #$this->measurement?$this->measurement->getShoulderHeight():0,
                0,
                $this->measurement?$this->measurement->getOutseam():0,
                $this->measurement?$this->measurement->getSleeve():0,
                $this->measurement?$this->measurement->getNeck():0,
                $this->measurement?$this->measurement->getThigh():0,
                $this->measurement?$this->measurement->getCenterFrontWaist():0,
                $this->measurement?$this->measurement->getShoulderAcrossFront():0,
                $this->measurement?$this->measurement->getShoulderAcrossBack():0,
                $this->measurement?$this->measurement->getBicep():0,
                $this->measurement?$this->measurement->getTricep():0,
                $this->measurement?$this->measurement->getWrist():0,
                $this->measurement?$this->measurement->getBackWaist():0,
                $this->measurement?$this->measurement->getWaistHip():0,
                $this->measurement?$this->measurement->getKnee():0,
                $this->measurement?$this->measurement->getCalf():0,
                $this->measurement?$this->measurement->getAnkle():0,
            );    
        }
        
    }
    #------------------------------------------------------
    public function toDetailArray($options){
        $a=array();
        if (in_array('user', $options)){
            $a=array_merge($a, $this->toArray());
        }
        
        if (in_array('measurement', $options)){
            if ($this->measurement){
            $a=array_merge($a, $this->measurement->getArray());
            }
        }
        
        if (in_array('mask_marker', $options)){
            if ($this->user_marker){
            $a=array_merge($a, $this->user_marker->toDataArray());
            }
        }

        if (in_array('device', $options)){
            $ud=$this->getDeviceSpecs();
            if ($ud){
                $a=array_merge($a, $ud->toArray());
            }
        }        
        return $a;
    }
    #---------------------------0--------------------------------
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
        
        #$width = $image_info[0] * 0.50;
        #$height = $image_info [1] * 0.50;
        
        $width = 320;
        $height = 568;
                
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
  ##############################################################################
}