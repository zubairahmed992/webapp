<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="ltf_support_user")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SupportAdminUserRepository")
 */
class SupportAdminUser implements UserInterface, \Serializable {

    /**
     * @ORM\OneToMany(targetEntity="\LoveThatFit\SupportBundle\Entity\SupportTaskLog", mappedBy="ltf_support_user")
     */
    protected $support_admin_user;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true, nullable=false)
     * @Assert\Email(groups={"registration_step_one"}, message="Please provide a valid email")
     * @Assert\NotBlank(groups={"registration_step_one"}, message="Email cannot be blank")
     */
    private $email;

    /**
     * @var string $user_name
     *
     * @ORM\Column(name="user_name", type="string", length=60, nullable=false)
     * @Assert\NotBlank(groups={"registration_step_one"}, message="User Name cannot be blank")
     */
    private $user_name;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=40)
     *      @Assert\Length(
     *      min = "6",
     *      max = "50",
     *      minMessage = "Password must contain at least 8 characters, including one number, one upper-case and one lower-case alphabet.",
     *      maxMessage = "Password cannot be longer than than {{ limit }} characters long",
     *      groups={"registration_step_one"}
     * )
     *      @Assert\NotBlank(groups={"registration_step_one"}, message="Password cannot be blank")
     */
    private $password;

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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    //-----------------------------------------------------
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SupportAdminUser
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SupportAdminUser
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SupportAdminUser
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

    /**
     * Set salt
     *
     * @param string $salt
     * @return SupportAdminUser
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SupportAdminUser
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return SupportAdminUser
     */
    public function setDisabled($disabled) {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled() {
        return $this->disabled;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUserName() {
        return $this->user_name;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return array('ROLE_SUPPORT');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {

    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
                    $this->id,
                ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                ) = unserialize($serialized);
    }


    /**
     * Set user_name
     *
     * @param string $username
     * @return SupportAdminUser
     */
    public function setUserName($username)
    {
        $this->user_name = $username;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return SupportAdminUser
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

//    /**
//     * Set name
//     *
//     * @param string $name
//     * @return SupportAdminUser
//     */
//    public function setUserName($name)
//    {
//        $this->name = $username;
//
//        return $this;
//    }
//
//    /**
//     * Get name
//     *
//     * @return string
//     */
//    public function getUserName()
//    {
//        return $this->user_name;
//    }

    public $old_password;

    public function getOldpassword() {
        return $this->old_password;
    }

    /**
     * Set support_admin_user
     *
     * @param LoveThatFit\SupportBundle\Entity\SupportTaskLog $support_admin_user
     * @return support_admin_user
     * 
     */
    //----------------------------------------------------------
    public function setSupportAdminUser(\LoveThatFit\SupportBundle\Entity\SupportTaskLog $support_admin_user = null) 
    {
        $this->support_admin_user = $support_admin_user;
        return $this;
    }

    /**
     * Get support_admin_user
     *
     * @return LoveThatFit\SupportBundle\Entity\SupportTaskLog 
     */
    public function getSupportAdminUser() {
        return $this->support_admin_user;
    }

}