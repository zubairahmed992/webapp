<?php

namespace LoveThatFit\SupportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\SupportBundle\Entity\SupportTaskLog
 *
 * @ORM\Table(name="support_task_log")
 * @ORM\Entity(repositoryClass="LoveThatFit\SupportBundle\Entity\SupportTaskLogRepository")
 */
class SupportTaskLog
{    
    public function __construct()
    {
              
    }
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\SupportAdminUser", inversedBy="support_task_log")
     * @ORM\JoinColumn(name="support_admin_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $support_admin_user;
    
    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\UserArchives", inversedBy="support_task_log")
     * @ORM\JoinColumn(name="archives_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $archives_id;
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $member_email
     *
     * @ORM\Column(name="member_email", type="string", length=40,nullable=true)
     */
    private $member_email;

    /**
     * @var string $support_user_name
     *
     * @ORM\Column(name="support_user_name", type="string", length=40,nullable=true)
     */
    private $support_user_name;
    /**
     * @var int $duration
     *
     * @ORM\Column(name="duration", type="integer", length=11,nullable=true)
     */
    private $duration;

    /**
     * @var string $log_type
     *
     * @ORM\Column(name="log_type", type="string", length=40,nullable=true)
     */
    private $log_type;

    /**
     * @var \DateTime $start_time
     *
     * @ORM\Column(name="start_time", type="datetime",nullable=true)
     */
    private $start_time;
    
    /**
     * @var \DateTime $end_time
     *
     * @ORM\Column(name="end_time", type="datetime",nullable=true)
     */
    private $end_time;
    
    
    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime",nullable=true)
     */
    private $created_at;

    
    
#---------------------------------------------------------------------
    /**
     * Get id 
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
#---------------------------------------
    /**
     * Set member_email
     *
     * @param string $member_email
     * @return SupportTaskLog
     */
    public function setMemberEmail($member_email) {
        $this->member_email = $member_email;
        return $this;
    }

    /**
     * Get member_email
     *
     * @return string 
     */
    public function getMemberEmail() {
        return $this->member_email;
    }
    #---------------------------------------
    /**
     * Set support_user_name
     *
     * @param string $support_user_name
     * @return SupportTaskLog
     */
    public function setSupportUserName($support_user_name) {
      $this->support_user_name = $support_user_name;
      return $this;
    }

    /**
     * Get support_user_name
     *
     * @return string
     */
    public function getSupportUserName() {
      return $this->support_user_name;
    }
    #----------------------------------
    
    
    /**
     * Set duration
     *
     * @param int $duration
     * @return SupportTaskLog
     */
    public function setDuration($duration){
        $this->duration = $duration;    
        return $this;
    }

    /**
     * Get duration
     *
     * @return int 
     */
    public function getDuration(){
        return $this->duration;
    }

   #---------------------------------------
    /**
     * Set log_type
     *
     * @param string $log_type
     * @return SupportTaskLog
     */
    public function setLogType($log_type) {
        $this->log_type = $log_type;
        return $this;
    }

    /**
     * Get log_type
     *
     * @return string 
     */
    public function getLogType() {
        return $this->log_type;
    }
    
   #----------------------------------------
    /**
     * Set start_time
     *
     * @param \DateTime $start_time
     * @return SupportTaskLog
     */
    public function setStartTime($start_time){
        $this->start_time = $start_time;    
        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime(){
        return $this->start_time;
    }
#----------------------------------------
    
    /**
     * Set end_time
     *
     * @param \DateTime $end_time
     * @return SupportTaskLog
     */
    public function setEndTime($end_time){
        $this->end_time = $end_time;    
        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime(){
        return $this->end_time;
    }

   #----------------------------------------
    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     * @return SupportTaskLog
     */
    public function setCreatedAt($created_at){
        $this->created_at = $created_at;    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt(){
        return $this->created_at;
    }

    #----------------------------------------
    /**
     * Set archives_id
     *
     * @param LoveThatFit\UserBundle\Entity\UserArchives $archives_id
     * @return archives_id
     * 
     */
    public function setArchives(\LoveThatFit\UserBundle\Entity\UserArchives $archives_id = null) {
        $this->archives_id = $archives_id;

        return $this;
    }

    /**
     * Get archives_id
     *
     * @return LoveThatFit\UserBundle\Entity\UserArchives 
     */
    public function getArchives() {
        return $this->archives_id;
    }
    #--------------------------------------------
  
    /**
     * Set support_admin_user
     *
     * @param LoveThatFit\AdminBundle\Entity\SupportAdminUser $support_admin_user
     * @return SupportAdminUser
     */
    public function setSupportAdminUser(\LoveThatFit\AdminBundle\Entity\SupportAdminUser $support_admin_user = null){
        $this->support_admin_user = $support_admin_user;    
        return $this;
    }

    /**
     * Get support_admin_user
     *
     * @return LoveThatFit\AdminBundle\Entity\SupportAdminUser 
     */
    public function getSupportAdminUser(){
        return $this->support_admin_user;
    }

}