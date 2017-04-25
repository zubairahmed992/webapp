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
     * @ORM\ManyToMany(targetEntity="LoveThatFit\AdminBundle\Entity\Categories", mappedBy="category_products")
     **/
    private $categories;

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
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserFittingRoomItem", mappedBy="product_id")
     */
    private $user_fitting_room_ittem;
    
    /**
     * @ORM\OneToMany(targetEntity="ProductColorView", mappedBy="product", orphanRemoval=true)
     */
    protected $product_color_views;

    /**
     * @ORM\OneToMany(targetEntity="ProductSize", mappedBy="product", orphanRemoval=true)
     */
    protected $product_sizes;

    /**
     * @ORM\OneToMany(targetEntity="ProductItem", mappedBy="product", orphanRemoval=true)
     */
    protected $product_items;
    
    /**
     * @ORM\OneToMany(targetEntity="ProductImage", mappedBy="product", orphanRemoval=true)
     */
    protected $product_image;

    /**
     * @ORM\OneToOne(targetEntity="ProductColor") 
     * @ORM\JoinColumn(name="display_product_color_id", referencedColumnName="id", onDelete="CASCADE")      
     * */
    public $displayProductColor;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemTryHistory", mappedBy="product")
     */
    private $user_item_try_history;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemFavHistory", mappedBy="product")
     */
    private $user_item_fav_history;

    /////////////////////////////////////////////////////////////////////////////////  

    public function __construct() {
        $this->product_colors = new ArrayCollection();
        $this->product_sizes = new ArrayCollection();
        $this->product_items = new ArrayCollection();
        $this->user_item_try_history = new ArrayCollection();
        $this->user_item_fav_history = new ArrayCollection();
        $this->product_image = new ArrayCollection();
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"product_detail"})
     */
    protected $control_number;
    
    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $styling_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $hem_length;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $neckline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sleeve_styling;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $rise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stretch_type;

    /**
     * @ORM\Column(type="float", nullable=true)
    */
    protected $horizontal_stretch;

    /**
     * @ORM\Column(type="float", nullable=true)
    */
    protected $vertical_stretch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fabric_weight;
   
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $layering;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $structural_detail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fit_type;


        /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fit_priority;
    
    
        /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fabric_content;
    
    
        /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $garment_detail;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $size_title_type;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=true)
     */
    private $disabled;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $product_model_height;
    
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    protected $retailer_reference_id;

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
     * Set control_number
     *
     * @param string $name
     * @return Product
     */
    public function setControlNumber($control_number) {
        $this->control_number = $control_number;

        return $this;
    }

    /**
     * Get control_number
     *
     * @return string 
     */
    public function getControlNumber() {
        return $this->control_number;
    }


//----------------------------------------------------------
    
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


#--------------------------------------------------------------------------------
    /**
     * Add user_fitting_room_ittem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem
     * @return ProductItem
     */
    public function addUserFittingRoomIttem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem)
    {
        $this->user_fitting_room_ittem[] = $userFittingRoomIttem;

        return $this;
    }

    /**
     * Remove user_fitting_room_ittem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem
     */
    public function removeUserFittingRoomIttem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem)
    {
        $this->user_fitting_room_ittem->removeElement($userFittingRoomIttem);
    }

    /**
     * Get user_fitting_room_ittem
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserFittingRoomIttem()
    {
        return $this->user_fitting_room_ittem;
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

    public function getFitPriorityArray(){
        return json_decode($this->fit_priority, true);
    }
    public function getFitPointsWithPriority(){
        $fps = $this->getFitPriorityArray();
        $fpa=array();
        if (is_array($fps)){
            foreach ($fps as $k=>$v){
                if($v>0){
                $fpa[$k]=$v;    
                }
            }
        }
        return $fpa;
    }
    #~~~~~~~~~~~~~~~~~~~~~~~>
    public function fitPriorityAvailable(){
        $fps = $this->getFitPriorityArray();
        if (is_array($fps)){
            foreach ($fps as $k=>$v){
                if($v>0){
                    return true;
                }
            }
        }
        return false;
    }
    
    public function getFitPriorityLowerCase(){        
        return str_replace('_', ' ', strtolower($this->fit_priority));
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
    
    /* Set product_model_height
     *
     * @param string $productModelHeight
     * @return Product
     */
    public function setProductModelHeight($productModelHeight)
    {
        $this->product_model_height = $productModelHeight;
    
        return $this;
    }

    /**
     * Get product_model_height
     *
     * @return string 
     */
    public function getProductModelHeight()
    {
        return $this->product_model_height;
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
 

    /**
     * Set size_title_type
     *
     * @param string $sizeTitleType
     * @return Product
     */
    public function setSizeTitleType($sizeTitleType)
    {
        $this->size_title_type = $sizeTitleType;
    
        return $this;
    }

    /**
     * Get size_title_type
     *
     * @return string 
     */
    public function getSizeTitleType()
    {
        return $this->size_title_type;
    }
    
    
    
    
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 public function getDetailArray(){
        $product=$this->getAttributeArray();
        $product['sizes']=$this->getProductSizeDescriptionArray();
        $product['colors']=$this->getColorArray();
        $product['items']=$this->getItemArray();
        return $product;
    }
    
    public function getAttributeArray(){
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand->getName(),
            'brand_url' => $this->brand->getImage(),
            'description' => $this->description,
            'gender' => $this->gender,
            'clothing_type' => $this->clothing_type->getName(),
            'target' => $this->clothing_type->getTarget(),
            'styling_type' => $this->styling_type,
            'hem_length' => $this->hem_length,
            'neckline' => $this->neckline,
            'sleeve_styling' => $this->sleeve_styling,
            'rise' => $this->rise,
            'stretch_type' => $this->stretch_type,
            'fabric_weight' => $this->fabric_weight,
            'layering' => $this->layering,
            'structural_detail' => $this->structural_detail,
            'fit_type' => $this->fit_type,
            'size_title_type' => $this->size_title_type,   
            'display_image' => $this->displayProductColor->getWebPath(),
        );
    }
        //----------------------------------------------------------
    public function getItemArray() {
        $productItems = $this->getProductItems();
        $items_array = array();
        foreach ($productItems as $pi) {
            $items_array[$pi->getId()] = array(
                'id' => $pi->getId(), 
                'size_id' => $pi->getProductSize()->getId(), 
                'color_id' => $pi->getProductColor()->getId(), 
                'image_url'=>$pi->getWebPath());
        }
        return $items_array;
    }
        //----------------------------------------------------------
    public function getColorArray() {
        $productColors = $this->getProductColors();
        $colors_array = array();
        foreach ($productColors as $pc) {
            $colors_array[$pc->getId()] = array(
                'id' => $pc->getId(), 
                'title' => $pc->getTitle(), 
                'image' => $pc->getWebPath(), 
                'pattern'=>$pc->getPatternWebPath(),
                'sizes'=> array_flip($pc->getSizeDescriptionArray()),
                );
        }
        return $colors_array;
    }
         //----------------------------------------------------------
    public function getProductSizeDescriptionArray() {
        $productSizes = $this->getProductSizes();
        $sizeTitle = array();
        foreach ($productSizes as $ps) {
            $sizeTitle[$ps->getBodyType() . ' ' . $ps->getTitle()]=$ps->getId();
        }
        return $sizeTitle;
    }
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
        
    public function getSizeByTitleBaseBodyType($sizeTitle, $bodyType) {
        $productSizes = $this->getProductSizes();

        foreach ($productSizes as $ps) {
            if (strcmp(strtolower($ps->getTitle()),strtolower($sizeTitle))==0 and $ps->getBodyType() == $bodyType) {
                return $ps;
            }
        }
        return;
    }
    //----------------------------------------------------------
    #----!!! This method used for 
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
    public function getProductSizeDetailArray() {
        $productSizes = $this->getProductSizes();

        $sizeTitle = array();
        foreach ($productSizes as $ps) {
            $sizeTitle[$ps->getId()] = array('title' => $ps->getTitle(), 'description' => $ps->getTitle(), 'body_type' => $ps->getBodyType());
        }
        return $sizeTitle;
    }
    //----------------------------------------------------------
    public function getProductSizesLayeredArray() {
        $productSizes = $this->getProductSizes();

        $sizeTitle = array();
        foreach ($productSizes as $ps) {
            $sizeTitle[$ps->getBodyType()][$ps->getTitle()]=$ps->getTitle(); 
            }
        return $sizeTitle;
    
    }
    //----------------------------------------------------------
    public function getProductSizeTitleFitPointArray($fit_point, $body_type) {
        $productSizes = $this->getProductSizes();
        $sizeTitle = array();
        $body_type=  strtolower($body_type);
        foreach ($productSizes as $ps) {            
            if ($body_type==strtolower($ps->getBodyType()))
                $sizeTitle [$ps->getTitle()] = $ps->getFitPointMeasurements($fit_point);
        }
        return $sizeTitle;
    }
    
    //----------------------------------------------------------
    public function getProductImagePaths() {

        $ar = array();

        foreach ($this->getProductColors() as $pc) {
            $ar['path'] = $pc->getUploadRootDir();
        }

        return $ar;
    }

//----------------------------------------------------------??? if Default color missing
    public function getUserFittingItem($user) {

        $fe = new \LoveThatFit\SiteBundle\FitEngine($user, null);
        $item = $fe->getFittingItem($this);
        return $item;
        
    }
//----------------------------------------------------------??? if Default color missing
    public function getComparisonUserItem($user) {

        #$comp = new \LoveThatFit\SiteBundle\AvgAlgorithm($user, $this);
        $comp = new \LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2($user, $this);
        $fb = $comp->getFeedBack();
        if (array_key_exists('recommendation', $fb)){                        
            $item=$this->displayProductColor->getItemBySizeId($fb['recommendation']['id']);
            return $item;
        }else{
            return null;
        }
        
        /*$comp = new \LoveThatFit\SiteBundle\Comparison($user, $this);
        $fb = $comp->getFeedBack();
        if (array_key_exists('best_fit', $fb)){                        
            $item=$this->displayProductColor->getItemBySizeId($fb['best_fit']['id']);
            return $item;
        }else{
            return null;
        }*/
        
    }    
    #--------Get Default Product Colors----------------#    ???   if Default color missing
    public function getDefaultItem($user = null) {        
        $default_item = null;        
        if ($user != null) {
            $default_item = $this->getComparisonUserItem($user);
            #$default_item = $this->getUserFittingItem($user);
        }
        if ($user == null || $default_item == null) {
            $default_item=$this->getDefaultColorFirstItem();
        }
        # incase if default color is not available?????
        return $default_item;
    }
    
     #--------Temporary function to support old algorithm in devices----------------#    
    public function getDefaultItemForDevice($user = null) {
        $default_item = null;
        if ($user != null) {
            $comp = new \LoveThatFit\SiteBundle\AvgAlgorithm($user, $this);
            $fb = $comp->getFeedBack();
            $default_item = array_key_exists('recommendation', $fb)?$this->displayProductColor->getItemBySizeId($fb['recommendation']['id']):null;
        }
        if ($user == null || $default_item == null) {
            $default_item = $this->getDefaultColorFirstItem();
        }
        return $default_item;
    }

    //----------------------------------------------------------
    public function getRandomItem() {
        return $this->getProductItems()->getIterator()->current();
    }

    //----------------------------------------------------------???  if Default color missing
    public function getDefaultColorRandomItem() {
        return $this->displayProductColor()->getIterator()->current();
    }

    //---------------------------------------------------------- ?? if Default color missing
    public function getDefaultColorFirstItem() {
          $productColor = $this->getDisplayProductColor();
            foreach ($productColor->getProductItems() as $item) {
                return $item;
            }
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
#----------------------------------------------------------------
    public function getItemImagesPaths() {
        $productItems = $this->getProductItems();
        $imagesPath = array();
        foreach ($productItems as $item) {
            $imagesPath[] = $item->getImagePaths();
        }

        return $imagesPath;
    }
//----------------------------------------------------------
    public function getIdsArray() {
        $productItems = $this->getProductItems();
        $item_array = array();
        $size_array = array();
        $color_array = array();
        foreach ($productItems as $pi) {


            $size_array[$pi->getProductSize()->getId()] =  $pi->getProductSize()->getTitle();
            $color_array[$pi->getProductColor()->getId()] =  $pi->getProductColor()->getTitle();
            $item_array[$pi->getProductColor()->getId()] [$pi->getProductSize()->getId()] = array(
                'id' => $pi->getId(),
            );
        }

        return array('item'=> $item_array, 'size'=> $size_array, 'color'=> $color_array);
    }
    /**
     * Add product_color_view
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView
     * @return Product
     */
    public function addProductColorView(\LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView)
    {
        $this->product_color_view[] = $productColorView;
    
        return $this;
    }

    /**
     * Remove product_color_view
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView
     */
    public function removeProductColorView(\LoveThatFit\AdminBundle\Entity\ProductColorView $productColorView)
    {
        $this->product_color_view->removeElement($productColorView);
    }

    /**
     * Get product_color_view
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductColorView()
    {
        return $this->product_color_view;
    }
    
    
     //----------------------------------------------------------

    /**
     * Add product_image
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductImage $productImages
     * @return Product
     */
    public function addProductImage(\LoveThatFit\AdminBundle\Entity\ProductImage $productImages) {
        $this->product_image[] = $productImages;

        return $this;
    }

    /**
     * Remove product_images
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductImage $productImages
     */
    public function removeProductImage(\LoveThatFit\AdminBundle\Entity\ProductImage $productImages) {
        $this->product_image->removeElement($productImages);
    }

    /**
     * Get product_images
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getProductImages() {
        return $this->product_image;
    }
    
    

    /**
     * Set retailer_reference_id
     *
     * @param integer $retailerReferenceId
     * @return Product
     */
    public function setRetailerReferenceId($retailerReferenceId)
    {
        $this->retailer_reference_id = $retailerReferenceId;
    
        return $this;
    }

    /**
     * Get retailer_reference_id
     *
     * @return integer 
     */
    public function getRetailerReferenceId()
    {
        return $this->retailer_reference_id;
    }
    
     public function toArray(){
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,            
            'control_number' => $this->control_number,
            'brand_id' => $this->brand->getId(),            
            'brand_name' => $this->brand->getName(),            
            'retailer_id' => $this->retailer?$this->retailer->getId():$this->retailer,            
            'retailer_name' => $this->retailer?$this->retailer->getTitle():$this->retailer,            
            'styling_type' => $this->styling_type,
            'neckline' => $this->neckline,
            'sleeve_styling' => $this->sleeve_styling,
            'rise' => $this->rise,
            'hem_length' => $this->hem_length,
            'stretch_type' => $this->stretch_type,            
            'horizontal_stretch'=> $this->horizontal_stretch,
            'vertical_stretch'=> $this->vertical_stretch,
            'fabric_weight' => $this->fabric_weight,
            'structural_detail' => $this->structural_detail,            
            'fit_type' => $this->fit_type,
            'layering' => $this->layering,
            'fit_priority'=> $this->fit_priority,
            'fabric_content'=> $this->fabric_content,
            'garment_detail'=> $this->garment_detail,
            'size_title_type' => $this->size_title_type,               
            'description' => $this->description,
            'clothing_type_id' => $this->clothing_type->getId(),
            'clothing_type' => $this->clothing_type->getName(),
            'target' => $this->clothing_type->getTarget(),
            'layering' => $this->layering,                        
        );
    }

    /*
     * This function use in EvaluationPopUpProductsType & EvaluationDefaultProductsType
     * for generate product with their controller number
     */
    public function getNameAndController()
    {

        return $this->name .' ('. $this->control_number.' # '.$this->brand->getName().')';
    }

    /**
     * Add categories
     *
     * @param \LoveThatFit\AdminBundle\Entity\Categories $categories
     * @return Product
     */
    public function addCategorie(\LoveThatFit\AdminBundle\Entity\Categories $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \LoveThatFit\AdminBundle\Entity\Categories $categories
     */
    public function removeCategorie(\LoveThatFit\AdminBundle\Entity\Categories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get product_color_views
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductColorViews()
    {
        return $this->product_color_views;
    }

    /**
     * Get product_image
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductImage()
    {
        return $this->product_image;
    }

    /**
     * Add user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     * @return Product
     */
    public function addUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history[] = $userItemFavHistory;
    
        return $this;
    }

    /**
     * Remove user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     */
    public function removeUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history->removeElement($userItemFavHistory);
    }

    /**
     * Get user_item_fav_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserItemFavHistory()
    {
        return $this->user_item_fav_history;
    }
}