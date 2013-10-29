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
     * @ORM\ManyToOne(targetEntity="Retailer", inversedBy="products")
     * @ORM\JoinColumn(name="retailer_id", referencedColumnName="id")
     */
    protected $retailer;

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
     * */
    public $displayProductColor;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemTryHistory", mappedBy="product")
     */
    private $user_item_try_history;

    /////////////////////////////////////////////////////////////////////////////////  

    public function __construct() {
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
     * this needs to be REMOVED~~~~~~~~~~~~X
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
     * @ORM\Column(type="string", length=255)
     */
    protected $styling_type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $hem_length;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $neckline;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $sleeve_styling;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $rise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $stretch_type;

    /**
     * @ORM\Column(type="float",nullable=true)
    */
    protected $horizontal_stretch;

    /**
     * @ORM\Column(type="float",nullable=true)
    */
    protected $vertical_stretch;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fabric_weight;
   
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $layering;


    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $structural_detail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fit_type;


        /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fit_priority;
    
    
        /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fabric_content;
    
    
        /**
     * @ORM\Column(type="string", length=255)
     */
    protected $garment_detail;
    
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
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

//----------------------------------------------------------
    /**
     * Add product_colors
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColors
     * @return Product
     */
    public function addProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColors) {
        $this->product_colors[] = $productColors;

        return $this;
    }

    /**
     * Remove product_colors
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColors
     */
    public function removeProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColors) {
        $this->product_colors->removeElement($productColors);
    }

    /**
     * Get product_colors
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductColors() {
        return $this->product_colors;
    }

    //----------------------------------------------------------

    /**
     * Add product_sizes
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSizes
     * @return Product
     */
    public function addProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSizes) {
        $this->product_sizes[] = $productSizes;

        return $this;
    }

    /**
     * Remove product_sizes
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSizes
     */
    public function removeProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSizes) {
        $this->product_sizes->removeElement($productSizes);
    }

    /**
     * Get product_sizes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductSizes() {
        return $this->product_sizes;
    }

//----------------------------------------------------------
    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return Product
     */
    public function addProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items[] = $productItems;

        return $this;
    }

    /**
     * Remove product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     */
    public function removeProductItem(\LoveThatFit\AdminBundle\Entity\ProductItem $productItems) {
        $this->product_items->removeElement($productItems);
    }

    /**
     * Get product_items
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductItems() {
        return $this->product_items;
    }

    //----------------------------------------------------------
    public static function getSizes() {
        return array('XS', 'S', 'M', 'ML', 'L', 'XL', '2XL', '3XL');
    }

    //----------------------------------------------------------
    public function getSizeByTitle($sizeTitle) {
        $productSizes = $this->getProductSizes();
        foreach ($productSizes as $ps) {
            if ($ps->getTitle() == $sizeTitle) {
                return $ps;
            }
        }
        return;
    }

    //----------------------------------------------------------
    public function getThisItem($color, $size) {
        $productItems = $this->getProductItems();
        foreach ($productItems as $pi) {
            if ($pi->getProductSize()->getId() == $size->getId() && $pi->getProductColor()->getId() == $color->getId()) {
                return $pi;
            }
        }
        return;
    }

    //----------------------------------------------------------
    public function getProductSizeTitleArray() {
        $productSizes = $this->getProductSizes();

        $sizeTitle = array();
        foreach ($productSizes as $ps) {
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
    public function setDisplayProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $displayProductColor = null) {
        $this->displayProductColor = $displayProductColor;

        return $this;
    }

    /**
     * Get displayProductColor
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductColor 
     */
    public function getDisplayProductColor() {
        return $this->displayProductColor;
    }

//----------------------------------------------------------
    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Product
     */
    public function setDisabled($disabled) {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled() {
        return $this->disabled;
    }

    //----------------------------------------------------------
    public function getProductImagePaths() {

        $ar = array();

        foreach ($this->getProductColors() as $pc) {
            $ar['path'] = $pc->getUploadRootDir();
        }

        return $ar;
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
        return $this->getProductItems()->getIterator()->current();
    }

    //----------------------------------------------------------
    public function getDefaultColorRandomItem() {
        return $this->displayProductColor()->getIterator()->current();
    }

    /**
     * Add user_item_try_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     * @return Product
     */
    public function addUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory) {
        $this->user_item_try_history[] = $userItemTryHistory;

        return $this;
    }

    /**
     * Remove user_item_try_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     */
    public function removeUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory) {
        $this->user_item_try_history->removeElement($userItemTryHistory);
    }

    /**
     * Get user_item_try_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserItemTryHistory() {
        return $this->user_item_try_history;
    }

    public function getTryProductCount() {
        return count($this->getUserItemTryHistory());
    }

    public function getdefalutImagePaths() {
        return $this->displayProductColor->getImagePaths();
    }

    #--------Get Default Product Colors----------------#

    public function getDefaultItem() {

        $productColor = $this->getDisplayProductColor();

        foreach ($productColor->getProductItems() as $item) {
            return $item;
        }
        return;
    }

#-----------------Get images path for image downloading------------------------#

    public function getColorImagesPaths() {
        $productColors = $this->getProductColors();

        $imagesPath = array();
        foreach ($productColors as $color) {
            $imagesPath[] = $color->getImagePaths();
            $imagesPath[] = $color->getPatternPaths();
        }
        return $imagesPath;
    }

    public function getItemImagesPaths() {
        $productItems = $this->getProductItems();
        $imagesPath = array();
        foreach ($productItems as $item) {
            $imagesPath[] = $item->getImagePaths();
        }

        return $imagesPath;
    }

    /**
     * Set retailer
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailer
     * @return Product
     */
    public function setRetailer(\LoveThatFit\AdminBundle\Entity\Retailer $retailer = null) {
        $this->retailer = $retailer;

        return $this;
    }

    /**
     * Get retailer
     *
     * @return \LoveThatFit\AdminBundle\Entity\Retailer 
     */
    public function getRetailer() {
        return $this->retailer;
    }


    /**
     * Set styling_type
     *
     * @param string $stylingType
     * @return Product
     */
    public function setStylingType($stylingType)
    {
        $this->styling_type = $stylingType;
    
        return $this;
    }

    /**
     * Get styling_type
     *
     * @return string 
     */
    public function getStylingType()
    {
        return $this->styling_type;
    }

    /**
     * Set hem_length
     *
     * @param string $hemLength
     * @return Product
     */
    public function setHemLength($hemLength)
    {
        $this->hem_length = $hemLength;
    
        return $this;
    }

    /**
     * Get hem_length
     *
     * @return string 
     */
    public function getHemLength()
    {
        return $this->hem_length;
    }

    /**
     * Set neckline
     *
     * @param string $neckline
     * @return Product
     */
    public function setNeckline($neckline)
    {
        $this->neckline = $neckline;
    
        return $this;
    }

    /**
     * Get neckline
     *
     * @return string 
     */
    public function getNeckline()
    {
        return $this->neckline;
    }

    /**
     * Set sleeve_styling
     *
     * @param string $sleeveStyling
     * @return Product
     */
    public function setSleeveStyling($sleeveStyling)
    {
        $this->sleeve_styling = $sleeveStyling;
    
        return $this;
    }

    /**
     * Get sleeve_styling
     *
     * @return string 
     */
    public function getSleeveStyling()
    {
        return $this->sleeve_styling;
    }

    /**
     * Set rise
     *
     * @param string $rise
     * @return Product
     */
    public function setRise($rise)
    {
        $this->rise = $rise;
    
        return $this;
    }

    /**
     * Get rise
     *
     * @return string 
     */
    public function getRise()
    {
        return $this->rise;
    }

    /**
     * Set stretch_type
     *
     * @param string $stretchType
     * @return Product
     */
    public function setStretchType($stretchType)
    {
        $this->stretch_type = $stretchType;
    
        return $this;
    }

    /**
     * Get stretch_type
     *
     * @return string 
     */
    public function getStretchType()
    {
        return $this->stretch_type;
    }

    /**
     * Set horizontal_stretch
     *
     * @param float $horizontalStretch
     * @return Product
     */
    public function setHorizontalStretch($horizontalStretch)
    {
        $this->horizontal_stretch = $horizontalStretch;
    
        return $this;
    }

    /**
     * Get horizontal_stretch
     *
     * @return float 
     */
    public function getHorizontalStretch()
    {
        return $this->horizontal_stretch;
    }

    /**
     * Set vertical_stretch
     *
     * @param float $verticalStretch
     * @return Product
     */
    public function setVerticalStretch($verticalStretch)
    {
        $this->vertical_stretch = $verticalStretch;
    
        return $this;
    }

    /**
     * Get vertical_stretch
     *
     * @return float 
     */
    public function getVerticalStretch()
    {
        return $this->vertical_stretch;
    }

    /**
     * Set fabric_weight
     *
     * @param string $fabricWeight
     * @return Product
     */
    public function setFabricWeight($fabricWeight)
    {
        $this->fabric_weight = $fabricWeight;
    
        return $this;
    }

    /**
     * Get fabric_weight
     *
     * @return string 
     */
    public function getFabricWeight()
    {
        return $this->fabric_weight;
    }

    /**
     * Set structural_detail
     *
     * @param string $structuralDetail
     * @return Product
     */
    public function setStructuralDetail($structuralDetail)
    {
        $this->structural_detail = $structuralDetail;
    
        return $this;
    }

    /**
     * Get structural_detail
     *
     * @return string 
     */
    public function getStructuralDetail()
    {
        return $this->structural_detail;
    }

    /**
     * Set fit_type
     *
     * @param string $fitType
     * @return Product
     */
    public function setFitType($fitType)
    {
        $this->fit_type = $fitType;
    
        return $this;
    }

    /**
     * Get fit_type
     *
     * @return string 
     */
    public function getFitType()
    {
        return $this->fit_type;
    }

    /**
     * Set fit_priority
     *
     * @param string $fitPriority
     * @return Product
     */
    public function setFitPriority($fitPriority)
    {
        $this->fit_priority = $fitPriority;
    
        return $this;
    }

    /**
     * Get fit_priority
     *
     * @return string 
     */
    public function getFitPriority()
    {
        return $this->fit_priority;
    }

    /**
     * Set fabric_content
     *
     * @param string $fabricContent
     * @return Product
     */
    public function setFabricContent($fabricContent)
    {
        $this->fabric_content = $fabricContent;
    
        return $this;
    }

    /**
     * Get fabric_content
     *
     * @return string 
     */
    public function getFabricContent()
    {
        return $this->fabric_content;
    }

    /**
     * Set garment_detail
     *
     * @param string $garmentDetail
     * @return Product
     */
    public function setGarmentDetail($garmentDetail)
    {
        $this->garment_detail = $garmentDetail;
    
        return $this;
    }

    /**
     * Get garment_detail
     *
     * @return string 
     */
    public function getGarmentDetail()
    {
        return $this->garment_detail;
    }

    /**
     * Set layering
     *
     * @param string $layering
     * @return Product
     */
    public function setLayering($layering)
    {
        $this->layering = $layering;
    
        return $this;
    }

    /**
     * Get layering
     *
     * @return string 
     */
    public function getLayering()
    {
        return $this->layering;
    }
}