<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\UserBundle\Entity\UserParentChildLink
 *  
 * @ORM\Table(name="user_parent_child_link")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserParentChildLinkRepository")
 */
class UserParentChildLink
{
    
    
    /**     
     * 
     * @ORM\OneToOne(targetEntity="User", inversedBy="userparentchildlink")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $child;
    
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="userparentchildlink")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $parent;
    
     
    
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
     * @ORM\Column(name="email", type="string", length=32, nullable=true)
     */
    private $email;

    
    /**
     * @var boolean $isApproved
     *
     * @ORM\Column(name="is_approved", type="boolean", nullable=true)
     */
    private $isApproved;
    
    /**
     * @var datetime $approvedAt
     *
     * @ORM\Column(name="approvedAt", type="datetime", nullable=true)    
     */
    private $approvedAt;

    

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
     * Set email
     *
     * @param string $email
     * @return UserParentChildLink
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

    /**
     * Set isApproved
     *
     * @param boolean $isApproved
     * @return UserParentChildLink
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
    
        return $this;
    }

    /**
     * Get isApproved
     *
     * @return boolean 
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set approvedAt
     *
     * @param \DateTime $approvedAt
     * @return UserParentChildLink
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    
        return $this;
    }

    /**
     * Get approvedAt
     *
     * @return \DateTime 
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * Set child
     *
     * @param \LoveThatFit\UserBundle\Entity\User $child
     * @return UserParentChildLink
     */
    public function setChild(\LoveThatFit\UserBundle\Entity\User $child = null)
    {
        $this->child = $child;
    
        return $this;
    }

    /**
     * Get child
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set parent
     *
     * @param \LoveThatFit\UserBundle\Entity\User $parent
     * @return UserParentChildLink
     */
    public function setParent(\LoveThatFit\UserBundle\Entity\User $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getParent()
    {
        return $this->parent;
    }
}