<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\AdminBundle\ImageHelper;
/**
 *
 * 
 * @ORM\Table(name="ltf_retailer")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\RetailerRepository")
 */
class Retailer
{
    
    /**
     * @ORM\OneToMany(targetEntity="RetailerUser", mappedBy="retailer", orphanRemoval=true)
     */
    
    protected $retailer_users;
    
    /**
     * @ORM\ManyToMany(targetEntity="Brand", inversedBy="retailers")
     * @ORM\JoinTable(name="retailer_brand")
     * */
    private $brands;
    
     /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="retailer")
     */
    protected $products;
    
    
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

      
     /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $image;
    
    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;
    
    
    /**
     * @var dateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    
    private $createdAt;
    
    /**
     * @var dateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    
    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"add"}, message = "must upload Retailer logo image!") 
     */
    public $file;
    
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
     * Set title
     *
     * @param string $title
     * @return Retailers
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Retailer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Retailer
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->brands = new \Doctrine\Common\Collections\ArrayCollection();
        $this->retailer_users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add retailer_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers
     * @return Retailer
     */
    public function addRetailerUser(\LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers)
    {
        $this->retailer_users[] = $retailerUsers;
    
        return $this;
    }

    /**
     * Remove retailer_users
     *
     * @param \LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers
     */
    public function removeRetailerUser(\LoveThatFit\AdminBundle\Entity\RetailerUser $retailerUsers)
    {
        $this->retailer_users->removeElement($retailerUsers);
    }

    /**
     * Get retailer_users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailerUsers()
    {
        return $this->retailer_users;
    }

    /**
     * Add brands
     *
     * @param \LoveThatFit\AdminBundle\Entity\Brand $brands
     * @return Retailer
     */
    public function addBrand(\LoveThatFit\AdminBundle\Entity\Brand $brands)
    {
        $this->brands[] = $brands;
    
        return $this;
    }

    /**
     * Remove brands
     *
     * @param \LoveThatFit\AdminBundle\Entity\Brand $brands
     */
    public function removeBrand(\LoveThatFit\AdminBundle\Entity\Brand $brands)
    {
        $this->brands->removeElement($brands);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrands()
    {
        return $this->brands;
    }
    
   
    public function getBrandArray()
    {
        $brands = $this->brands;
        $brand_array=array();
       foreach ($brands as $b) {
            //$brand_array[$b->getId()] = $b->getName();            
           $brand_array[$b->getName()] = $b->getId();            
        }
        //asort($brand_array);
        return $brand_array;        
    }
    
    
     public function getBrandNames()
    {
        $brands = $this->brands;        
        $brandname="";
       foreach ($brands as $b) {  
           if(strlen($brandname)>0)
           {
            $brandname=$brandname.', '.$b->getName();            
           }else
           {
               $brandname=$b->getName();            
           }
        }      
        return $brandname;        
    }

    /**
     * Add products
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $products
     * @return Retailer
     */
    public function addProduct(\LoveThatFit\AdminBundle\Entity\Product $products)
    {
        $this->products[] = $products;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $products
     */
    public function removeProduct(\LoveThatFit\AdminBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
    
    
    

    /**
     * Set image
     *
     * @param string $image
     * @return Retailer
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Retailer
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
    
    
      //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        if (null === $this->file) {
            return;
        }        
       $ih=new ImageHelper('retailer', $this);
        $ih->upload();
    }
//---------------------------------------------------
    
  public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->image;
    }
//---------------------------------------------------
    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->image;
    }
//---------------------------------------------------
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
//---------------------------------------------------
    protected function getUploadDir()
    {
        return 'uploads/ltf/retailers/web';
    }
    //---------------------------------------------------
       public function getImagePaths() {
        $ih = new ImageHelper('retailer', $this);        
        return $ih->getImagePaths();
    }
    
 /**
 * @ORM\PostRemove
 */
public function deleteImages()
{
     $ih=new ImageHelper('retailer', $this);
     $ih->deleteImages($this->image);
}
}