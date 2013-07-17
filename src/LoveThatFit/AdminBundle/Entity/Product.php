<?php

namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use LoveThatFit\SiteBundle\Algorithm;
/**
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductRepository")
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 */
class Product {

    /**
     * @ORM\ManyToOne(targetEntity="ClothingType", inversedBy="products")
     * @ORM\JoinColumn(name="clothing_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $clothing_type;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $brand;

    
   /**
     * @ORM\OneToMany(targetEntity="ProductColor", mappedBy="product", orphanRemoval=true)
     */
    protected $product_colors;
    
       /**
     * @ORM\OneToMany(targetEntity="ProductSize", mappedBy="product", orphanRemoval=true)
     */
    protected $product_sizes;
    
      /**
     * @ORM\OneToMany(targetEntity="ProductItem", mappedBy="product", orphanRemoval=true)
     */
    protected $product_items;

    /**
     * @ORM\OneToOne(targetEntity="ProductColor") 
     * @ORM\JoinColumn(name="display_product_color_id", referencedColumnName="id", onDelete="CASCADE")      
     **/
     public $displayProductColor;
     
     /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemTryHistory", mappedBy="product")
     */
    private $user_item_try_history;
    
    
  /////////////////////////////////////////////////////////////////////////////////  
    
     public function __construct()
    {
        $this->product_colors = new ArrayCollection();
        $this->product_sizes = new ArrayCollection();
        $this->product_items = new ArrayCollection();
        $this->user_item_try_history = new ArrayCollection();
        
    }
    
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   
    
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"product_detail"})
     */
    protected $name;
    
    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\Regex(pattern= "/[0-9]/", message="Require number only") 
     */
    protected $adjustment;     

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $gender;

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
    //----------------------------------------------------------
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
//----------------------------------------------------------
    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }
//----------------------------------------------------------
    /**
     * Set adjustment
     *
     * @param float $adjustment
     * @return Product
     */
    public function setAdjustment($adjustment) {
        $this->adjustment = $adjustment;

        return $this;
    }

    /**
     * Get adjustment
     *
     * @return float 
     */
    public function getAdjustment() {
        return $this->adjustment;
    }
    /**
     * Set gender
     *
     * @param string $gender
     * @return Product
     */
    //----------------------------------------------------------
    public function setGender($gender) {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Product
     */
    //----------------------------------------------------------
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Product
     */
    //----------------------------------------------------------
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }
//----------------------------------------------------------
    /**
     * Set clothing_type
     *
     * @param LoveThatFit\AdminBundle\Entity\Brand $clothingType
     * @return Product
     */
    public function setClothingType(\LoveThatFit\AdminBundle\Entity\ClothingType $clothingType = null) {
        $this->clothing_type = $clothingType;

        return $this;
    }

    /**
     * Get clothing_type
     *
     * @return LoveThatFit\AdminBundle\Entity\Brand 
     */
    public function getClothingType() {
        return $this->clothing_type;
    }

    /**
     * Set brand
     *
     * @param LoveThatFit\AdminBundle\Entity\Brand $brand
     * @return Product
     * 
     */
    
    //----------------------------------------------------------
    public function setBrand(\LoveThatFit\AdminBundle\Entity\Brand $brand = null) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return LoveThatFit\AdminBundle\Entity\Brand 
     */
    public function getBrand() {
        return $this->brand;
    }
//----------------------------------------------------------
    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
//----------------------------------------------------------
    /**
     * Add product_colors
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColors
     * @return Product
     */
    public function addProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColors)
    {
        $this->product_colors[] = $productColors;
    
        return $this;
    }

    /**
     * Remove product_colors
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColors
     */
    public function removeProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColors)
    {
        $this->product_colors->removeElement($productColors);
    }

    /**
     * Get product_colors
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductColors()
    {
        return $this->product_colors;
    }
    //----------------------------------------------------------

    /**
     * Add product_sizes
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSizes
     * @return Product
     */
    public function addProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSizes)
    {
        $this->product_sizes[] = $productSizes;
    
        return $this;
    }

    /**
     * Remove product_sizes
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSizes
     */
    public function removeProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSizes)
    {
        $this->product_sizes->removeElement($productSizes);
    }

    /**
     * Get product_sizes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductSizes()
    {
        return $this->product_sizes;
    }
//----------------------------------------------------------
    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return Product
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items[] = $productItems;
    
        return $this;
    }

    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems)
    {
        $this->product_items->removeElement($productItems);
    }

    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems()
    {
        return $this->product_items;
    }
    //----------------------------------------------------------
  public static function getSizes(){
        return array('XS', 'S', 'M', 'ML', 'L', 'XL', '2XL', '3XL');
    }
    //----------------------------------------------------------
    public function getSizeByTitle($sizeTitle)
    {
        $productSizes=$this->getProductSizes();
        foreach ($productSizes as $ps) {
            if ($ps->getTitle()==$sizeTitle){
                return $ps;
            }            
        }
        return;
    }
    //----------------------------------------------------------
    public function getThisItem($color, $size)
    {
        $productItems=$this->getProductItems();
        foreach ($productItems as $pi) {
            if ($pi->getProductSize()->getId()==$size->getId() && $pi->getProductColor()->getId()==$color->getId()){
                return $pi;
            }            
        }
        return;
    }
    //----------------------------------------------------------
    public function getProductSizeTitleArray(){
        $productSizes=$this->getProductSizes();
        
        $sizeTitle=array();
        foreach($productSizes as $ps){            
            array_push($sizeTitle, $ps->getTitle());
        }
    
        return $sizeTitle;
    }
  
//----------------------------------------------------------
    /**
     * Set displayProductColor
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $displayProductColor
     * @return Product
     */
    public function setDisplayProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $displayProductColor = null)
    {
        $this->displayProductColor = $displayProductColor;
    
        return $this;
    }

    /**
     * Get displayProductColor
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductColor 
     */
    public function getDisplayProductColor()
    {
        return $this->displayProductColor;
    }
//----------------------------------------------------------
    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Product
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
    //----------------------------------------------------------
    public function getUserFittingSize($user) {

        $fitting_algo = new Algorithm($user, null);
     
        foreach ($this->getProductSizes() as $ps) {
            $fitting_algo->setProductMeasurement($ps);
            if ($fitting_algo->fit()) {
                return $ps;
            }
        }
        return null;
    }
    
     //----------------------------------------------------------
    public function getRandomItem() {

       $i=$this->getProductItems();
      return $this->getProductItems()->getIterator()->current(); 
    }
    

    /**
     * Add user_item_try_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     * @return Product
     */
    public function addUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history[] = $userItemTryHistory;
    
        return $this;
    }

    /**
     * Remove user_item_try_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     */
    public function removeUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history->removeElement($userItemTryHistory);
    }

    /**
     * Get user_item_try_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserItemTryHistory()
    {
        return $this->user_item_try_history;
    }
    
    public function getTryProductCount()
    {
        return count($this->getUserItemTryHistory());
    }
    public function  getdefalutImagePaths()
    {
        return $this->displayProductColor->getImagePaths();
    }
    #--------Get Default Product Colors----------------#
    public function getDefaultItem() {
    
        $productColor = $this->getDisplayProductColor();
        
       foreach($productColor->getProductItems() as $item)
       {
           return  $item;
       }
     return  ;
    }
    
    
}