<?php
// src/Acme/StoreBundle/Entity/Product.php
namespace Acme\StoreBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="product_data")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     */
    protected $imageurl;

      /**
     * @Assert\File(maxSize="6000000")
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
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * Set category
     *
     * @param Acme\StoreBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\Acme\StoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return Acme\StoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set imageurl
     *
     * @param string $imageurl
     * @return Product
     */
    public function setImageurl($imageurl)
    {
        $this->imageurl = $imageurl;
    
        return $this;
    }

    /**
     * Get imageurl
     *
     * @return string 
     */
    public function getImageurl()
    {
        return $this->imageurl;
    }
    
     //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        $this->file->move(
                $this->getUploadRootDir(), $this->file->getClientOriginalName()
        );

        $this->imageurl = $this->file->getClientOriginalName();
        $this->file = null;
    }

    
  public function getAbsolutePath()
    {
        return null === $this->imageurl
            ? null
            : $this->getUploadRootDir().'/'.$this->imageurl;
    }

    public function getWebPath()
    {
        return null === $this->imageurl
            ? null
            : $this->getUploadDir().'/'.$this->imageurl;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/acme/products';
    }
}