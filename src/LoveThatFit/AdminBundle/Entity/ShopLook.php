<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopLook
 *
 * @ORM\Table(name="shop_look")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ShopLookRepository")
 */
class ShopLook
{
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
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="shop_model_image", type="string", length=255)
     */
    private $shop_model_image;

    /**
     * @ORM\OneToMany(targetEntity="ShopLookProduct", mappedBy="shoplook", orphanRemoval=true)
     */

    protected $shop_look_product;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sorting;

    /**
     * @var string $disabled
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=true)
     */
    private $disabled;


    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    public $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->save_look_item = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function upload() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $this->shop_model_image = 'shop_model_image_'.substr(uniqid(),0,10) .'.'. $ext;

        if (!is_dir($this->getUploadRootDir())) {
            try {
                @mkdir($this->getUploadRootDir(), 0700);
            }catch (\Exception $e)
            { $e->getMessage();}
        }

        move_uploaded_file($this->file["tmp_name"], $this->getAbsolutePath());
        #$this->file->move($this->getUploadRootDir(), $this->image);

        $this->file = null;
        return $this->shop_model_image;
    }

    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir() {
        return 'uploads/ltf/shop_Look';
    }

    public function getAbsolutePath() {
        return null === $this->shop_model_image ? null : $this->getUploadRootDir() . '/' . $this->shop_model_image;
    }

    public function deleteImages( $image )
    {

        if ($image) {
            $generated_file_name = $this->getUploadRootDir() . '/' . $image;
            if (is_readable($generated_file_name)) {
                @unlink($generated_file_name);
            }

        }
    }

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
     * @return ShopLook
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
     * Set shop_model_image
     *
     * @param string $shopModelImage
     * @return ShopLook
     */
    public function setShopModelImage($shopModelImage)
    {
        $this->shop_model_image = $shopModelImage;
    
        return $this;
    }

    /**
     * Get shop_model_image
     *
     * @return string 
     */
    public function getShopModelImage()
    {
        return $this->shop_model_image;
    }

    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return ShopLook
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    
        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer 
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return ShopLook
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ShopLook
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
     * @return ShopLook
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
     * Add shop_look_product
     *
     * @param \LoveThatFit\AdminBundle\Entity\ShopLookProduct $shopLookProduct
     * @return ShopLook
     */
    public function addShopLookProduct(\LoveThatFit\AdminBundle\Entity\ShopLookProduct $shopLookProduct)
    {
        $this->shop_look_product[] = $shopLookProduct;
    
        return $this;
    }

    /**
     * Remove shop_look_product
     *
     * @param \LoveThatFit\AdminBundle\Entity\ShopLookProduct $shopLookProduct
     */
    public function removeShopLookProduct(\LoveThatFit\AdminBundle\Entity\ShopLookProduct $shopLookProduct)
    {
        $this->shop_look_product->removeElement($shopLookProduct);
    }

    /**
     * Get shop_look_product
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShopLookProduct()
    {
        return $this->shop_look_product;
    }
}