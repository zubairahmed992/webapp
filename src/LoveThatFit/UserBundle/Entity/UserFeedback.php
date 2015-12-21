<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoveThatFit\UserBundle\Entity\UserFeedback
 *  
 * @ORM\Table(name="ltf_user_feedback")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserRepository")
 */
class UserFeedback {

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_feedback" , cascade={"persist"})
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
     * @var string $message
     *
     * @ORM\Column(name="message", type="text",nullable=true)
     */
    private $message;

    /**
     * @var dateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;
    #-------------------------------------------------
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
#-------------------------------------------------
    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserFeedback
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
    #-------------------------------------------------
    
     /**
     * Set message
     *
     * @param string $message
     * @return UserFeedback
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
    #-------------------------------------------------

     /**
     * Set createdAt
     *
     * @param \DateTime $created_at
     * @return UserDevices
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    
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

}