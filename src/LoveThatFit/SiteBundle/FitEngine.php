<?php

namespace LoveThatFit\SiteBundle;

class FitEngine {

    private $user;
    private $product_item;
    private $product_size;
    private $user_measurement;

    function __construct($user = null, $product_item = null) {

        if ($user) {
            $this->setUser($user);
        }

        if ($product_item) {
            $this->setProductItem($product_item);
        }
    }

    function setProductItem($product_item) {
        $this->product_item = $product_item;
        $this->product_size = $product_item->getProductSize();
    }

    function setUser($user) {
        $this->user = $user;
        $this->user_measurement = $user->getMeasurement();
    }

    function getMeasurementArray() {
        $user_measurement_array = null;
        if ($this->user_measurement) {
            $user_measurement_array = (array) $this->user_measurement;
        }
        return $user_measurement_array;
    }
    
    function getProductItem() {
        return $this->product_item;
    }

    function getUser() {
        return $this->user;
    }

    function fit() {

        $fp_array = null;
        $str = "";
        if ($this->product_item) {
            $product = $this->product_item->getProduct();

            $measurement_array = $this->product_size->getMeasurementArray();
            $fp_array = $product->getFitPriorityArray();
            $body_measurement = $this->user->getMeasurement()->getArray();

            foreach ($fp_array as $key => $value) {
                $str.= $this->compare($body_measurement, $measurement_array, strtolower($key), $value);
                $str.= " <br>";
            }
        }
        return $str;
    }

    //                return array("msg" => 'item max measurement value is not provided.', 'fit' => false, 'max'=> null, 'min'=> null, 'body'=> null);


    private function compare($body_specs, $item_specs, $fit_point, $fit_priority = null) {
        
        if ($fit_point === NULL || $fit_priority === NULL || $fit_priority <= 0) {
            return null;
        }

        $str = "";
        $feeback = array('priority'=>$fit_priority);
                
        if (array_key_exists($fit_point, $item_specs) && array_key_exists($fit_point, $body_specs)) {
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            $feeback['min']=$item_specs[$fit_point]['min'] ;
            $feeback['max']=$item_specs[$fit_point]['max'] ;
            $feeback['body']=$body_specs[$fit_point] ;
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            
            if ($item_specs[$fit_point]['max'] === NULL || $item_specs[$fit_point]['max'] == 0 || $item_specs[$fit_point]['min'] === NULL || $item_specs[$fit_point]['min'] == 0) {                
                if ($item_specs[$fit_point]['max'] === NULL || $item_specs[$fit_point]['max'] == 0) {
                    $str = 'Product maximum ' . $fit_point . ' measurement not available. ';                    
                }
                if ($item_specs[$fit_point]['min'] === NULL || $item_specs[$fit_point]['min'] == 0) {
                    $str .= 'Product minimam ' . $fit_point . ' measurement not available. ';
                }
                                
            } elseif ($body_specs[$fit_point] === NULL || $body_specs[$fit_point] == 0) {
                $str = 'User body ' . $fit_point . ' measurement not provided. ';                
                
            } else {
                if (strlen($str) == 0) {
                    if ($body_specs[$fit_point] <= $item_specs[$fit_point]['max'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['min']) {
                        $str = 'Love that fit ';
                        
                    } elseif ($body_specs[$fit_point] > $item_specs[$fit_point]['max']) {
                        $str = 'loose';
                        
                    } elseif ($body_specs[$fit_point] < $item_specs[$fit_point]['min']) {
                        $str = 'tight';
                        
                    } else {
                        $str = 'No comparision occur';
                        
                    }
                    //$str .= ' ' . $fit_point. '('. $body_specs[$fit_point] .') min('. $item_specs[$fit_point]['min'] .') max('. $item_specs[$fit_point]['max'].')';
                }
            }
        } elseif (!array_key_exists($fit_point, $item_specs)) {
            $str = 'Product ' . $fit_point . ' measurement (min-max) range is not available. ';
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            $feeback['min']=$item_specs[$fit_point]['min'] ;
            $feeback['max']=$item_specs[$fit_point]['max'] ;
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            
            
        } elseif (!array_key_exists($fit_point, $body_specs)) {
            $str = 'user ' . $fit_point . ' measurement not provided';
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            $feeback['body']=$body_specs[$fit_point] ;
            
            #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
            $feeback['msg']=$str;
        }
        if (strlen($str) > 0) {
            $str = $fit_point . " : " . $str;
        }

        return $str;
    }

    private function compare2($body_specs, $item_specs, $fit_point, $fit_priority=null){
        $str = "";
        
        if (array_key_exists($fit_point, $item_specs) && array_key_exists($fit_point, $body_specs)) {
        
            if ($item_specs[$fit_point]['max']===NULL || $item_specs[$fit_point]['max']==0){
                $str .= 'item max measurement value is not provided.';
            }elseif ($item_specs[$fit_point]['min']===NULL || $item_specs[$fit_point]['min']==0){
                $str .= 'item min measurement value is not provided.';
            }elseif ($body_specs[$fit_point]===NULL || $body_specs[$fit_point]==0){
                $str .= 'User body measurement value is not provided.';
            }else{
                if($body_specs[$fit_point] <= $item_specs[$fit_point]['max'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['min']){
                    $str .= 'Love that fit ';
                }elseif($body_specs[$fit_point] > $item_specs[$fit_point]['max']){
                    $str .= 'loose';
                }elseif($body_specs[$fit_point] < $item_specs[$fit_point]['min']){
                    $str .= 'tight';
                }else{
                    $str .= 'No comparision occur';
                }            
                 $str .= ' ' . $fit_point. '('. $body_specs[$fit_point] .') min('. $item_specs[$fit_point]['min'] .') max('. $item_specs[$fit_point]['max'].')';
            }
        } elseif (!array_key_exists($fit_point, $item_specs)){
            if ($fit_priority===Null || $fit_priority==0){
                $str .= '';   
            }else{
                $str .= 'product measurement is not available.';
            }                
        } elseif (!array_key_exists($fit_point, $body_specs)){
                $str .= 'user measurement not available';
        }
            if(strlen($str)>0){
                $str = $fit_point . " : " . $str;
            }
        
        return $str;
    }
    
}
