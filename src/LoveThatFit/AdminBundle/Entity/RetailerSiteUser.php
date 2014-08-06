<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="ltf_retailer_site_user")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\RetailerSiteUserRepository")
 */
class RetailerSiteUser {

   /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="retailer_site_users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\Retailer", inversedBy="retailer_site_users")
     * @ORM\JoinColumn(name="retailer_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @var string
     *
     * @ORM\Column(name="user_reference_id", type="string")
     */
    private $user_reference_id;

 

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
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;
    
    /**
      * @var string $customerOrder
     * @ORM\Column(name="customer_order", type="string", nullable=true)
     */
    private $customerOrder;
    

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserRetailer
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
     * @return UserRetailer
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return UserRetailer
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set user0
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserRetailer
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

    

    /**
     * Set retailer
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailer
     * @return UserRetailer
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

    /**
     * Set user_reference_id
     *
     * @param string $userReferenceId
     * @return RetailerSiteUser
     */
    public function setUserReferenceId($userReferenceId)
    {
        $this->user_reference_id = $userReferenceId;
    
        return $this;
    }

    /**
     * Get user_reference_id
     *
     * @return string 
     */
    public function getUserReferenceId()
    {
        return $this->user_reference_id;
    }

    /**
     * Set customerOrder
     *
     * @param string $customerOrder
     * @return RetailerSiteUser
     */
    public function setCustomerOrder($customerOrder)
    {
        $this->customerOrder = $customerOrder;
    
        return $this;
    }

    /**
     * Get customerOrder
     *
     * @return string 
     */
    public function getCustomerOrder()
    {
        return $this->customerOrder;
    }
}