<?php

namespace LoveThatFit\WebServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLog
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LoveThatFit\WebServiceBundle\Entity\UserLogRepository")
 */
class UserLog
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
     * @ORM\Column(name="appName", type="string", length=150)
     */
    private $appName;

    /**
     * @var string
     *
     * @ORM\Column(name="sessionId", type="string", length=150)
     */
    private $sessionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="loginAt", type="datetime")
     */
    private $loginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logoutAt", type="datetime")
     */
    private $logoutAt;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="user_log")
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
     * Set appName
     *
     * @param string $appName
     * @return UserLog
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    
        return $this;
    }

    /**
     * Get appName
     *
     * @return string 
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Set loginAt
     *
     * @param \DateTime $loginAt
     * @return UserLog
     */
    public function setLoginAt($loginAt)
    {
        $this->loginAt = $loginAt;
    
        return $this;
    }

    /**
     * Get loginAt
     *
     * @return \DateTime 
     */
    public function getLoginAt()
    {
        return $this->loginAt;
    }

    /**
     * Set logoutAt
     *
     * @param \DateTime $logoutAt
     * @return UserLog
     */
    public function setLogoutAt($logoutAt)
    {
        $this->logoutAt = $logoutAt;
    
        return $this;
    }

    /**
     * Get logoutAt
     *
     * @return \DateTime 
     */
    public function getLogoutAt()
    {
        return $this->logoutAt;
    }

    /**
     * Set users
     *
     * @param \LoveThatFit\UserBundle\Entity\User $users
     * @return UserLog
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
     * Set sessionId
     *
     * @param string $sessionId
     * @return UserLog
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    
        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
}