<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Component\HttpFoundation\JsonResponse;


class ServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getResultFormat($result){
         if($result != null){
                $data_array = array('clothing_type','brand','style_name','style_id_number'); 
                $size_array = array('garment_dimension','grade_rule');
                $result_data = json_decode($result[0]['specs_json']);                
                foreach ($result_data as $key => $value) {
                    if (in_array($key, $data_array)) {
                    $data[$key] = $value;  
                  }
                }
                foreach ($result_data->sizes as $label_key => $size_labels) {                   
                    foreach ($size_labels as $attr_key => $size_attr) {                        
                        foreach ($size_attr as $key => $value) {
                            if (in_array($key, $size_array)) {
                                $data[$label_key][$attr_key][$key] = $value;  
                            }
                        }
                    }
                }
            } else {
                $data = 'Record not found!';
            }
            return $data;        
    } 
    public function getProductDetails($id){
         $product = $this->container->get('admin.helper.product')->find($id);         
         $result = $this->container->get('service.repo')->getProductDetail($id);         
        return $result;
    }
      #------------------------------------------------------------------------------

    public function createProduct($data) {
        $data = json_decode($data, true);
        //return new JsonResponse($data);
        $clothing_type = $this->container->get("admin.helper.clothingtype")->find($data[0]['clothing_type_id']);
        $brand = $this->container->get('admin.helper.brand')->find($data[0]['brand_id'] );
     
        $product = new Product;
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);
        $product->setName($data[0][0]['name']);
        $product->setControlNumber($data[0][0]['control_number']);
        $product->setDescription($data[0][0]['description']);
        $product->setStretchType($data[0][0]['stretch_type']);
        $product->setHorizontalStretch($data[0][0]['horizontal_stretch']);
        $product->setVerticalStretch($data[0][0]['vertical_stretch']);
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender($data[0][0]['gender']);
        $product->setStylingType($data[0][0]['styling_type']);
        $product->setNeckline($data[0][0]['neckline']);
        $product->setSleeveStyling($data[0][0]['sleeve_styling']);
        $product->setRise($data[0][0]['rise']);
        $product->setHemLength($data[0][0]['hem_length']);
        $product->setFabricWeight($data[0][0]['fabric_weight']);
        $product->setStructuralDetail($data[0][0]['structural_detail']);
        $product->setFitType($data[0][0]['fit_type']);
        $product->setLayering($data[0][0]['layering']);
        $product->setFitPriority($data[0][0]['fit_priority']);
        $product->setFabricContent($data[0][0]['fabric_content']);
        $product->setDisabled(false);
        $product->setSizeTitleType($data[0][0]['size_title_type']);
        #------------------------
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        
        //------------------- Add Product Colors 
        $product_colors_id = '';
        foreach ($data[0][0]['product_colors'] as $colors => $value) {
            $pc = new ProductColor();
            $pc->setTitle(trim($value['title']));  
            $pc->setPattern(trim($value['pattern']));
            $pc->setImage(trim($value['image']));
            $pc->setProduct($product);
            $em->persist($pc);
            $em->flush();
            $product_colors_id[] = $pc->getId();
        }
       
        
        //--------------- Add Product Size
        foreach ($data[0][0]['product_sizes'] as $key => $product_size_value) {            
                $ps = new ProductSize();
                $ps->setTitle($product_size_value['title']);
                $ps->setProduct($product);
                $ps->setBodyType($product_size_value['body_type']);
                $ps->setIndexValue($product_size_value['index_value']);
                $em->persist($ps);
                $em->flush();  
        //----------------- Add Product Items
        foreach ($product_size_value['product_items'] as $key => $value) {
            $pi = new ProductItem();

            $product_color = $this->container->get('admin.helper.productitem')->find($product_colors_id[$key]);
         
            $pi->setPrice(trim($value['price']));  
            $pi->setLineNumber(trim($value['line_number']));
            $pi->setImage(trim($value['image']));
            $pi->setProduct($product);
            $pi->setProductSize($ps);
            $pi->setProductColor($product_color);
            $pi->setRawImage($value['raw_image']);
            $pi->setSku($value['sku']);
            //$pi->addProductItemPiece($value['line_number']); 
            $em->persist($pc);
            $em->flush();
        }
        
        //------------ Add Product Size Measurements
        foreach ($product_size_value['product_size_measurements'] as $product_size_measurements => $value) {            
            $psm = new ProductSizeMeasurement;
            $psm->setTitle($value['title']);
            $psm->setProductSize($ps);
            $psm->setGarmentMeasurementFlat($value['garment_measurement_flat']);
            $psm->setGarmentMeasurementStretchFit($value['garment_measurement_stretch_fit']);
            $psm->setMaxBodyMeasurement($value['max_body_measurement']);
            $psm->setIdealBodySizeHigh($value['ideal_body_size_high']);
            $psm->setIdealBodySizeLow($value['ideal_body_size_low']);
            $psm->setMinBodyMeasurement($value['min_body_measurement']);
            $psm->setFitModelMeasurement($value['fit_model_measurement']);
            $psm->setMaxCalculated($value['max_calculated']);
            $psm->setMinCalculated($value['min_calculated']);
            $psm->setGradeRule($value['grade_rule']);
            $psm->setStretchTypePercentage($value['stretch_type_percentage']);
            $psm->setHorizontalStretch($value['horizontal_stretch']);
            $psm->setVerticalStretch($value['vertical_stretch']);  
            $em->persist($psm);
            $em->flush();
        }
        }
       
        return "Product Insert Sucessfully. ";
    }
    
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }
}