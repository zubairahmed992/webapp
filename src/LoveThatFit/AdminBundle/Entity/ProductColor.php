<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\AdminBundle\Entity\ProductColor
 *
 * @ORM\Table(name="product_color")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductColorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductColor {

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_colors")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="ProductItem", mappedBy="product_color", orphanRemoval=true)
     */
    protected $product_items;

    public function __construct() {
        $this->product_items = new ArrayCollection();
    }

    /////////////////////////////////////////////////////////////////////////

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string $pattern
     *
     * @ORM\Column(name="pattern", type="string",nullable=true)
     */
    private $pattern;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string",nullable=true)
     */
    private $image;
    //---------------------- Public Variables -----------------------------------------
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @var string $tempImage
     * @Assert\Blank()
     */
    public $tempImage;

    /**
     * @var string $tempPattern
     * @Assert\Blank()
     */
    public $tempPattern;

    /**
     * @var string $displayProductColor
     */
    public $displayProductColor;

//---------------------------------------------------------------

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

//---------------------------------------------------------------
    /**
     * Set title
     *
     * @param string $title
     * @return ProductColor
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }
//----------------------------------------------------------------------------------
    /**
     * Set pattern
     *
     * @param string $pattern
     * @return ProductColor
     */
    public function setPattern($pattern) {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern() {
        return $this->pattern;
    }

//---------------------------------------------------------------
    /**
     * Set image
     *
     * @param string $image
     * @return ProductColor
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

//---------------------------------------------------------------
    /**
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductColor
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return LoveThatFit\AdminBundle\Entity\Product 
     */
    public function getProduct() {
        return $this->product;
    }

    //---------------------------------------------------------------

    /**
     * Add product_items
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $productItems
     * @return ProductColor
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

    //---------------------------------------------------------------    
    //------------ Facilitating sizes ---------

    private $sizes;

    public function getSizes() {
        return $this->sizes;
    }

    public function setSizes($sizes) {
        $this->sizes = $sizes;
        return $this;
    }

//---------------------------------------------------------------    


    public function getSizeTitleArray() {
        $items = $this->product_items;
        $size_titles = array();
        foreach ($items as $i) {
            //$size_titles[$i->getProductSize()->getTitle()] = $i->getProductSize()->getId();
            $size_titles[$i->getProductSize()->getId()] = $i->getProductSize()->getTitle();
            //changed due to issue in size selection
        }
        return $size_titles;
    }

   

    //---------------------------------------------------------------    
    //-------------- Image Upload ---------------------
    //---------------------------------------

    public function upload() {
        $ih = new ImageHelper('product', $this);
        $ih->upload();
    }

    //------------------------------------------------------------
    public function savePattern() {

        // delete previous images remains to test
        if ($this->tempPattern) {
            $old_file_name = $this->getAbsolutePatternPath();
            $temp_file_name = $this->getAbsolutePatternTempPath();

            $ext = pathinfo($this->getAbsolutePatternTempPath(), PATHINFO_EXTENSION);
            $this->pattern= uniqid() . '.' . $ext;
           
            if (!is_dir($this->getUploadRootDir() . '/pattern')) {
                    mkdir($this->getUploadRootDir() . '/pattern', 0700);
                }
            $dest = $this->getUploadRootDir() . '/pattern/' . $this->pattern;

            rename($temp_file_name, $dest);
            
            if ($this->getId()) {
                if (is_readable($old_file_name)) {
                    @unlink($old_file_name);
                }
            }
        }
    }

    //------------------------------------------------------------
    public function saveImage() {
        if ($this->tempImage) {
            $ih = new ImageHelper('product', $this);
            $ih->uploadProductTempImage(); // save & resize images 
        }
    }

    //------------------------------------------------------------

    public function getImagePaths() {
        $ih = new ImageHelper('product', $this);
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

    
    protected function getUploadFixturesRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getFixturesUploadDir();
    }
    
    protected function getFixturesUploadDir()
    {
        return 'uploads/ltf/fixtures/products';
    }
    
    
    public function emptyDir($path) {
     $debugStr = '';
     if($handle = opendir($path)) {
       while(false !== ($file = readdir($handle))) {
               if($file != "." && $file != "..") {             
               if(is_file($path."/".$file)) { 
                    if(@unlink($path."/".$file)) {
                    $debugStr .=$file;     
                    }
               } else { 
                    if($handle2 = opendir($path."/".$file)) { 
                         while(false !== ($file2 = readdir($handle2))) { 
                              if($file2 != "." && $file2 != "..") {
                                   if(@unlink($path."/".$file."/".$file2)) {
                                   $debugStr .=$file/$file2;     
                                   }
                              } 
                         } 
                    } 
                    if(@rmdir($path."/".$file)) {
                    $debugStr .=$file;     
                    } 
               } 
               } 
          } 
          closedir($handle);
     }
     return $debugStr;
}	

public function deleteAllProductImageFiles()
{
    $path=$this->getUploadRootDir();
    return $this->emptyDir($path);
}

public function smartCopy($source,$dest,$options=array('folderPermission'=>777,'filePermission'=>777)) 
    { 
        $result=false;         
        if (is_file($source)) { 
            if ($dest[strlen($dest)-1]=='/') { 
                if (!file_exists($dest)) { 
                    cmfcDirectory::makeAll($dest,$options['folderPermission'],true); 
                } 
                $__dest=$dest."/".basename($source); 
            } else { 
                $__dest=$dest; 
            } 
            $result=@copy($source, $__dest); 
            chmod($__dest,$options['filePermission']); 
            
        } elseif(is_dir($source)) { 
            if ($dest[strlen($dest)-1]=='/') { 
                if ($source[strlen($source)-1]=='/') { 
                    //Copy only contents 
                } else { 
                    //Change parent itself and its contents 
                    $dest=$dest.basename($source); 
                    @mkdir($dest); 
                    chmod($dest,$options['filePermission']); 
                } 
            } else { 
                if ($source[strlen($source)-1]=='/') { 
                    //Copy parent directory with new name and all its content 
                    @mkdir($dest,$options['folderPermission']); 
                    chmod($dest,$options['filePermission']); 
                } else { 
                    //Copy parent directory with new name and all its content 
                    @mkdir($dest,$options['folderPermission']); 
                    chmod($dest,$options['filePermission']); 
                } 
            }
            $dirHandle=opendir($source); 
            while($file=readdir($dirHandle)) 
            { 
                if($file!="." && $file!="..") 
                { 
                     if(!is_dir($source."/".$file)) { 
                        $__dest=$dest."/".$file; 
                    } else { 
                        $__dest=$dest."/".$file; 
                    }
                    $result=  $this->smartCopy($source."/".$file, $__dest, $options); 
                } 
            } 
            closedir($dirHandle);             
        } else { 
            $result=false; 
        } 
        return $result; 
    } 

public function copyAllProductImageFiles()
{
    $src=  $this->getUploadFixturesRootDir();
    $dst=  $this->getUploadRootDir();
    return $this->smartCopy($src,$dst,$options=array('folderPermission'=>777,'filePermission'=>777));    
}
    
    
    
//-------------------------------------------------------
//-------------------------------------------------------

    public function getAbsoluteTempPath() {
        return null === $this->tempImage ? null : $this->getUploadRootDir() . '/temp/' . $this->tempImage;
    }

    //-------------------------------------------------------
    public function getAbsolutePatternTempPath() {
        return null === $this->tempPattern ? null : $this->getUploadRootDir() . '/temp/' . $this->tempPattern;
    }

//-------------------------------------------------------
    public function getAbsolutePatternPath() {
        return null === $this->pattern ? null : $this->getUploadRootDir() . '/pattern/' . $this->pattern;
    }

//-------------------------------------------------------
    public function getPatternWebPath() {
        return null === $this->pattern ? null : $this->getUploadDir() . '/pattern/' . $this->pattern;
    }

//------------------------------------------------

    public function uploadTemporaryImage() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $temp_image = $this->product->getId() . '_' . uniqid() . '.' . $ext;

        $this->file->move(
                $this->getUploadRootDir() . '/temp/', $temp_image
        );

        $this->file = null;
        return array('image_url'=>$this->getUploadDir() . '/temp/' . $temp_image,'image_name'=>$temp_image);
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteImages() {
        if ($this->image) {
            $ih = new ImageHelper('product', $this);
            $ih->deleteImages($this->image);
        }
    }

}