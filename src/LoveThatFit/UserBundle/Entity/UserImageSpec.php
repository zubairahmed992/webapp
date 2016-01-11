<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\UserBundle\Entity\UserImageSpecs
 *  
 * @ORM\Table(name="user_image_spec")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserImageSpecRepository")
 */
class UserImageSpec
{   
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="user_image_spec")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * */   
   
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
     * @var float $camera_angle
     *
     * @ORM\Column(name="camera_angle", type="float", nullable=true)     
     */
    private $camera_angle;
    
    /**
     * @var float $camera_x
     *
     * @ORM\Column(name="camera_x", type="float", nullable=true)     
     */
    private $camera_x;
    
    /**
     * @var float $displacement_x
     *
     * @ORM\Column(name="displacement_x", type="float", nullable=true)     
     */
    private $displacement_x;

     /**
     * @var float $displacement_y
     *
     * @ORM\Column(name="displacement_y", type="float", nullable=true)     
     */
    private $displacement_y;
    
    /**
     * @var float $rotation
     *
     * @ORM\Column(name="rotation", type="float", nullable=true)     
     */
    private $rotation;
    
     /**
     * @var string $deviceType
     *
     * @ORM\Column(name="device_type", type="string", length=60, nullable=true)
     */
    private $deviceType;
    
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return UserMarker
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
     * @return UserMarker
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
#---------------------------------------
    /**
     * Set camera_angle
     * @param float $camera_angle
     * @return UserImageSpec
     */

    public function setCameraAngle($camera_angle){
        $this->camera_angle = $camera_angle;    
        return $this;
    }

    /**
     * Get camera_angle
     * @return float 
     */
    public function getCameraAngle(){
        return $this->camera_angle;
    }
#---------------------------------------
    /**
     * Set camera_x
     * @param float $camera_x
     * @return UserImageSpec
     */
    public function setCameraX($camera_x){
        $this->camera_x= $camera_x;    
        return $this;
    }

    /**
     * Get camera_x
     * @return float 
     */
    public function getCameraX(){
        return $this->camera_x;
    }
    
#---------------------------------------

    /**
     * Set displacement_x
     * @param float $displacement_x
     * @return UserImageSpec
     */
    public function setDisplacementX($displacement_x){
        $this->displacement_x= $displacement_x;    
        return $this;
    }

    /**
     * Get displacement_x
     * @return float 
     */
    public function getDisplacementX(){
        return $this->displacement_x;
    }
#---------------------------------------

    /**
     * Set displacement_y
     * @param float $displacement_y
     * @return UserImageSpec
     */
    public function setDisplacementY($displacement_y){
        $this->displacement_y= $displacement_y;    
        return $this;
    }

    /**
     * Get displacement_y
     * @return float 
     */
    public function getDisplacementY(){
        return $this->displacement_y;
    }    
#---------------------------------------
     /**
     * Set rotation
     * @param float $rotation
     * @return UserImageSpec
     */
    public function setRotation($rotation){
        $this->rotation= $rotation;    
        return $this;
    }

    /**
     * Get rotation
     * @return float 
     */
    public function getRotation(){
        return $this->rotation;
    }
    
#---------------------------------------
    
    /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserImageSpec 
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
    
       /**
     * Set deviceType
     *
     * @param string $deviceType
     * @return UserDevices
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;    
        return $this;
    }

    /**
     * Get deviceType
     *
     * @return string 
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

}