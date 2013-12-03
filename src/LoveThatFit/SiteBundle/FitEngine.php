<?php

namespace LoveThatFit\SiteBundle;

class FitEngine {

    private $user;
    private $product_item;
    private $product_size;
    private $user_measurement;

    function __construct($user = null, $product_item = null) {
        if ($user)
            $this->setUser($user);
        if ($product_item)
            $this->setProductItem($product_item);
    }
#----------------------------------------------------------------------------------------------------
    function setProductItem($product_item) {
        $this->product_item = $product_item;
        $this->product_size = $product_item->getProductSize();
    }
#----------------------------------------------------------------------------------------------------
    function setUser($user) {
        $this->user = $user;
        $this->user_measurement = $user->getMeasurement();
    }
#----------------------------------------------------------------------------------------------------
    function getProductItem() {
        return $this->product_item;
    }
#----------------------------------------------------------------------------------------------------
    function getUser() {
        return $this->user;
    }

#---------------------------------------------------------------------------------
    
    function getFeedBackJSON() {
        return json_encode($this->getBasicFeedback());
    }

#--------------------------------------------------------------------------------->
#----------------------------   Get Fitting Size  -----------------------------------------------------|
#--------------------------------------------------------------------------------->
    
    function getFittingSize($current_item = null) {
        if ($current_item === NULL) {
            $current_item =  $this->product_item;
        }        
    
        $product=$current_item->getProduct();        
        $sizes=$product->getProductSizes();
        $priority = $product->getFitPriorityArray();        
        $body_specs = $this->user->getMeasurement()->getArray();
        
        foreach ($sizes as $size) {
            $item_specs = $size->getMeasurementArray();
            $dose_fit =  $this->fits($priority, $body_specs, $item_specs);
            if ($dose_fit){
                return $size->getTitle();
            }
        }
        return "No Size fits..";
    }

#--------------------------------------------------------------------------------->
        
    private function fits($priority,$body_specs, $item_specs) {
        $is_ltf = true;        
        foreach ($priority as $key => $value) {
            $fb = $this->compare($body_specs, $item_specs, strtolower($key), $value);
            if ($fb != NULL) {                
                if ($fb['fit'] === false) {
                    $is_ltf = false;
                }
            }
        }
        return $is_ltf;
    }


#--------------------------------------------------------------------------------->
#---------------------------  Feedback Methods ------------------------------------------------------|
#--------------------------------------------------------------------------------->

    
    function getBasicFeedback($current_item=null) {

        $feed_back = array();
        $is_ltf = true;
        
        if ($current_item===NULL){
            $current_item=$this->product_item;
        }
        
        if ($current_item) {
            $product = $current_item->getProduct();
            $measurement_array = $this->product_size->getMeasurementArray();
            $fp_array = $product->getFitPriorityArray();
            $body_measurement = $this->user->getMeasurement()->getArray();

            foreach ($fp_array as $key => $value) {
                $fb = $this->compare($body_measurement, $measurement_array, strtolower($key), $value);
                if ($fb != NULL) {
                    $feed_back [strtolower($key)] = $fb;
                    if ($fb['fit'] === FALSE) {
                        $is_ltf = false;
                    }
                }
            }
        }
        if ($is_ltf === true) {
            $str = 'Love that Fit!';
            $feed_back = null;
            $feed_back['Overall'] = $this->getFeedbackArrayElement(null, null, null, 0, null, true, $str);
        }
        return $feed_back;
    }

    
   
#---------------------------------------------------------------------------------

    private function compare($body_specs, $item_specs, $fit_point, $fit_priority = null) {

        if ($fit_point === NULL || $fit_priority === NULL || $fit_priority <= 0) {
            return null;
        }
        $min = null; $max = null; $body = null; $diff = null;
        $priority = $fit_priority; $fit = false; $str = "";

        if (array_key_exists($fit_point, $item_specs) && array_key_exists($fit_point, $body_specs)) {
            if ($item_specs[$fit_point]['max'] === NULL || $item_specs[$fit_point]['max'] == 0 || $item_specs[$fit_point]['min'] === NULL || $item_specs[$fit_point]['min'] == 0) {
                if ($item_specs[$fit_point]['max'] === NULL || $item_specs[$fit_point]['max'] == 0) {
                    $str = 'Product maximum ' . $fit_point . ' measurement not available. ';
                } else {
                    $max = $item_specs[$fit_point]['max']; #~~~~~~~~~>
                }
                if ($item_specs[$fit_point]['min'] === NULL || $item_specs[$fit_point]['min'] == 0) {
                    $str .= 'Product minimam ' . $fit_point . ' measurement not available. ';
                } else {
                    $min = $item_specs[$fit_point]['min']; #~~~~~~~~~>
                }
            } elseif ($body_specs[$fit_point] === NULL || $body_specs[$fit_point] == 0) {
                $str = 'User body ' . $fit_point . ' measurement not provided. ';
                $max = $item_specs[$fit_point]['max']; #~~~~~~~~~>
                $min = $item_specs[$fit_point]['min']; #~~~~~~~~~>
            } else {
                $max = $item_specs[$fit_point]['max']; #~~~~~~~~~>
                $min = $item_specs[$fit_point]['min']; #~~~~~~~~~>
                $body = $body_specs[$fit_point]; #~~~~~~~~~>

                if ($body_specs[$fit_point] <= $item_specs[$fit_point]['max'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['min']) {
                    $str = 'Perfect fit ';
                    $diff = 0;
                    $fit = true;
                } elseif ($body_specs[$fit_point] > $item_specs[$fit_point]['max']) {
                    $str = 'loose';
                    $diff = $body_specs[$fit_point] - $item_specs[$fit_point]['max']; #~~~~~~~~~>                        
                } elseif ($body_specs[$fit_point] < $item_specs[$fit_point]['min']) {
                    $str = 'tight';
                    $diff = $body_specs[$fit_point] - $item_specs[$fit_point]['min']; #~~~~~~~~~>
                } else {
                    $str = 'No comparision occur';
                }
            }
        } elseif (!array_key_exists($fit_point, $item_specs)) {
            $str = 'Product ' . $fit_point . ' measurement (min-max) range is not available. ';
        } elseif (!array_key_exists($fit_point, $body_specs)) {
            $str = 'user ' . $fit_point . ' measurement not provided';
        }

        return $this->getFeedbackArrayElement($min, $max, $body, $diff, $priority, $fit, $str);
    }
 #----------------------------------------------------------------------------------------------------
    private function getFeedbackArrayElement($min, $max, $body, $diff, $priority, $fit, $msg) {
        return array('min' => $min,
            'max' => $max,
            'body' => $body,
            'diff' => $diff,
            'priority' => $priority,
            'fit' => $fit,
            'msg' => $msg);
    }

}
