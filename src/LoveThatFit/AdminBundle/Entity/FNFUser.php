<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FNFUser
 *
 * @ORM\Table()
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
     * @var float
     *
     * @ORM\Column(name="discount", type="float")
     */
    private $discount;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return FNFUser
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    
        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
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
}