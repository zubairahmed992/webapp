<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\UserBundle\Entity\UserAppAccessLog
 *
 * @ORM\Table("user_app_access_log")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserAppAccessLogRepository")
 */
class UserAppAccessLog
{
  /**
   * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_app_access_log")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */

	private $user;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created_at;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $updated_at;


  	/**
     * @var integer
     *
     * @ORM\Column(name="updated_count", type="integer")
     */
    private $updated_count;


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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return UserAppAccessLog
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return UserAppAccessLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
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

    /**
     * Set updated_count
     *
     * @param integer $updatedCount
     * @return UserAppAccessLog
     */
    public function setUpdatedCount($updatedCount)
    {
        $this->updated_count = $updatedCount;
    
        return $this;
    }

    /**
     * Get updated_count
     *
     * @return integer 
     */
    public function getUpdatedCount()
    {
        return $this->updated_count;
    }

	#---------------------------------------

	/**
	 * Set user
	 *
	 * @param \LoveThatFit\UserBundle\Entity\User $user
	 * @return UserAppAccessLog
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

	#---------------------------------------

}
