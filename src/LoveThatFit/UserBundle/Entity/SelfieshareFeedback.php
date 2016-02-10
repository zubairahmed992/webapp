<?php

namespace LoveThatFit\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LoveThatFit\UserBundle\Entity\SelfieshareFeedback
 *  
 * @ORM\Table(name="selfieshare_feedback")
 * @ORM\Entity()
 */
class SelfieshareFeedback  {
    
        
      /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\Selfieshare", inversedBy="selfieshare_feedback")
     * @ORM\JoinColumn(name="selfieshare_id", referencedColumnName="id", onDelete="CASCADE" )
     *  */
    private $selfieshare;
  
     /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string $comments
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private $comments;
    
      /**
     * @var integer $rating
     *
     * @ORM\Column(name="rating", type="integer")     
     */
    private $rating;
     /**
     * @var boolean $like
     *
     * @ORM\Column(name="like", type="boolean", nullable=true)
     */
    private $like;
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private $name;
     /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=true)
     */
    private $email;
     /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=60, nullable=true)
     */
    private $phone;
     /**
     * @var string $ref
     *
     * @ORM\Column(name="ref", type="string", length=60, nullable=true)
     */
    private $ref;
     /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;
    
    #------------------------------------------------   
    
    /**
     * Set selfieshare
     *
     * @param \LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare
     * @return SelfieshareFeedback
     */
    public function setSelfieshare(\LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare = null)
    {
        $this->selfieshare = $selfieshare;    
        return $this;
    }

    /**
     * Get selfieshare
     *
     * @return \LoveThatFit\UserBundle\Entity\Selfieshare 
     */
    public function getSelfieshare()
    {
        return $this->selfieshare;
    }
    
    
    #------------------------------------------------   
      /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    #------------------------------------------------   comments
         /**
     * Set comments
     *
     * @param string $comments
     * @return SelfieshareFeedback
     */
    public function setComments($comments) {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments() {
        return $this->comments;
    }
    #------------------------------------------------   rating
       

    /**
     * Set rating
     *
     * @param integer $rating
     * @return SelfieshareFeedback
     */
    public function setRating($rating) {
        $this->rating = $rating != null ? $rating : 0;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating() {
        return $this->rating != null ? $this->rating : 0;
    }
    #------------------------------------------------   like

       /**
     * Set like
     *
     * @param boolean $like
     * @return User
     */
    public function setLike($like) {
        $this->like = $like;
        return $this;
    }

    /**
     * Get like
     *
     * @return boolean 
     */
    public function getLike() {
        return $this->like;
    }

    
    #------------------------------------------------   name
        /**
     * Set name
     *
     * @param string $name
     * @return SelfieshareFeedback
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }
    #------------------------------------------------   email
    /**
     * Set email
     *
     * @param string $email
     * @return SelfieshareFeedback
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

    #------------------------------------------------   phone
         /**
     * Set phone
     *
     * @param string $phone
     * @return SelfieshareFeedback
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone() {
        return $this->phone;
    }
    #------------------------------------------------   ref
             /**
     * Set ref
     *
     * @param string $ref
     * @return SelfieshareFeedback
     */
    public function setRef($ref) {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef() {
        return $this->ref;
    }
    #------------------------------------------------   
   
    /**
     * Set updatedAt
     *
     * @param datetime $updated_at
     * @return SelfieshareFeedback
     */
    public function setUpdatedAt(\dateTime $updated_at) {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    
}