<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
/**
 * 
 *
 * @ORM\Table(name="ltf_retailer")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\RetailerRepository")
 */
class Retailer
{
    
    /**
     * @ORM\OneToMany(targetEntity="RetailerUser", mappedBy="ltf_retailer", orphanRemoval=true)
     */
    
    protected $retailer_users;
   
    
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    
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
     * @return Retailers
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
     * @return Retailer
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
     * @return Retailer
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
     * Constructor
     */
    public function __construct()
    {
        $this->retailer_users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add retailer_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers
     * @return Retailer
     */
    public function addRetailerUser(\LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers)
    {
        $this->retailer_users[] = $retailerUsers;
    
        return $this;
    }

    /**
     * Remove retailer_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers
     */
    public function removeRetailerUser(\LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers)
    {
        $this->retailer_users->removeElement($retailerUsers);
    }

    /**
     * Get retailer_users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailerUsers()
    {
        return $this->retailer_users;
    }
}