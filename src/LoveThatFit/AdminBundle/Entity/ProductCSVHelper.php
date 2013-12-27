<?php

namespace LoveThatFit\AdminBundle\Entity;

class ProductCSVHelper {

    private $product;
    private $row;
    private $previous_row;
    private $path;

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
        if ($this->row == 0) {
            $this->product['garment_name'] = $data[1];
            $this->product['retailer_name'] = $data[4]; #~~~~~ Retailer
            $this->product['style'] = $data[7]; #~~~~~ Style
        }


        if ($this->row == 11) {
            $this->product['stretch_type'] = $data[1];
            $this->product['horizontal_stretch'] = $data[3];
            $this->product['vertical_stretch'] = $data[5];
        }

        if ($this->row == 13) {
            $this->product['fabric_weight'] = $data[1];
            $this->product['structural_detail'] = $data[4];
            $this->product['styling_detail'] = $data[7];
        }
        if ($this->row == 15) {
            $this->product['fit_type'] = $data[1];
            $this->product['layring'] = $data[4];
        }
        #~~~~~ Fit Priority
        if ($this->row == 18) {
            $this->product['fit_priority'] = array();
        }
        #~~~~~ Colors
        if ($this->row == 25) {
            $this->product['product_color'] = array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]);
        }

        $this->readSize($data);
        $this->readMeasurement($data);
    }

    private function readSize($data) {
        //$this->product['sizes'] = array($data[23], $data[31], $data[39], $data[47], $data[55], $data[63], $data[71], $data[79], $data[87], $data[95]);
        if ($this->row == 0) {
            $i = 23;
            while (isset($data[$i]) > 0) {
                $s = explode(" ", $data[$i]);
                $this->product['sizes'][$s[1]]['key'] = $i;
                $i = $i + 8;
            }
        }
    }
    private function readMeasurement($data) {
        if ($this->row >= 5 && $this->row <= 22) {
            $sm=array();
            foreach($this->product['sizes'] as $k=>$v){
                $this->product['sizes'][$k][$this->fitPoint($this->row)] = $this->fillFitPointMeasurement($data, intval($v['key']));
            }
            //$this->product['sizes']=$sm;
           }
    }
    private function _readMeasurement($data) {
        if ($this->row >= 5 && $this->row <= 22) {
            $i = 23;
            while ($i <= 95) {
                $m = $data[$i];
                $this->product['sizes'][$m] = $this->fillFitPointMeasurement($data, intval($i));
                $i = $i + 8;
            }
        }
    }
    private function fillFitPointMeasurement($data,$i) {
        return array('garment_measurement_flat' => $data[$i+2],	
        'stretch_type_percentage' => $data[$i+3],
        'garment_measurement_stretch_fit' => $data[$i+4],		
        'maximum_body_measurement' => $data[$i+5],
        'ideal_body_size_high' => $data[$i+6], 
        'ideal_body_size_low' => $data[$i+7],
            );	
    }
    private function fitPoint($i){

        if($i==5) return 'central_front_waist';
        if($i==6) return 'back_waist';
        if($i==7)return 'waist_ to_hip';
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
}

?>
