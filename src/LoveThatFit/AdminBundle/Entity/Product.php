<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductRepository")
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 */
class Product {

    /**
     * @ORM\ManyToOne(targetEntity="ClothingType", inversedBy="products")
     * @ORM\JoinColumn(name="clothing_type_id", referencedColumnName="id")
     */
    protected $clothing_type;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    protected $brand;

    
    /**
     * @ORM\OneToMany(targetEntity="ProductColor", mappedBy="product")
     */
    protected $product_colors;
        
    
    
    
    
  /////////////////////////////////////////////////////////////////////////////////  
    
     public function __construct()
    {
        $this->product_colors = new ArrayCollection();
    }
    
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   
    
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
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
     */
    protected $adjustment;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
      */
    protected $sku;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
      */
    protected $waist;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
    */
    protected $hip;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $bust;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $arm;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $leg;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $inseam;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $outseam;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $hem;

    /**
     * @ORM\Column(type="decimal", scale=2,nullable=true)
     */
    protected $back;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $length;

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
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

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
     * Set sku
     *
     * @param string $sku
     * @return Product
     */
    public function setSku($sku) {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Product
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set waist
     *
     * @param float $waist
     * @return Product
     */
    public function setWaist($waist) {
        $this->waist = $waist;

        return $this;
    }

    /**
     * Get waist
     *
     * @return float 
     */
    public function getWaist() {
        return $this->waist;
    }

    /**
     * Set hip
     *
     * @param float $hip
     * @return Product
     */
    public function setHip($hip) {
        $this->hip = $hip;

        return $this;
    }

    /**
     * Get hip
     *
     * @return float 
     */
    public function getHip() {
        return $this->hip;
    }

    /**
     * Set bust
     *
     * @param float $bust
     * @return Product
     */
    public function setBust($bust) {
        $this->bust = $bust;

        return $this;
    }

    /**
     * Get bust
     *
     * @return float 
     */
    public function getBust() {
        return $this->bust;
    }

    /**
     * Set arm
     *
     * @param float $arm
     * @return Product
     */
    public function setArm($arm) {
        $this->arm = $arm;

        return $this;
    }

    /**
     * Get arm
     *
     * @return float 
     */
    public function getArm() {
        return $this->arm;
    }

    /**
     * Set leg
     *
     * @param float $leg
     * @return Product
     */
    public function setLeg($leg) {
        $this->leg = $leg;

        return $this;
    }

    /**
     * Get leg
     *
     * @return float 
     */
    public function getLeg() {
        return $this->leg;
    }

    /**
     * Set inseam
     *
     * @param float $inseam
     * @return Product
     */
    public function setInseam($inseam) {
        $this->inseam = $inseam;

        return $this;
    }

    /**
     * Get inseam
     *
     * @return float 
     */
    public function getInseam() {
        return $this->inseam;
    }

    /**
     * Set outseam
     *
     * @param float $outseam
     * @return Product
     */
    public function setOutseam($outseam) {
        $this->outseam = $outseam;

        return $this;
    }

    /**
     * Get outseam
     *
     * @return float 
     */
    public function getOutseam() {
        return $this->outseam;
    }

    /**
     * Set hem
     *
     * @param float $hem
     * @return Product
     */
    public function setHem($hem) {
        $this->hem = $hem;

        return $this;
    }

    /**
     * Get hem
     *
     * @return float 
     */
    public function getHem() {
        return $this->hem;
    }

    /**
     * Set back
     *
     * @param float $back
     * @return Product
     */
    public function setBack($back) {
        $this->back = $back;

        return $this;
    }

    /**
     * Get back
     *
     * @return float 
     */
    public function getBack() {
        return $this->back;
    }

    /**
     * Set length
     *
     * @param float $length
     * @return Product
     */
    public function setLength($length) {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return float 
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Product
     */
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
     */
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

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------

    public function upload() {
        if (null === $this->file) {
            return;
        }        
        $ih=new ImageHelper('product', $this);
        $ih->upload();
    }

    //------------------------------------------------------------

public function getImagePaths() {
    $ih=new ImageHelper('product', $this);
    return $ih->getImagePaths();        
}
  
  
//-------------------------------------------------------
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }
//-------------------------------------------------------
    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }
//-------------------------------------------------------
    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
//-------------------------------------------------------
    protected function getUploadDir() {
       return 'uploads/ltf/products';            
    }

    //---------------------------------------------------
    
 /**
 * @ORM\postRemove
 */
public function deleteImages()
{
     $ih=new ImageHelper('product', $this);
     $ih->deleteImages($this->image);
}
    

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
}