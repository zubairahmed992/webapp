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
            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
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
            case 1:
                $this->product['gender'] = strtolower($data[1])=='male'?'m':'f';
                break;
            case 4:
                $this->readClothingType($data);
                $this->product['styling_type'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 5:
                $this->product['neck_line'] = strtolower($data[$this->clothing_type_index]);
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
                $this->readFitPriority($data);
                break;
            case 21:
                $this->product['size_title_type'] = $this->changeSizeTitleType($data[0]);         
                break;
            case 23:
                $this->product['body_type'] = $data[0];                
                break;
            case 25:
                $this->readColors($data); //['product_color'] = array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]);
                break;
            case 28:             
                $this->readFabricContent($data);
                break;
        }

        $this->readMeasurement($data);
    
        
    }
#---------------------------------------------------------------
    public function map() {

        $this->row = 0;
        $this->previous_row = '';

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                //$this->readProduct($data);
                $str = $this->row . '  ';
                for ($i=0;$i<=110;$i++){
                    $str.=$data[$i].', ';
                }
                $this->product[$this->row] = $str;
                $this->row++;
            }
            fclose($handle);
            
            return $this->product;
        }
        return;
    }

#---------------------------------------------------------------
    private function changeSizeTitleType($str) {
        if(strtolower($str)=='numeric') return 'numbers';
        else if(strtolower($str)=='letter') return 'letters';        
        else return $str;         
    }

#---------------------------------------------------------------
    private function readFitPriority($data) {
          $previous_row = $this->previous_row;
                $this->product['fit_priority'] = array(
                   $this->makeSnake($previous_row[1]) => $this->removePercent($data[1]), 
                    $this->makeSnake($previous_row[2]) => $this->removePercent($data[2]), 
                    $this->makeSnake($previous_row[3]) => $this->removePercent($data[3]), 
                    $this->makeSnake($previous_row[4]) => $this->removePercent($data[4]), 
                    $this->makeSnake($previous_row[5]) => $this->removePercent($data[5]), 
                    $this->makeSnake($previous_row[6]) => $this->removePercent($data[6]));
    }
    
#---------------------------------------------------------------

    private function readFabricContent($data) {
             $previous_row = $this->previous_row;
                $this->product['fabric_content'] = array(
                   $this->makeSnake($data[1]) => $this->removePercent($data[0]), 
                    $this->makeSnake($data[3]) => $this->removePercent($data[2]), 
                    $this->makeSnake($data[5]) => $this->removePercent($data[4]), 
                    $this->makeSnake($data[7]) => $this->removePercent($data[6]), 
                    $this->makeSnake($data[9]) => $this->removePercent($data[8]), 
                    $this->makeSnake($data[11]) => $this->removePercent($data[10]), 
                    $this->makeSnake($previous_row[1]) => $this->removePercent($previous_row[0]), 
                    $this->makeSnake($previous_row[3]) => $this->removePercent($previous_row[2]), 
                    $this->makeSnake($previous_row[5]) => $this->removePercent($previous_row[4]), 
                    $this->makeSnake($previous_row[7]) => $this->removePercent($previous_row[6]), 
                    $this->makeSnake($previous_row[9]) => $this->removePercent($previous_row[8]), 
                    $this->makeSnake($previous_row[11]) => $this->removePercent($previous_row[10]),
                    );
    }
#---------------------------------------------------------------

    private function readClothingType($data) {
        $i = 1;
        while ($i <= 11) {
            if (strlen($data[$i]) > 0 && $data[$i] != 'N/A') {
                $this->product['clothing_type'] = $this->getMatchingClothingType($this->previous_row[$i]);
                $this->clothing_type_index = $i;
            }
            $i = $i + 1;
        }
    }
   
    #---------------------------------------------------------------
    private function getMatchingClothingType($ct){
        if($ct=='Tee *knit') return 'tee knit';
        if($ct=='Tank *knit') return 'tank knit';
        if($ct=='Pant/ Trouser') return 'trouser';
        if($ct=='Pant/ Jean') return "jean";
        if($ct=='Tee/Polo/Tank *knit') return 'tee knit';
        return $ct;
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
                if ($data[intval($v['key'])+1]>0){
                $i = $this->fitPoint($this->row);
                $this->product['sizes'][$k][$i] = $this->fillFitPointMeasurement($data, intval($v['key']));
                }
            }
        }
    }

    #---------------------------------------------------------------

    private function fillFitPointMeasurement($data, $i) {
        return array('garment_measurement_flat' => $this->removePercent($data[$i + 1]),
            'stretch_type_percentage' => $this->removePercent($data[$i + 2]),
            'garment_measurement_stretch_fit' => $this->removePercent($data[$i + 3]),
            'maximum_body_measurement' => $data[$i + 4],
            'ideal_body_size_high' => $data[$i + 5],
            'ideal_body_size_low' => $data[$i + 6],
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
        $product->setGender($data['gender']);
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
        $product->setFabricContent(json_encode($data['fabric_content']));
        $product->setDisabled(false);        
        $product->setSizeTitleType($data['size_title_type']);
        
        #---------
        return $product;
    }
    public function fillProductColor($data){
        $pc=new ProductColor;
        $pc->setTitle($data);
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

    
//-------------------------------------------------------
    private function initialCap($str){        
        return str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($str))));
    }
    
//-------------------------------------------------------
    private function makeSnake($str){                
        return str_replace(' ', '_', strtolower($str));
    }
    
    //-------------------------------------------------------
      private function removePercent($str){
        return str_replace('%', '', $str);
    }
}

?>
