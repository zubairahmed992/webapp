<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
/**
 * @ORM\Entity
 * @ORM\Table(name="ltf_retailer_user")
 */
class RetailerUser
{
    /**
     * @ORM\ManyToOne(targetEntity="Retailer", inversedBy="retailer_users")
     * @ORM\JoinColumn(name="retailer_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(name="retailer_id", referencedColumnName="id")
     */
    protected $retailer;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

       /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true, nullable=false)
     * @Assert\Email(groups={"registration_step_one"}, message="Please provide a valid email")
     * @Assert\NotBlank(groups={"registration_step_one"}, message="Email cannot be blank")
     */
    private $email;

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
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;
    
    //-----------------------------------------------------
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
     * @return RetailerUser
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return RetailerUser
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return RetailerUser
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return RetailerUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return RetailerUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return RetailerUser
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return RetailerUser
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
     * Set retailer
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailer
     * @return RetailerUser
     */
    public function setRetailer(\LoveThatFit\AdminBundle\Entity\Retailer $retailer = null)
    {
        $this->retailer = $retailer;
    
        return $this;
    }

    /**
     * Get retailer
     *
     * @return \LoveThatFit\AdminBundle\Entity\Retailer 
     */
    public function getRetailer()
    {
        return $this->retailer;
    }
}