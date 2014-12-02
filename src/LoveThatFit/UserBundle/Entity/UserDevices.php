<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\UserDevices
 *  
 * @ORM\Table(name="user_devices")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserDevicesRepository")
 */
class UserDevices  {

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_devices" , cascade={"persist"})
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
     * @var string $device_name
     *
     * @ORM\Column(name="device_name", type="string",nullable=true)
     */
    private $device_name; 
    
    
     /**
     * @var string $deviceType
     *
     * @ORM\Column(name="device_type", type="string", length=60, nullable=true)
     */
    private $deviceType;
    
     /**
     * @var string $deviceUserPerInchPixelHeight
     *
     * @ORM\Column(name="device_user_per_inch_pixel_height", type="string", length=60, nullable=true)
     */
    private $deviceUserPerInchPixelHeight; 
    
     /**
     * @var string $deviceImage
     * @ORM\Column(name="device_image", type="string", length=255, nullable=true)
    */
    private $deviceImage; 
    
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
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserDevices
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
     * Set device_name
     *
     * @param string $deviceName
     * @return UserDevices
     */
    public function setDeviceName($deviceName)
    {
        $this->device_name = $deviceName;
    
        return $this;
    }

    /**
     * Get device_name
     *
     * @return string 
     */
    public function getDeviceName()
    {
        return $this->device_name;
    }

    /**
     * Set deviceType
     *
     * @param string $deviceType
     * @return UserDevices
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
    
        return $this;
    }

    /**
     * Get deviceType
     *
     * @return string 
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * Set deviceUserPerInchPixelHeight
     *
     * @param string $deviceUserPerInchPixelHeight
     * @return UserDevices
     */
    public function setDeviceUserPerInchPixelHeight($deviceUserPerInchPixelHeight)
    {
        $this->deviceUserPerInchPixelHeight = $deviceUserPerInchPixelHeight;
    
        return $this;
    }

    /**
     * Get deviceUserPerInchPixelHeight
     *
     * @return string 
     */
    public function getDeviceUserPerInchPixelHeight()
    {
        return $this->deviceUserPerInchPixelHeight;
    }

    /**
     * Set deviceImage
     *
     * @param string $deviceImage
     * @return UserDevices
     */
    public function setDeviceImage($deviceImage)
    {
        $this->deviceImage = $deviceImage;
    
        return $this;
    }

    /**
     * Get deviceImage
     *
     * @return string 
     */
    public function getDeviceImage()
    {
        return $this->deviceImage;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserDevices
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
     * @return UserDevices
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
}