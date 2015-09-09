<?php

namespace LoveThatFit\AdminBundle\Entity;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;


class ProductCSVDataUploader {

    private $product;
    private $row;
    private $previous_row;
    private $path;
    private $clothing_type_index;
    private $db_product;
    
    
//--------------------------------------------------------------------
    public function __construct($path) {
        $this->path = $path;
    }

    //------------------------------------------------------

    public function read() {
        $this->row = 0;
        $this->previous_row = '';
        ini_set('auto_detect_line_endings',TRUE);
        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $this->readProduct($data);
                $this->previous_row = $data;
                $this->row++;
            }
            ini_set('auto_detect_line_endings',FALSE);
            fclose($handle);
            
            return $this->product;
        }
        return;
    }

//------------------------------------------------------

    private function readProduct($data) {
        switch ($this->row) {
            case 0:
                $this->product['garment_name'] = strtolower($data[1]);
                $this->product['retailer_name'] = strtolower($data[4]); #~~~~~ Retailer
                $this->product['brand_name'] = strtolower($data[10]); #~~~~~ Brand
                $this->product['style'] = strtolower($data[7]); #~~~~~ Style
                $this->readSize($data);
                break;
            case 1:
                if(strtolower($data[1])=='male'){
                    $this->product['gender'] = 'm';
                }elseif(strtolower($data[1])=='female'){
                    $this->product['gender'] = 'f';
                }else{
                    $this->product['gender'] =null;
                }                
                    
                break;
            case 3:
                $this->readClothingType($data);
                $this->product['styling_type'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 4:
                $this->product['neck_line'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 5:
                $this->product['sleeve_styling'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 6:
                $this->product['rise'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 7:
                $this->product['hem_length'] = strtolower($data[$this->clothing_type_index]);
                break;
            case 9:
                $this->product['stretch_type'] = $data[1];
                $this->product['horizontal_stretch'] = $data[3];
                $this->product['vertical_stretch'] = $data[5];
                break;
            case 11:
                $this->product['fabric_weight'] = $data[1];
                $this->product['structural_detail'] = $data[4];
                $this->product['styling_detail'] = $data[7];

                break;
            case 13:
                $this->product['fit_type'] = $data[1];
                $this->product['layring'] = $data[4];

                break;
            case 16:
                $this->readFitPriority($data);
                break;
            case 19:
                $this->product['size_title_type'] = $this->changeSizeTitleType($data[0]);         
                break;
            case 21:
                $this->product['body_type'] = $data[0];                
                break;
            case 26:
                if ($this->product['gender'] == 'f' ){
                    $this->readColors($data); //['product_color'] = array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]);
                }
                break;
            case 29:             
                if ($this->product['gender'] == 'f' ){
                    $this->readFabricContent($data);
                }
                break;
            case 39:
                if ($this->product['gender'] == 'm' ){
                    $this->readColors($data); //['product_color'] = array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]);
                }
                break;
            case 41:
                if ($this->product['gender'] == 'm' ){
                   $this->readFabricContent($data);
                }
                break;
        }

        $this->readMeasurement($data);
    
        
    }
#---------------------------------------------------------------
    public function map($row_length=1000, $col=20) {

        $this->row = 0;
        $this->previous_row = '';

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, $row_length, ",")) !== FALSE) {
                //$this->readProduct($data);
                $str = $this->row . '  ';
                for ($i=0;$i<=$col;$i++){
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
        if(strtolower($str)=='numeric') return 'number';
        else return strtolower($str);         
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
        while ($i <= 13) {
            if (strlen($data[$i]) > 0 && $data[$i] != 'N/A') {
                $this->product['clothing_type'] = $this->getMatchingClothingType($this->previous_row[$i]);
                $this->clothing_type_index = $i;
            }
            $i = $i + 1;
        }
    }
   
    #---------------------------------------------------------------
    private function getMatchingClothingType($ct){
        if($ct=='Tee *knit') return 'tee_knit';
        if($ct=='Tank *knit') return 'tank_knit';
        if($ct=='Pant/ Trouser') return 'trouser';
        if($ct=='Pant/ Jean') return "jean";
        if($ct=='Tee/Polo/Tank *knit') return 'tee_knit';
        return $ct;
    }

#---------------------------------------------------------------

    private function readColors($data) {
        $i = 1;
        $this->product['product_color'] = array();
        while (strlen($data[$i]) > 0 && $i <= 11) {
            array_push($this->product['product_color'], strtolower($data[$i]));
            $i = $i + 1;
        }
    }

    #---------------------------------------------------------------

    private function readSize($data) {
        $i = 27;
        while (isset($data[$i]) > 0) {
            $s = explode(" ", $data[$i]);
            if(array_key_exists(1,$s)) $this->product['sizes'][$s[1]]['key'] = $i;
            $i = $i + 13;
        }
    }

    #---------------------------------------------------------------

    private function readMeasurement($data) {
        if ($this->row >= 3 && $this->row <= 20) {
            $sm = array();
            foreach ($this->product['sizes'] as $k => $v) {
                # if flat measurement or (high & low measurements available then pic the data )
                if ($data[intval($v['key'])+1]>0 || ($data[intval($v['key'])+6]>0 && $data[intval($v['key'])+7]>0 && $data[intval($v['key'])+8]>0)){
                    $i = $this->fitPoint($this->row);
                    $this->product['sizes'][$k][$i] = $this->fillFitPointMeasurement($data, intval($v['key']));
                }
            }
        }
    }

    #---------------------------------------------------------------

    private function fillFitPointMeasurement($data, $i) {
        return array(
            'garment_measurement_flat' => $this->removePercent($data[$i + 1]),
            'garment_measurement_stretch_fit' => $this->removePercent($data[$i + 2]),
            'grade_rule' => $data[$i + 3],
            'min_calculated' => $data[$i + 4],
            'min_body_measurement' => $data[$i + 5],
            'ideal_body_size_low' => $data[$i + 6],
            'fit_model' => $data[$i + 7],
            'ideal_body_size_high' => $data[$i + 8],
            'maximum_body_measurement' => $data[$i + 9],            
            'max_calculated' => $data[$i + 10],
            );       
    }
  
    #---------------------------------------------------------------
    private function fitPoint($i){
        if ($this->product['gender']=='f'){
        if($i==3) return 'central_front_waist';
        if($i==4) return 'back_waist';
        if($i==5)return 'waist_to_hip';
        
        if($i==6){
            if ($this->product['clothing_type'] == 'trouser' || $this->product['clothing_type'] == "jean"){
               return 'inseam';
            }else{
               return 'hem_length'; 
            }
        }
        
        if($i==7)return 'arm_length';
        if($i==8)return 'bust';
        if($i==9)return 'waist';
        if($i==10)return 'hip';
        if($i==11)return 'thigh';
        if($i==12)return 'shoulder_across_front';
        if($i==13)return 'shoulder_across_back';
        if($i==14)return 'shoulder_height';
        if($i==15)return 'tricep';
        if($i==16)return 'bicep';
        if($i==17)return 'wrist';
        if($i==18)return 'knee';
        if($i==19)return 'calf';
        if($i==20)return 'ankle';
        }else{
        if($i==5) return 'central_front_waist';
        if($i==6) return 'back_waist';
        if($i==7)return 'waist_to_hip';
        if($i==8)return 'inseam';
        if($i==9)return 'rise';
        if($i==10)return 'arm_length';
        if($i==11)return 'neck';
        if($i==12)return 'chest';
        if($i==13)return 'waist';
        if($i==14)return 'hip';
        if($i==15)return 'thigh';
        if($i==16)return 'shoulder_across_front';
        if($i==17)return 'shoulder_across_back';
        if($i==18)return 'shoulder_height';
        if($i==19)return 'tricep';
        if($i==20)return 'bicep';
        if($i==21)return 'wrist';
        if($i==22)return 'knee';
        if($i==23)return 'calf';
        if($i==24)return 'ankle';
        }
    }
    
    #===================================================================
    
    public function fillProduct($data, $product=null){
        #$retailer=$this->get('admin.helper.retailer')->findOneByName($this->product['retailer_name']);        
        #$clothingType=$this->get('admin.helper.clothingtype')->findOneByName(strtolower($this->product['clothing_type']));
        #$brand=$this->get('admin.helper.brand')->findOneByName($this->product['retailer_name']);
        #$data=$this->product;
        $product==null?new Product:$product;
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
        $product->setControlNumber($data['style']);
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
    #--------------------------------------------
    
      public function DBProductToArray($product) {        
          if (!$product) return null;
        $p = $product->toArray();
        unset($p['id']);
        unset($p['brand_id']);
        unset($p['retailer_id']);
        unset($p['clothing_type_id']);
        unset($p['garment_detail']);
        unset($p['description']);
        unset($p['target']);
        
        $p['garment_name'] = $p['name']; unset($p['name']);
        $p['style'] = $p['control_number']; unset($p['control_number']);
        $p['fit_priority'] = json_decode($p['fit_priority']); 
        $p['fabric_content'] = json_decode($p['fabric_content']); 
        $p['neck_line'] = $p['neckline']; unset($p['neckline']);
        $p['layring'] = $p['layering']; unset($p['layering']);
        
        foreach ($product->getProductSizes() as $ps) {
            foreach ($ps->getProductSizeMeasurements() as $psm) {
                $p['sizes'][$ps->getTitle()][$psm->getTitle()] = $psm->toArray();
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['title']);
                    $p['sizes'][$ps->getTitle()][$psm->getTitle()]['maximum_body_measurement'] = $p['sizes'][$ps->getTitle()][$psm->getTitle()]['max_body_measurement'];
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['max_body_measurement']);
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['horizontal_stretch']);
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['vertical_stretch']);
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['stretch_type_percentage']);
                    $p['sizes'][$ps->getTitle()][$psm->getTitle()]['fit_model'] = $p['sizes'][$ps->getTitle()][$psm->getTitle()]['fit_model_measurement'];
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['fit_model_measurement']);
                    
                    
                    unset($p['sizes'][$ps->getTitle()][$psm->getTitle()]['vertical_stretch']);
            }
        }
        
       $p['product_color']= array();
       foreach ($product->getProductColors() as $pc) {
            array_push($p['product_color'],$pc->getTitle());
        }
        $this->db_product=$p;
        return $p;
    }  
    
    public function csv_added_colors($db,$csv){
        return array_diff($csv, $db);
    }

    public function compare_color_array($db,$csv){         
         $added = array_diff($csv, $db);
         $deleted = array_diff($db,$csv);
         $combined=array_unique(array_merge($csv,$db));
         $final=array();
         foreach($combined as $c=>$v){
             if(in_array($v, $added)){
                 $final[$v]=1;
             }elseif(in_array($v, $deleted)){
                 $final[$v]=-1;
             }else{
                 $final[$v]=0;
             }             
         }
         return $final;
    }
    #-----------------------------------------------
    public function getTitle($str){
        switch ($str){
          case 'garment_measurement_flat':
              return 'Garment Flat';
              break;
          case 'ideal_body_size_high':
              return 'Ideal High';
              break;
          case 'ideal_body_size_low':
              return 'Ideal Low';
              break;
          case 'garment_measurement_stretch_fit':
              return 'Stretched';
              break;
          case 'min_body_measurement':
              return 'Min';
              break;
          case 'grade_rule':
              return 'Grade Rule';
              break;
          case 'min_calculated':
              return 'Min Calc';
              break;
          case 'max_calculated':
              return 'Max Calc';
              break;
          case 'maximum_body_measurement':
              return 'Max';
              break;
          case 'fit_model':
              return 'Fit Model';
              break;
          default:
              return $str;
              break;
        };
    }
    #-----------------------------------------------
    
    public function compare_product($db, $csv) {
        $db['gender'] == $csv['gender'] ? $db['gender_csv'] =$db['gender']:'';
        $db['brand_name'] == $csv['brand_name'] ? $db['brand_name_csv'] =$csv['brand_name']  : '';
        $db['retailer_name'] == $csv['retailer_name'] ? $db['retailer_name_csv']=$csv['retailer_name'] : '';
        $db['styling_type'] == $csv['styling_type'] ? $db['styling_type_csv'] = $csv['styling_type'] : '';
        $db['neckline'] == $csv['neckline'] ? $db['neckline_csv'] = $csv['neckline']  : '';
        $db['sleeve_styling'] == $csv['sleeve_styling'] ? $db['sleeve_styling_csv'] = $csv['sleeve_styling'] : '';
        $db['rise'] == $csv['rise'] ? $db['rise_csv'] = $csv['rise']: '';
        $db['hem_length'] == $csv['hem_length'] ?$db['hem_length_csv'] = $csv['hem_length']:'';
        $db['stretch_type'] == $csv['stretch_type'] ? $db['stretch_type_csv'] =  $csv['stretch_type']:'';
        $db['horizontal_stretch'] == $csv['horizontal_stretch'] ?$db['horizontal_stretch_csv'] =  $csv['horizontal_stretch']:'';
        $db['vertical_stretch'] == $csv['vertical_stretch'] ? $db['vertical_stretch_csv'] = $csv['vertical_stretch']:'';
        $db['fabric_weight'] == $csv['fabric_weight'] ? $db['fabric_weight_csv'] = $csv['fabric_weight']:'';
        $db['structural_detail'] == $csv['structural_detail'] ? $db['structural_detail_csv'] =  $csv['structural_detail']:'';
        $db['fit_type'] == $csv['fit_type'] ? $db['fit_type_csv'] =  $csv['fit_type']:'';
        $db['layering'] == $csv['layering'] ? $db['layering_csv'] =  $csv['layering']:'';
        $db['size_title_type'] == $csv['size_title_type'] ? $db['size_title_type_csv'] =  $csv['size_title_type']:'';
        $db['clothing_type'] == $csv['clothing_type'] ? $db['clothing_type_csv'] =  $csv['clothing_type']:'';
        $db['garment_name'] == $csv['garment_name'] ? $db['garment_name_csv'] =  $csv['garment_name']:'';
        $db['style'] == $csv['style'] ? $db['style_csv'] =  $csv['style']:'';
        $db['fit_priority'] == $csv['fit_priority'] ? $db['fit_priority_csv'] =  $csv['fit_priority']:'';
        $db['fabric_content'] == $csv['fabric_content'] ? $db['fabric_content_csv'] =  $csv['fabric_content']:'';
        return $db;
    }
    
    public function compare_product_detail() {
     
    $a = array(
            "central_front_waist" => array(
                "garment_measurement_flat" => "13.50",
                "garment_measurement_stretch_fit" => "13.50",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "back_waist" => array(
                "garment_measurement_flat" => "14.00",
                "garment_measurement_stretch_fit" => "14.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "waist_to_hip" => array(
                "garment_measurement_flat" => "8.00",
                "garment_measurement_stretch_fit" => "8.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "hem_length" => array(
                "garment_measurement_flat" => "25.73",
                "garment_measurement_stretch_fit" => "25.73",
                "grade_rule" => "0.13",
                "min_calculated" => "30.14",
                "min_body_measurement" => 0,
                "ideal_body_size_low" => "31.12",
                "fit_model" => "31.36",
                "ideal_body_size_high" => "31.61",
                "ideal_body_size_high" => 0,
                "max_calculated" => "32.59"
            ),
            "arm_length" => array(
                "garment_measurement_flat" => "24.00",
                "garment_measurement_stretch_fit" => "24.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "bust" => array(
                "garment_measurement_flat" => "35.00",
                "garment_measurement_stretch_fit" => "38.19",
                "grade_rule" => "1.00",
                "min_calculated" => "30.51",
                "min_body_measurement" => "30.51",
                "ideal_body_size_low" => "32.31",
                "fit_model" => "32.76",
                "ideal_body_size_high" => "33.21",
                "ideal_body_size_high" => "35.00",
                "max_calculated" => "35.00"
            ),
            "waist" => array(
                "garment_measurement_flat" => "28.50",
                "garment_measurement_stretch_fit" => "31.09",
                "grade_rule" => "1.00",
                "min_calculated" => "22.80",
                "min_body_measurement" => "22.80",
                "ideal_body_size_low" => "24.55",
                "fit_model" => "24.99",
                "ideal_body_size_high" => "25.43",
                "ideal_body_size_high" => "25.87",
                "max_calculated" => "27.18"
            ),
            "shoulder_across_front" => array(
                "garment_measurement_flat" => "16.00",
                "garment_measurement_stretch_fit" => "17.46",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "shoulder_across_back" => array(
                "garment_measurement_flat" => "16.50",
                "garment_measurement_stretch_fit" => "18.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "shoulder_height" => array(
                "garment_measurement_flat" => "5.00",
                "garment_measurement_stretch_fit" => "5.46",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            )            
        );
    
      $b = array(
            "waist_to_hip" => array(
                "garment_measurement_flat" => "8.00",
                "garment_measurement_stretch_fit" => "8.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "arm_length" => array(
                "garment_measurement_flat" => "24.00",
                "garment_measurement_stretch_fit" => "24.00",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
           
            "waist" => array(
                "garment_measurement_flat" => "28.50",
                "garment_measurement_stretch_fit" => "31.09",
                "grade_rule" => "1.00",
                "min_calculated" => "22.80",
                "min_body_measurement" => "22.80",
                "ideal_body_size_low" => "24.55",
                "fit_model" => "24.99",
                "ideal_body_size_high" => "25.43",
                "ideal_body_size_high" => "25.87",
                "max_calculated" => "27.18"
            ),
            "hip" => array(
                "garment_measurement_flat" => "36.00",
                "garment_measurement_stretch_fit" => "39.28",
                "grade_rule" => "1.00",
                "min_calculated" => "32.40",
                "min_body_measurement" => "32.40",
                "ideal_body_size_low" => "34.20",
                "fit_model" => "34.65",
                "ideal_body_size_high" => "35.10",
                "ideal_body_size_high" => "36.90",
                "max_calculated" => "36.90"
            ),
          
            "knee" => array(
                "garment_measurement_flat" => "18.00",
                "garment_measurement_stretch_fit" => "19.64",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "calf" => array(
                "garment_measurement_flat" => "18.00",
                "garment_measurement_stretch_fit" => "19.64",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            ),
            "ankle" => array(
                "garment_measurement_flat" => "18.50",
                "garment_measurement_stretch_fit" => "20.18",
                "grade_rule" => 0,
                "min_calculated" => 0,
                "min_body_measurement" => 0,
                "ideal_body_size_low" => 0,
                "fit_model" => 0,
                "ideal_body_size_high" => 0,
                "ideal_body_size_high" => 0,
                "max_calculated" => 0
            )
        );
      return array_diff_key($b,$a);
    }
}

?>
