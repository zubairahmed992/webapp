<?php
namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="events_management")
 * 
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\EventManagementRepository")
 */
 
class EventManagement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)     
     */    
    protected $event_name;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;
    
    
    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;

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
     * Set event_name
     *
     * @param string $event_name
     * @return ClothingType
     */
    public function setEventName($event_name)
    {
        $this->event_name = $event_name;
    
        return $this;
    }

    /**
     * Get event_name
     *
     * @return string 
     */
    public function getEventName()
    {
        return $this->event_name;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ClothingType
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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return ClothingType
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
     * Set disabled
     *
     * @param boolean $disabled
     * @return ClothingType
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    
        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled()
    {
        return $this->disabled;
    }
}