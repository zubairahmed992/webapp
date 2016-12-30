<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductImageRepository")
 * @ORM\Table(name="product_image")
 * @ORM\HasLifecycleCallbacks() 
 */
class ProductImage
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_image")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */    
    protected $product; 
   
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    #----------------------------------------------------------------------------------------
    /**
     * @var string $image_tittle
     *
     * @ORM\Column(name="image_title", type="string", nullable=true)
     */
    private $image_title;
#----------------------------------------------------------------------------------------
    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
   #----------------------------------------------------------------------------------------
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    #----------------------------------------------------------------------------------------
    /**
     * @var integer $imageSort
     * @ORM\Column(name="image_sort", type="integer", nullable=true,options={"default" = 0})
     */
    private $image_sort;
    
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
     * Set image_title
     *
     * @param string $imageTitle
     * @return ProductImage
     */
    public function setImageTitle($imageTitle)
    {
        $this->image_title = $imageTitle;
    
        return $this;
    }

    /**
     * Get image_title
     *
     * @return string 
     */
    public function getImagaeTitle()
    {
        return $this->image_title;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return image
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
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductItem
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return LoveThatFit\AdminBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    //---------------------------------------------------
    
     public function upload() {         
            $ih = new ImageHelper('banner', $this);    
            ///print_r($ih->upload());
            //die("Asdfasdf");
            $ih->upload(); // save & resize images 
    }
    
    /**
     * @ORM\PostRemove
     */
    public function deleteImages()
    {
        $target_path = str_replace('\\', '/', getcwd()). '/uploads/ltf/product_models/';
        if($this->image)
        {
            $generated_file_name= $target_path.$this->image;
            if (is_readable($generated_file_name )){
                    @unlink($generated_file_name );    
                }
        }
    }
    
    /**
     * Set image_sort
     *
     * @param integer $imageSort
     * @return ProductImage
     */
    public function setImageSort($imageSort) {
      
            $this->image_sort = $imageSort;
             return $this;
         
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * Get image_sort
     *
     * @return integer
     */
    public function getImageSort() {

        return $this->image_sort;
    }

}