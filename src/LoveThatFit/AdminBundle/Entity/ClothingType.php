<?php


namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="clothing_type")
 * 
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ClothingTypeRepository")
 */

 
class ClothingType
{
 
  /**
  * @ORM\OneToMany(targetEntity="Product", mappedBy="clothing_type")
  */
    
    
    
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
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    
    protected $target;
    
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
     * Set name
     *
     * @param string $name
     * @return ClothingType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return ClothingType
     */
    public function setTarget($target)
    {
        $this->target = $target;
    
        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
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
    //----------------------------------- sample code for validation
    public function isValid(){
        $msg_array=array();
        $valid=true;
        if($this->name=''){
            array_push($msg_array, array('valid'=>false, 'message'=>'Invalid name'));
            $valid=false;
        }
        
        if($this->target!='Top' || $this->target!='Bottom' || $this->target='Dress' ){
            array_push($msg_array, array('valid'=>false, 'message'=>'Invalid Target'));
            $valid=false;
        }
          
        array_push($msg_array, array('valid'=>$valid));
        
        return $msg_array;
    }
}