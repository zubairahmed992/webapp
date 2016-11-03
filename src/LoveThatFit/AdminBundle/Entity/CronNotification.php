<?php

namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\CronNotificationRepository")
 * @ORM\Table(name="cron_notifications")
 * @ORM\HasLifecycleCallbacks()
 */
class CronNotification {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Cron Type!")
     */
    protected $cron_type;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set CronType
     *
     * @param string $cronType
     * @return CronNotification
     */
    public function setCronType($cronType) {
        $this->cronType = $cronType;
        return $this;
    }

    /**
     * Get Cron Type
     *
     * @return string 
     */
    public function getCronType() {
        return $this->cronType;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CronNotification
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}