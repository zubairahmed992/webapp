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
        
        return var_dump($this->user_measurement);
        
        $fp_array = null;
        $str = "";
        if ($this->product_item) {
            $product = $this->product_item->getProduct();
            $measurement_array = $this->product_size->getMeasurementArray();
            $fp_array = $product->getFitPriorityArray();

            foreach ($fp_array as $key => $value) {
                if (array_key_exists(strtolower($key), $measurement_array)) {
                    $str.= $measurement_array[strtolower($key)]['max'] . " > () < " . $measurement_array[strtolower($key)]['min'];
                    
                } else {
                    $str.= " (" . strtolower($key) . ")";
                }
                $str.= " <br>";
            }

            $str = "> fitPriority -> " . $str;
        
        }
 
        return $str;
    }

}
