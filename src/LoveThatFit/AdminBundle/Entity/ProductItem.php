<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductItemRepository")
 * @ORM\Table(name="product_item")
 * @ORM\HasLifecycleCallbacks() 
 */
class ProductItem
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */    
    protected $product; 
    
     /**
     * @ORM\ManyToOne(targetEntity="ProductSize", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id", onDelete="CASCADE")
     
      */    
    protected $product_size; 
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id", onDelete="CASCADE")
     
     */    
    protected $product_color; 
 
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemTryHistory", mappedBy="productitem")
     */
    private $user_item_try_history;
    /**
     * @ORM\ManyToMany(targetEntity="LoveThatFit\UserBundle\Entity\User", mappedBy="product_items")
     **/
    private $users;
    
    
    /**
     * Bidirectional (INVERSE SIDE)
     * 
     * @ORM\OneToMany(targetEntity="ProductItemTwoPieces", mappedBy="productitem")
     * */
    private $productitemtwopieces;
    

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_item_try_history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    
    
    /////////////////////////////////////////////////////////////
    
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $line_number
     *
     * @ORM\Column(name="line_number", type="string", nullable=true)
     */
    private $line_number;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
   
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    
     /**
     * @var string $raw_image
     *
     * @ORM\Column(name="raw_image", type="string", nullable=true)
     */
    private $raw_image;
    
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
     * Set line_number
     *
     * @param string $lineNumber
     * @return ProductItem
     */
    public function setLineNumber($lineNumber)
    {
        $this->line_number = $lineNumber;
    
        return $this;
    }

    /**
     * Get line_number
     *
     * @return string 
     */
    public function getLineNumber()
    {
        return $this->line_number;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return ProductItem
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

    /**
     * Set product_size
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSize
     * @return ProductItem
     */
    public function setProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSize = null)
    {
        $this->product_size = $productSize;
    
        return $this;
    }

    /**
     * Get product_size
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductSize 
     */
    public function getProductSize()
    {
        return $this->product_size;
    }

    /**
     * Set product_color
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColor
     * @return ProductItem
     */
    public function setProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColor = null)
    {
        $this->product_color = $productColor;
    
        return $this;
    }

    /**
     * Get product_color
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductColor 
     */
    public function getProductColor()
    {
        return $this->product_color;
    }
    //---------------------------------------------------
    
     public function upload() {
            $ih = new ImageHelper('product_item', $this);
            $ih->upload(); // save & resize images 
    }
    //---------------------------------------------------
    
     public function uploadRawImage() {
         if (null === $this->file) {
            return;
        }
        $old_image_path=Null;
        if ($this->raw_image){
            $old_image_path = $this->getRawImageWebPath();
        }
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->raw_image = uniqid() .".". $ext;        
        $this->file->move(
                $this->getUploadRawImageRootDir(), $this->raw_image
        );
        
        if (is_readable($old_image_path)) {
           @unlink($old_image_path);
       }
        $this->file = null;          
    
    }
    //-------------------------------------------------------
    
     
    public function getImagePaths() {
        $ih = new ImageHelper('product_item', $this);        
        return $ih->getImagePaths();
    }
    
    //-------------------------------------------------------1
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }
//-------------------------------------------------------2
    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }
//-------------------------------------------------------3
    protected function getUploadRootDir() {
        return $this->getRootDir() . $this->getUploadDir();
    }
//-------------------------------------------------------4
    protected function getRootDir() {
        return __DIR__ . '/../../../../web/';
    }

//-------------------------------------------------------5
    protected function getUploadDir() {        
       return 'uploads/ltf/products/fitting_room/web';            
    }

 //-------------------------------------------------------1
    public function getRawImageAbsolutePath() {
        return null === $this->raw_image ? null : $this->getRootDir() . $this->getRawImageUploadDir() . '/' . $this->raw_image;
    }
//-------------------------------------------------------2
    public function getRawImageWebPath() {
        return null === $this->raw_image ? null : $this->getRawImageUploadDir() . '/' . $this->raw_image;
    }
//-------------------------------------------------------3
    protected function getUploadRawImageRootDir() {
        return $this->getRootDir() . $this->getRawImageUploadDir();
    }
    
//-------------------------------------------------------5
    protected function getRawImageUploadDir() {        
       return 'uploads/ltf/products/fitting_room/raw';            
    }

    //-------------------------------------------------------
    
/**
 * @ORM\PostRemove
 */
    
    public function deleteImages()
{
    if($this->image)
    {
        $generated_file_name=$this->getUploadRootDir(). '/' . $this->image;
        if (is_readable($generated_file_name )){
                @unlink($generated_file_name );    
            }
    
  }
}



    /**
     * Add users
     *
     * @param LoveThatFit\UserBundle\Entity\User $users
     * @return ProductItem
     */
    public function addUser(\LoveThatFit\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param LoveThatFit\UserBundle\Entity\User $users
     */
    public function removeUser(\LoveThatFit\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    

    /**
     * Add user_item_try_history
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     * @return ProductItem
     */
    public function addUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history[] = $userItemTryHistory;
    
        return $this;
    }

    /**
     * Remove user_item_try_history
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     */
    public function removeUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history->removeElement($userItemTryHistory);
    }

    /**
     * Get user_item_try_history
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUserItemTryHistory()
    {
        return $this->user_item_try_history;
    }

    /**
     * Set raw_image
     *
     * @param string $rawImage
     * @return ProductItem
     */
    public function setRawImage($rawImage)
    {
        $this->raw_image = $rawImage;    
        return $this;
    }

    /**
     * Get raw_image
     *
     * @return string 
     */
    public function getRawImage()
    {
        return $this->raw_image;
    }

    /**
     * Add productitemtwopieces
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemTwoPieces $productitemtwopieces
     * @return ProductItem
     */
    public function addProductitemtwopiece(\LoveThatFit\AdminBundle\Entity\ProductItemTwoPieces $productitemtwopieces)
    {
        $this->productitemtwopieces[] = $productitemtwopieces;
    
        return $this;
    }

    /**
     * Remove productitemtwopieces
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemTwoPieces $productitemtwopieces
     */
    public function removeProductitemtwopiece(\LoveThatFit\AdminBundle\Entity\ProductItemTwoPieces $productitemtwopieces)
    {
        $this->productitemtwopieces->removeElement($productitemtwopieces);
    }

    /**
     * Get productitemtwopieces
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductitemtwopieces()
    {
        return $this->productitemtwopieces;
    }
}