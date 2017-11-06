<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LoveThatFit\UserBundle\Entity\InviteFriend
 *  
 * @ORM\Table(name="invite_friend")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\InviteFriendRepository")
 */
class InviteFriend  {

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitefriend" , cascade={"persist"})
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
     * @var dateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;


    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=true)
     */
    private $email;

 
     /**
     * @var string $friend_name
     *
     * @ORM\Column(name="friend_name", type="string", length=60, nullable=true)
     */
    private $friend_name;


    /**
     * @var string $first_name
     *
     * @ORM\Column(name="first_name", type="string", length=60, nullable=true)
     */
    private $first_name;

    /**
     * @var string $last_name
     *
     * @ORM\Column(name="last_name", type="string", length=60, nullable=true)
     */
    private $last_name;

     /**
     * @var string $friend_email
     *
     * @ORM\Column(name="friend_email", type="string", length=60, nullable=true)
     */
    private $friend_email;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
#------------------------------------
    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return InviteFriend
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


  #------------------------------------

    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return InviteFriend
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

 #------------------------------------------------   message


    /**
     * Set friend_name
     *
     * @param string $friend_name
     * @return InviteFriend
     */
    public function setFriendName($friend_name) {
        $this->friend_name = $friend_name;

        return $this;
    }

    /**
     * Get friend_name
     *
     * @return string 
     */
    public function getFriendName() {
        return $this->friend_name;
    }


    #------------------------------------------------   message


    /**
     * Set first_name
     *
     * @param string $first_name
     * @return InviteFriend
     */
    public function setFirstName($first_name) {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }


    #------------------------------------------------   message


    /**
     * Set last_name
     *
     * @param string $last_name
     * @return InviteFriend
     */
    public function setLastName($last_name) {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }
    #------------------------------------------------   email
    /**
     * Set friend_email
     *
     * @param string $friend_email
     * @return InviteFriend
     */
    public function setFriendEmail($friend_email) {
        $this->friend_email = $friend_email;

        return $this;
    }

    /**
     * Get friend_email
     *
     * @return string 
     */
    public function getFriendEmail() {
        return $this->friend_email;
    }

    #------------------------------------------------   phone

    /**
     * Set email
     *
     * @param string $email
     * @return InviteFriend
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
   
}