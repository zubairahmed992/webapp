<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FNFGroup
 *
 * @ORM\Table(name="fnf_group")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\FNFGroupRepository")
 */
class FNFGroup
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
     * @var string
     *
     * @ORM\Column(name="groupTitle", type="string", length=150)
     */
    private $groupTitle;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float")
     */
    private $discount;

    /**
     * @var float
     *
     * @ORM\Column(name="min_amount", type="float")
     */
    private $min_amount;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="FNFUser", mappedBy="groups")
     */
    private $fnfUsers;


    /**
     * @ORM\Column(name="is_archive", type="boolean")
     */

    private $isArchive = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetimetz")
     */

    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetimetz")
     */

    private $endAt;


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
     * Set groupTitle
     *
     * @param string $groupTitle
     * @return FNFGroup
     */
    public function setGroupTitle($groupTitle)
    {
        $this->groupTitle = $groupTitle;
    
        return $this;
    }

    /**
     * Get groupTitle
     *
     * @return string 
     */
    public function getGroupTitle()
    {
        return $this->groupTitle;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return FNFGroup
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
     * Set min_amount
     *
     * @param float $minAmount
     * @return FNFGroup
     */
    public function setMinAmount($minAmount)
    {
        $this->min_amount = $minAmount;

        return $this;
    }

    /**
     * Get min_amount
     *
     * @return float
     */
    public function getMinAmount()
    {
        return $this->min_amount;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fnfUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add fnfUsers
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFUser $fnfUsers
     * @return FNFGroup
     */
    public function addFnfUser(\LoveThatFit\AdminBundle\Entity\FNFUser $fnfUsers)
    {
        $this->fnfUsers[] = $fnfUsers;
    
        return $this;
    }

    /**
     * Remove fnfUsers
     *
     * @param \LoveThatFit\AdminBundle\Entity\FNFUser $fnfUsers
     */
    public function removeFnfUser(\LoveThatFit\AdminBundle\Entity\FNFUser $fnfUsers)
    {
        $this->fnfUsers->removeElement($fnfUsers);
    }

    /**
     * Get fnfUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFnfUsers()
    {
        return $this->fnfUsers;
    }

    /**
     * Set isArchive
     *
     * @param boolean $isArchive
     * @return FNFGroup
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

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return FNFGroup
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    
        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return FNFGroup
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    
        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }
}