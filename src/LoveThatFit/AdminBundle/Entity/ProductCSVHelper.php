<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;


class ProductCSVHelper {

    private $product;
    private $row;
    private $previous_row;
    private $path;
    private $clothing_type_index;

//--------------------------------------------------------------------
    public function __construct($path) {
        $this->path = $path;
    }

    //------------------------------------------------------

    public function read() {

        $this->row = 0;
        $this->previous_row = '';

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->readProduct($data);
                $this->previous_row = $data;
                $this->row++;
            }
            fclose($handle);
            
            return $this->product;
        }
        return;
    }

//------------------------------------------------------

    private function readProduct($data) {
        switch ($this->row) {
            case 0:
                $this->product['garment_name'] = $data[1];
                $this->product['retailer_name'] = $data[4]; #~~~~~ Retailer
                $this->product['style'] = $data[7]; #~~~~~ Style
                $this->readSize($data);
                break;
            case 4:
                $this->product['styling_type'] = $data[1];
                $this->product['neck_line'] = $data[4];
                $this->product['sleeve_styling'] = $data[7];
                $this->product['rise'] = $data[7];
                $this->product['hem_length'] = $data[7];
                $this->readClothingType($data);
                $this->product['styling_type'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 5:
                $this->product['neckline'] = strtolower($data[$this->clothing_type_index]);

                break;
            case 6:
                $this->product['sleeve_styling'] = strtolower($data[$this->clothing_type_index]);

                break;
            case 7:
                $this->product['rise'] = strtolower($data[$this->clothing_type_index]);

                break;
            case 8:
                $this->product['hem_length'] = strtolower($data[$this->clothing_type_index]);

                break;
            case 11:
                $this->product['stretch_type'] = $data[1];
                $this->product['horizontal_stretch'] = $data[3];
                $this->product['vertical_stretch'] = $data[5];
                break;
            case 13:
                $this->product['fabric_weight'] = $data[1];
                $this->product['structural_detail'] = $data[4];
                $this->product['styling_detail'] = $data[7];

                break;
            case 15:
                $this->product['fit_type'] = $data[1];
                $this->product['layring'] = $data[4];

                break;
            case 18:
                $previous_row = $this->previous_row;
                $this->product['fit_priority'] = array($previous_row[1] => $data[1], $previous_row[2] => $data[2], $previous_row[3] => $data[3], $previous_row[4] => $data[4], $previous_row[5] => $data[5], $previous_row[6] => $data[6]);
                break;
            case 25:
                $this->readColors($data); //['product_color'] = array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]);
                break;
            case 28:
                $previous_row = $this->previous_row;
                $this->product['fiber_content'] = array(
                    $data[1] => $data[0], 
                    $data[3] => $data[2], 
                    $data[5] => $data[4], 
                    $data[7] => $data[6], 
                    $data[9] => $data[8], 
                    $data[11] => $data[10], 
                    $previous_row[1] => $previous_row[0], 
                    $previous_row[3] => $previous_row[2], 
                    $previous_row[5] => $previous_row[4], 
                    $previous_row[7] => $previous_row[6], 
                    $previous_row[9] => $previous_row[8], 
                    $previous_row[11] => $previous_row[10],
                    );
                break;
        }

        $this->readMeasurement($data);
    
        
    }

#---------------------------------------------------------------

    private function readClothingType($data) {
        $i = 1;
        while ($i <= 11) {
            if (strlen($data[$i]) > 0 && $data[$i] != 'N/A') {
                $this->product['clothing_type'] = $this->previous_row[$i];
                $this->clothing_type_index = $i;
            }
            $i = $i + 1;
        }
    }

#---------------------------------------------------------------

    private function readColors($data) {
        $i = 1;
        $this->product['product_color'] = array();
        while (strlen($data[$i]) > 0 && $i <= 11) {
            array_push($this->product['product_color'], $data[$i]);
            $i = $i + 1;
        }
    }

    #---------------------------------------------------------------

    private function readSize($data) {
        //$this->product['sizes'] = array($data[23], $data[31], $data[39], $data[47], $data[55], $data[63], $data[71], $data[79], $data[87], $data[95]);
        $i = 23;
        while (isset($data[$i]) > 0) {
            $s = explode(" ", $data[$i]);
            if(array_key_exists(1,$s)) $this->product['sizes'][$s[1]]['key'] = $i;
            $i = $i + 8;
        }
    }

    #---------------------------------------------------------------

    private function readMeasurement($data) {
        if ($this->row >= 5 && $this->row <= 22) {
            $sm = array();
            foreach ($this->product['sizes'] as $k => $v) {
                $i = $this->fitPoint($this->row);
                $this->product['sizes'][$k][$i] = $this->fillFitPointMeasurement($data, intval($v['key']));
            }
        }
    }

    #---------------------------------------------------------------

    private function fillFitPointMeasurement($data, $i) {
        return array('garment_measurement_flat' => $data[$i + 2],
            'stretch_type_percentage' => $data[$i + 3],
            'garment_measurement_stretch_fit' => $data[$i + 4],
            'maximum_body_measurement' => $data[$i + 5],
            'ideal_body_size_high' => $data[$i + 6],
            'ideal_body_size_low' => $data[$i + 7],
        );
    }

    #---------------------------------------------------------------
    private function fitPoint($i){

        if($i==5) return 'central_front_waist';
        if($i==6) return 'back_waist';
        if($i==7)return 'waist_to_hip';
        if($i==8)return 'inseam';
        if($i==9)return 'arm_length';
        if($i==10)return 'bust';
        if($i==11)return 'waist';
        if($i==12)return 'hip';
        if($i==13)return 'thigh';
        if($i==14)return 'shoulder_across_front';
        if($i==15)return 'shoulder_across_back';
        if($i==16)return 'shoulder_height';
        if($i==17)return 'tricep';
        if($i==18)return 'bicep';
        if($i==19)return 'wrist';
        if($i==20)return 'knee';
        if($i==21)return 'calf';
        if($i==22)return 'ankle';

    }
    
    #===================================================================
    
    public function fillProduct($data){
        #$retailer=$this->get('admin.helper.retailer')->findOneByName($this->product['retailer_name']);        
        #$clothingType=$this->get('admin.helper.clothingtype')->findOneByName(strtolower($this->product['clothing_type']));
        #$brand=$this->get('admin.helper.brand')->findOneByName($this->product['retailer_name']);
#$data=$this->product;
        $product=new Product;
        #$product->setBrand($brand);
        #$product->setClothingType($clothingType);
        #$product->setRetailer($retailer);
        $product->setName($data['garment_name']);
        $product->setStretchType($data['stretch_type']);
        $product->setHorizontalStretch($data['horizontal_stretch']);
        $product->setVerticalStretch($data['vertical_stretch']);        
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender('F');
        $product->setStylingType($data['styling_type']);
        $product->setNeckline($data['neck_line']);
        $product->setSleeveStyling($data['sleeve_styling']);
        $product->setRise($data['rise']);
        $product->setHemLength($data['hem_length']);
        $product->setFabricWeight($data['fabric_weight']);
        $product->setStructuralDetail($data['structural_detail']);
        $product->setFitType($data['fit_type']);
        $product->setLayering($data['layring']);
        $product->setFitPriority(json_encode($data['fit_priority']));
        $product->setDisabled(false);
        return $product;
    }
    public function fillProductColor(){
        $pc=new ProductColor;
        return $pc;
    }
    public function fillProductSize(){
        $ps=new ProductSize;
        return $ps;
    }
    public function fillProductSizeMeasurement(){
        $psm=new ProductSizeMeasurement;
        return $psm;
    }
    public function fillProductItem(){
        $pi=new ProductItem;
        return $pi;
    }
}

?>
