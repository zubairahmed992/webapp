<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\BrandRepository")
 * @ORM\Table(name="brand")
 * @ORM\HasLifecycleCallbacks()
 */
class Brand {
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="brand")
     */
    protected $products;
    /**
     * @ORM\OneToMany(targetEntity="SizeChart", mappedBy="brand")
     */
    protected $sizechart;
    
    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToOne(targetEntity="BrandSpecification", mappedBy="brand", cascade={"ALL"}, orphanRemoval=true)
     * */
    private $brandspecification;
    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToOne(targetEntity="FitModelMeasurement", mappedBy="brand", cascade={"ALL"}, orphanRemoval=true)
     * */
    private $fit_model_measurement;
    
    /**
     * @ORM\ManyToMany(targetEntity="Retailer", mappedBy="brands")
     **/
    private $retailers;
  
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please enter Brand name!")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $image;

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
     * @Assert\NotBlank(groups={"add"}, message = "must upload brand logo image!") 
     */
    public $file;

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
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Brand
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
     * Set image
     *
     * @param string $image
     * @return Brand
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Brand
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
     * @return Brand
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

    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
       $ih=new ImageHelper('brand', $this);
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
    {# the path will be changed to 'uploads/ltf/brands/web'
        return 'uploads/ltf/brands/web';
    }
    //---------------------------------------------------
       public function getImagePaths() {
        $ih = new ImageHelper('brand', $this);        
        return $ih->getImagePaths();
    }
    
 /**
 * @ORM\PostRemove
 */
public function deleteImages()
{
     $ih=new ImageHelper('brand', $this);
     $ih->deleteImages($this->image);
}

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->retailers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sizechart = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    /**
     * Add sizechart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $sizechart
     * @return Brand
     */
    public function addSizechart(\LoveThatFit\AdminBundle\Entity\SizeChart $sizechart)
    {
        $this->sizechart[] = $sizechart;
    
        return $this;
    }

    

    /**
     * Get sizechart
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSizechart()
    {
        return $this->sizechart;
    }

   

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Brand
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

    /**
     * Remove sizechart
     *
     * @param LoveThatFit\AdminBundle\Entity\SizeChart $sizechart
     */
    public function removeSizechart(\LoveThatFit\AdminBundle\Entity\SizeChart $sizechart)
    {
        $this->sizechart->removeElement($sizechart);
    }

    /**
     * Add retailers
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailers
     * @return Brand
     */
    public function addRetailer(\LoveThatFit\AdminBundle\Entity\Retailer $retailers)
    {
        $this->retailers[] = $retailers;
    
        return $this;
    }

    /**
     * Remove retailers
     *
     * @param \LoveThatFit\AdminBundle\Entity\Retailer $retailers
     */
    public function removeRetailer(\LoveThatFit\AdminBundle\Entity\Retailer $retailers)
    {
        $this->retailers->removeElement($retailers);
    }

    /**
     * Get retailers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetailers()
    {
        return $this->retailers;
    }

    /**
     * Add products
     *
     * @param \LoveThatFit\AdminBundle\Entity\Product $products
     * @return Brand
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
     * Add top_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $topBrand
     * @return Brand
     */
    public function addTopBrand(\LoveThatFit\UserBundle\Entity\Measurement $topBrand)
    {
        $this->top_brand[] = $topBrand;
    
        return $this;
    }

    /**
     * Remove top_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $topBrand
     */
    public function removeTopBrand(\LoveThatFit\UserBundle\Entity\Measurement $topBrand)
    {
        $this->top_brand->removeElement($topBrand);
    }

    /**
     * Get top_brand
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTopBrand()
    {
        return $this->top_brand;
    }

    /**
     * Add bottom_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $bottomBrand
     * @return Brand
     */
    public function addBottomBrand(\LoveThatFit\UserBundle\Entity\Measurement $bottomBrand)
    {
        $this->bottom_brand[] = $bottomBrand;
    
        return $this;
    }

    /**
     * Remove bottom_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $bottomBrand
     */
    public function removeBottomBrand(\LoveThatFit\UserBundle\Entity\Measurement $bottomBrand)
    {
        $this->bottom_brand->removeElement($bottomBrand);
    }

    /**
     * Get bottom_brand
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBottomBrand()
    {
        return $this->bottom_brand;
    }

    /**
     * Add dress_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $dressBrand
     * @return Brand
     */
    public function addDressBrand(\LoveThatFit\UserBundle\Entity\Measurement $dressBrand)
    {
        $this->dress_brand[] = $dressBrand;
    
        return $this;
    }

    /**
     * Remove dress_brand
     *
     * @param \LoveThatFit\UserBundle\Entity\Measurement $dressBrand
     */
    public function removeDressBrand(\LoveThatFit\UserBundle\Entity\Measurement $dressBrand)
    {
        $this->dress_brand->removeElement($dressBrand);
    }

    /**
     * Get dress_brand
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDressBrand()
    {
        return $this->dress_brand;
    }
#---------------------------------------------------
    /**
     * Set brandspecification
     *
     * @param \LoveThatFit\AdminBundle\Entity\BrandSpecification $brandspecification
     * @return Brand
     */
    public function setBrandspecification(\LoveThatFit\AdminBundle\Entity\BrandSpecification $brandspecification = null)
    {
        $this->brandspecification = $brandspecification;
    
        return $this;
    }

    /**
     * Get brandspecification
     *
     * @return \LoveThatFit\AdminBundle\Entity\BrandSpecification 
     */
    public function getBrandspecification()
    {
        return $this->brandspecification;
    }
    #---------------------------------------------------
    /**
     * Set fit_model_measurement
     *
     * @param \LoveThatFit\AdminBundle\Entity\FitModelMeasurement $fit_model_measurement
     * @return Brand
     */
    public function setFitModelMeasurement(\LoveThatFit\AdminBundle\Entity\BrandSpecification $fit_model_measurement = null)   {
        $this->fit_model_measurement = $fit_model_measurement;    
        return $this;
    }

    /**
     * Get fit_model_measurement
     *
     * @return \LoveThatFit\AdminBundle\Entity\FitModelMeasurement 
     */
    public function getFitModelMeasurement(){
        return $this->fit_model_measurement;
    }
    
}