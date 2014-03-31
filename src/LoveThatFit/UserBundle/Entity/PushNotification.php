<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\PushNotification
 *  
 * @ORM\Table(name="push_notification")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\PushNotificationRepository")
 */
class PushNotification  {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

     /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $iserId;
    
   /**
     * @var string $message
     *
     * @ORM\Column(name="message", type="string",nullable=true)
     */
    private $message; 
    
    
     /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=60, nullable=true)
     */
    private $type;
    
    /**
     * @var string $limit
     *
     * @ORM\Column(name="limit", type="string", length=60, nullable=true)
     */
    private $limit;
    
     /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;
    
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
     * Set iserId
     *
     * @param integer $iserId
     * @return PushNotification
     */
    public function setIserId($iserId)
    {
        $this->iserId = $iserId;
    
        return $this;
    }

    /**
     * Get iserId
     *
     * @return integer 
     */
    public function getIserId()
    {
        return $this->iserId;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return PushNotification
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PushNotification
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set limit
     *
     * @param string $limit
     * @return PushNotification
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    
        return $this;
    }

    /**
     * Get limit
     *
     * @return string 
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return PushNotification
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PushNotification
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
     * @return PushNotification
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