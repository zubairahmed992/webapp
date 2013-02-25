<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Yaml\Parser;

/**
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductRepository")
 * @ORM\Table(name="product")
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
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $adjustment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $sku;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $image;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $waist;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $hip;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $bust;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $arm;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $leg;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $inseam;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $outseam;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $hem;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $back;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $length;

    /**
     * @ORM\Column(type="string", length=255)
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
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
//?????????????????????????????????????     Demo !!
//        $this->image = uniqid() .'.'. $ext;
            $this->image = $this->file->getClientOriginalName();
        $this->file->move(
                $this->getUploadRootDir(), $this->image
        );
        //this should be done after in saved callback 
//?????????????????????????????????????     Demo   
//$this->resize_image();
        $this->file = null;
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
        //return $this->getImageConfiguration()['original']['dir'];        
    }
    
    //-------------------------------------------------------
//------------------------------- image Resize ------------------------------------------
    //-------------------------------------------------------
    
    
    //---------------- read yaml for configuration --------------------
    protected function getImageConfiguration(){
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        return $conf['image_category']['product'];
    } 
    //---------------------------------------------------------------------
    protected function getImageExtention(){
        return pathinfo($this->image, PATHINFO_EXTENSION);
    }

//---------------------------------------------------------------------
        protected function getUniqueCode(){
        return str_replace('.'.$this->getImageExtention(), '', $this->image);
    }

//---------------------------------------------------------------------   
    
     
function validateConf ($value)
{
   $value['prefix']=strlen($value['prefix'])==0?'':'_'.$value['prefix'].'_';
   return $value;
}

    
//-------------------- resize & save images getting params from YAML -------------------------------------------------
    private function resize_image() {
        
        $filename = $this->getAbsolutePath();
        
        $image_info = getimagesize($filename);
        $image_type = $image_info[2];

        $conf=$this->getImageConfiguration();//read yml to conf variable
        
         switch ($image_type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filename);
                break;
        }
        
        $ext = pathinfo($this->image, PATHINFO_EXTENSION);
        
        foreach ($conf as $key => $value) {
            if ($key!='original')
             {
            $value=$this->validateConf($value); 
            
            $img_new = imagecreatetruecolor($value['width'], $value['height']);
            imagecopyresampled($img_new, $source, 0, 0, 0, 0, $value['width'], $value['height'], imagesx($source), imagesy($source));
            
            if (!is_dir($value['dir'])) {
    mkdir($value['dir'], 0700);
            }
            
            
            switch ($image_type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($img_new, $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix']  . $key . '.jpg', 75);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($img_new, $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix']  . $key . '.gif');
                    break;
                case IMAGETYPE_PNG:
                    imagepng($img_new, $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix']  . $key . '.png');
                    break;
            }
            
             }
            }
        
}

//-------------------- return array of image paths regenerated getting config from yaml file ----------------------------------------

public function getImagePaths() {
        $conf = $this->getImageConfiguration();
        $n[]=null;
         foreach ($conf as $key => $value) {
             if ($key!='original'){
                 $value=$this->validateConf($value);
                 $n[$key] = $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix'] . $key  . '.' . $this->getImageExtention();
             }
            }
        return $n;        
}

//---------------------------------------------------------------
   public function check() {
        
        $conf = $this->getImageConfiguration();
        
        $n='<ul>';
         foreach ($conf as $key => $value) {
             if ($key!='original')
             {
                 $value=$this->validateConf($value);
                 
             $n=$n.'<li>'. $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix'] . $key  . '.' . $this->getImageExtention();
                          
             }
            }
        return $n;
        
}

}