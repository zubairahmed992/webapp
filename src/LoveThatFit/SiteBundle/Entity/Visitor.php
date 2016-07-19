<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\SiteBundle\Entity\Visitor
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LoveThatFit\SiteBundle\Entity\VisitorRepository")
 */
class Visitor
{    
      /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     //----------------------------------------------------------
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
     public function __construct()
    {
    }
  
    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

  

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string",nullable=true)
     */
    private $email;
    
    /**
     * @var string $ip_address
     *
     * @ORM\Column(name="ip_address", type="string",nullable=true)
     */
    private $ip_address;
    
    
    /**
     * @var string $browser
     *
     * @ORM\Column(name="browser", type="string",nullable=true)
     */
    private $browser;
    
    /**
     * @var string $device
     *
     * @ORM\Column(name="device", type="string",nullable=true)
     */
    private $device;
    
    
    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string",nullable=true)
     */
    private $country;
    
    /**
     * @var string $json_data
     *
     * @ORM\Column(name="json_data", type="string",nullable=true)
     */
    private $json_data;
    
  #----------------------------------------------------------------  
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Visitor
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
#----------------------------------------------------------------  
   
    /**
     * Set device
     *
     * @param string $device
     * @return Visitor
     */
    public function setDevice($device)
    {
        $this->device = $device;
    
        return $this;
    }

    /**
     * Get device
     *
     * @return string 
     */
    public function getDevice()
    {
        return $this->device;
    }
   #----------------------------------------------------------------  

    /**
     * Set email
     *
     * @param string $email
     * @return Visitor
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
   #----------------------------------------------------------------  
   /**
     * Set ip_address
     *
     * @param string $ip_address
     * @return Visitor
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = $ip_address;
    
        return $this;
    }

    /**
     * Get $ip_address
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }
   #----------------------------------------------------------------  
      /**
     * Set browser
     *
     * @param string $browser
     * @return Visitor
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    
        return $this;
    }

    /**
     * Get $browser
     *
     * @return string 
     */
    public function getBrowser()
    {
        return $this->browser;
    }
   #----------------------------------------------------------------  
      /**
     * Set country
     *
     * @param string $country
     * @return Visitor
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get $country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }
   #----------------------------------------------------------------  
    
      /**
     * Set json_data
     *
     * @param string $json_data
     * @return Visitor
     */
    public function setJsonData($json_data)
    {
        $this->json_data = $json_data;
    
        return $this;
    }

    /**
     * Get $json_data
     *
     * @return string 
     */
    public function getJsonData()
    {
        return $this->json_data;
    }
   #----------------------------------------------------------------  
}