<?php

namespace LoveThatFit\SiteBundle;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;

class comparison {

    var $user_measurement;
    var $product;
    var $adjustment;

    function __construct($measurement, $outfit) {
        $this->user_measurement = $measurement;
        $this->product = $outfit;
        $this->adjustment = $this->product->getAdjustment();
    }

    function determine() {
        return $this->testing123();

        if (!$this->user_measurement)
            return "Measurement not found.";

        if (!$this->product)
            $target = $this->product->getClothingType()->getTarget();
        switch ($target) {
            case'Top':
                return $this->determine_top();
                break;
            case 'Bottom':
                return $this->determine_bottom();
                break;
            case 'Dress':
                return $this->determine_dress();
                break;
        }


        return "Clothing type not matched";
    }

    function determine_top() {

        return "Top aai";
    }

    function determine_bottom() {

        return "bottom aai";
    }

    function determine_dress() {

        return "dress aai";
    }

    function getDifference() {
        if (!$this->user_measurement)
            return "Measurement not found.";


        if (!$this->product)
            return "Product not found.";

        $array = array(
            "waist" => array("diff" => $this->product->getWaist() - $this->user_measurement->getWaist(), "msg" => ""),
            "hip" => array("diff" => $this->product->getHip() - $this->user_measurement->getHip(), "msg" => ""),
            "bust" => array("diff" => $this->product->getBust() - $this->user_measurement->getBust(), "msg" => ""),
            "arm" => array("diff" => $this->product->getArm() - $this->user_measurement->getArm(), "msg" => ""),
            "leg" => array("diff" => $this->product->getLeg() - $this->user_measurement->getLeg(), "msg" => ""),
            "inseam" => array("diff" => $this->product->getInseam() - $this->user_measurement->getInseam(), "msg" => ""),
            "back" => array("diff" => $this->product->getBack() - $this->user_measurement->getBack(), "msg" => ""),
        );

        return $array;
    }

    function getSuggestionArray() {
        
        if (!$this->user_measurement)
            return "Measurement not found.";

        if (!$this->product)
            return "Product not found.";

        $msg = $this->getMessageArray();
        $sug = $this->getDifference();
        
        foreach ($sug as $key => $value) {
            //sendnig the body part related messages array & the difference (between body & clothing)
            $sug[$key]["msg"]=$this->getSuggestion($msg[$key], $value["diff"]);           
        }

        return $sug;
    }

    function getSuggestion($part, $diff) {
        
        $prev_key = Null;
        $curr_key = Null;
        $msg=Null;
    //keys are the difference range limits 
        
        foreach ($part as $key => $value) {

            $curr_key = doubleval($key);
            //current key is difference 
            if ($curr_key == $diff) {
                $msg= $value;
                break;
            } elseif ($curr_key > $diff && !$prev_key) {//current key is greater than difference 
                $msg= $value;
                break;
            } elseif ($prev_key) {
                if ($curr_key < $diff && $prev_key > $diff) {
                    $msg= $value;
                    break;
                } elseif ($curr_key > $diff && $prev_key < $diff) {
                    $msg= $value;
                    break;
                }                
            }
            $prev_key = $curr_key;
        }
        if ($prev_key == $curr_key)
        {
            $msg= "larger than the upper most limit..";
        }elseif ($curr_key-$diff > -3)
        {
            $msg= "larger than the upper most limit..";
        }
        
            
        return $msg;
    }

    function testing123() {
        return json_encode($this->getSuggestionArray());
        #return json_encode($this->getSuggestion($this->getMessageArray()['bust'],"-2"));
    }


    function getMessageArray() {
        return array(
            "sku" => array(                
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "waist" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "hip" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "bust" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "arm" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "leg" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "inseam" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "outseam" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "hem" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "back" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
            "length" => array(
                "-2" => '2 inch short',
                "-1" => '1 inch short',
                "0" => 'Exact fit',
                "1" => 'One inch bigger',
                "2" => '2 inch bigger'),
        );
    }

}
