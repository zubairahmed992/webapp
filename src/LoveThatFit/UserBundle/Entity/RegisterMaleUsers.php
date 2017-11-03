<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LoveThatFit\UserBundle\Entity\RegisterMaleUsers
 *  
 * @ORM\Table(name="register_male_users")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\RegisterMaleUsersRepository")
 */
class RegisterMaleUsers  {


    
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
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return RegisterMaleUsers
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
     * Set first_name
     *
     * @param string $first_name
     * @return RegisterMaleUsers
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


    /**
     * Set last_name
     *
     * @param string $last_name
     * @return RegisterMaleUsers
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
     * Set email
     *
     * @param string $email
     * @return RegisterMaleUsers
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