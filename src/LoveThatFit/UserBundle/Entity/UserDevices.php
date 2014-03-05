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
}