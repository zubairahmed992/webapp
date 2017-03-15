<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FNFUser
 *
 * @ORM\Table(name="fnf_user")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\FNFUserRepository")
 */
class FNFUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="is_available", type="boolean")
     */
    private $is_available = true;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="fnfusers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $users;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="FNFGroup", inversedBy="fnfUsers")
     * @ORM\JoinTable(name="fnfusers_groups")
     */
    private $groups;

    /**
     * @ORM\Column(name="is_archive", type="boolean")
     */

    private $isArchive = false;

    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set is_available
     *
     * @param integer $isAvailable
     * @return FNFUser
     */
    public function setIsAvailable($isAvailable)
    {
        $this->is_available = $isAvailable;
    
        return $this;
    }

    /**
     * Get is_available
     *
     * @return integer 
     */
    public function getIsAvailable()
    {
        return $this->is_available;
    }

    /**
     * Set users
     *
     * @param \LoveThatFit\UserBundle\Entity\User $users
     * @return FNFUser
     */
    public function setUsers(\LoveThatFit\UserBundle\Entity\User $users = null)
    {
        $this->users = $users;
    
        return $this;
    }

    /**
     * Get users
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
     * Add groups
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFGroup $groups
     * @return FNFUser
     */
    public function addGroup(\LoveThatFit\AdminBundle\Entity\FNFGroup $groups)
    {
        $this->groups[] = $groups;
    
        return $this;
    }

    /**
     * Remove groups
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFGroup $groups
     */
    public function removeGroup(\LoveThatFit\AdminBundle\Entity\FNFGroup $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set isArchive
     *
     * @param boolean $isArchive
     * @return FNFUser
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;
    
        return $this;
    }

    /**
     * Get isArchive
     *
     * @return boolean 
     */
    public function getIsArchive()
    {
        return $this->isArchive;
    }
}