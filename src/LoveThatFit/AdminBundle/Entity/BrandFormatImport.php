<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\BrandFormatImportRepository")
 * @ORM\Table(name="brand_format_import")
 */
class BrandFormatImport {

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
     * @ORM\Column(name="brand_name", type="string", length=255)
     */
    private $brand_name;

    /**
     * @var string
     *
     * @ORM\Column(name="brand_format", type="string", length=255)
     */
    private $brand_format;


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
     * Set brand_name
     *
     * @param string $brand_name
       @return string
     */
    public function setBrandName($brand_name)
    {
        $this->brand_name = $brand_name;
        return $this;
    }

    /**
     * Get brand_name
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->brand_name;
    }


    /**
     * Set brand_format
     *
     * @param string $brand_format
      @return string
     */
    public function setBrandFormat($brand_format)
    {
        $this->brand_format = $brand_format;

        return $this;
    }

    /**
     * Get brand_format
     *
     * @return string
     */
    public function getBrandFormat()
    {
        return $this->brand_format;
    }
//     public function toArray(){
//        return array(
//            'id' => $this->id,
//            'name' => $this->name,
//            'gender' => $this->gender,
//            'control_number' => $this->control_number,
//            'brand_id' => $this->brand->getId(),
//            'brand_name' => $this->brand->getName(),
//            'retailer_id' => $this->retailer?$this->retailer->getId():$this->retailer,
//            'retailer_name' => $this->retailer?$this->retailer->getTitle():$this->retailer,
//            'styling_type' => $this->styling_type,
//            'neckline' => $this->neckline,
//            'sleeve_styling' => $this->sleeve_styling,
//            'rise' => $this->rise,
//            'hem_length' => $this->hem_length,
//            'stretch_type' => $this->stretch_type,
//            'horizontal_stretch'=> $this->horizontal_stretch,
//            'vertical_stretch'=> $this->vertical_stretch,
//            'fabric_weight' => $this->fabric_weight,
//            'structural_detail' => $this->structural_detail,
//            'fit_type' => $this->fit_type,
//            'layering' => $this->layering,
//            'fit_priority'=> $this->fit_priority,
//            'fabric_content'=> $this->fabric_content,
//            'garment_detail'=> $this->garment_detail,
//            'size_title_type' => $this->size_title_type,
//            'description' => $this->description,
//            'clothing_type_id' => $this->clothing_type->getId(),
//            'clothing_type' => $this->clothing_type->getName(),
//            'target' => $this->clothing_type->getTarget(),
//            'layering' => $this->layering,
//        );
//    }
}