<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Comparison {

    var $user_measurement;
    var $product;
    var $adjustment;

    function __construct($measurement, $outfit) {
        
        $this->user_measurement = $measurement;
        $this->product = $outfit;
        if($this->product)
            $this->adjustment = $this->product->getAdjustment();
            
        
    }

//$array = array("waist" => array("diff" => -22, "msg" => ""),"hip" => array("diff" => -1.5, "msg" => ""),"bust" => array("diff" => 0, "msg" => ""),"arm" => array("diff" => 1.5, "msg" => ""),"leg" => array("diff" => 22, "msg" => ""),"inseam" => array("diff" => 2, "msg" => ""),"back" => array("diff" => -2, "msg" => ""));
//{ sku: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, waist: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, hip: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, bust: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, arm: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, leg: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, inseam: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, outseam: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, hem: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, back: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' }, length: { lower: 'crossing lower most limit', -2: '2 inch short', -1: '1 inch short', 0: 'Exact fit', 1: 'One inch bigger', 2: '2 inch bigger', adjust: ' adjustment upplied, streched & fit', upper: 'crossing upper most limit' } }
//------------------------------------------------------------------------
    
    function getFeedBackJson() {
        if (!$this->user_measurement)
            return "Measurement not found.";

        if (!$this->product)
            return "Product not found.";

        return json_encode($this->getSuggestionArray());
        
    }
    
    //------------------------------------------------------------------------
    function getFeedBackArray() {
        if (!$this->user_measurement)
            return "Measurement not found.";

        if (!$this->product)
            return "Product not found.";

        return $this->getSuggestionArray();
    }
    
//------------------------------------------------------------------------
    private function getDifference($target) {
        switch ($target) {
            case'Top':
                return array(
                    "waist" => array("diff" => $this->product->getWaist() - $this->user_measurement->getWaist(), "msg" =>"", "data"=> "(".$this->product->getWaist().",".$this->user_measurement->getWaist().")"),
                    "bust" => array("diff" => $this->product->getBust() - $this->user_measurement->getBust(), "msg" => "", "data"=> "(".$this->product->getBust().",".$this->user_measurement->getBust().")"),
                    "arm" => array("diff" => $this->product->getArm() - $this->user_measurement->getArm(), "msg" => "", "data"=> "(".$this->product->getArm().",".$this->user_measurement->getArm().")"),
                    "back" => array("diff" => $this->product->getBack() - $this->user_measurement->getBack(), "msg" => "", "data"=> "(".$this->product->getBack().",".$this->user_measurement->getBack().")"),
                );
                break;
            case 'Bottom':
                return  array(
                    "waist" => array("diff" => $this->product->getWaist() - $this->user_measurement->getWaist(), "msg" => "", "data"=> "(".$this->product->getWaist().",".$this->user_measurement->getWaist().")"),
                    "hip" => array("diff" => $this->product->getHip() - $this->user_measurement->getHip(), "msg" => "", "data"=> "(".$this->product->getHip().",".$this->user_measurement->getHip().")"),
                    "leg" => array("diff" => $this->product->getLeg() - $this->user_measurement->getLeg(), "msg" => "", "data"=> "(".$this->product->getLeg().",".$this->user_measurement->getLeg().")"),
                    "inseam" => array("diff" => $this->product->getInseam() - $this->user_measurement->getInseam(), "msg" => "", "data"=> "(".$this->product->getInseam().",".$this->user_measurement->getInseam().")"),
                );
                break;
            case 'Dress':
                return  array(
                    "waist" => array("diff" => $this->product->getWaist() - $this->user_measurement->getWaist(), "msg" => "", "data"=> "(".$this->product->getWaist().",".$this->user_measurement->getWaist().")"),
                    "hip" => array("diff" => $this->product->getHip() - $this->user_measurement->getHip(), "msg" => "", "data"=> "(".$this->product->getHip().",".$this->user_measurement->getHip().")"),
                    "bust" => array("diff" => $this->product->getBust() - $this->user_measurement->getBust(), "msg" => ""),
                    "arm" => array("diff" => $this->product->getArm() - $this->user_measurement->getArm(), "msg" => "", "data"=> "(".$this->product->getArm().",".$this->user_measurement->getArm().")"),
                    "leg" => array("diff" => $this->product->getLeg() - $this->user_measurement->getLeg(), "msg" => "", "data"=> "(".$this->product->getLeg().",".$this->user_measurement->getLeg().")"),
                    "back" => array("diff" => $this->product->getBack() - $this->user_measurement->getBack(), "msg" => "", "data"=> "(".$this->product->getBack().",".$this->user_measurement->getBack().")"),
                );                
                break;
        }
        return null;
        
    }
//------------------------------------------------------------------------
    private function getSuggestionArray() {

        $msg = self::getMessageArray();
        $sug = $this->getDifference($this->product->getClothingType()->getTarget());

        foreach ($sug as $key => $value) {
            $sug[$key]["msg"] = $sug[$key]["msg"].$this->getSuggestion($msg[$key], $value["diff"]);
        }
        return $sug;
    }
//------------------------------------------------------------------------
    private function getSuggestion($part, $diff) {
        $msg = Null;

        $prev_key = Null;
        $curr_key = Null;

        if ($diff > 2) {
            $msg = $part['upper'];
        } elseif ($diff < -2) {
            $msg = $this->adjustmentUpply($diff)? $part['adjust']:$part['lower'];
            //$msg = $part['lower'];
        } else {
            foreach ($part as $key => $value) {
                $curr_key = doubleval($key);
                if ($curr_key) {
                    //current key is difference 
                    if ($curr_key == $diff) {
                        $msg = $value;
                        break;
                    } elseif ($curr_key > $diff && !$prev_key) {//current key is greater than difference 
                        $msg = $this->adjustmentUpply($diff)? $part['adjust']:$value;
                        break;
                    } elseif ($prev_key) {
                        if ($curr_key < $diff && $prev_key > $diff) {
                            $msg = $this->adjustmentUpply($diff)? $part['adjust']:$value;
                            break;
                        } elseif ($curr_key > $diff && $prev_key < $diff) {
                            $msg = $this->adjustmentUpply($diff)? $part['adjust']:$value;
                            break;
                        }
                    }
                    $prev_key = $curr_key;
                }
            }
        }
        return $msg;
    }
//------------------------------------------------------------------------

    private function adjustmentUpply($diff)
    {
        if ($this->adjustment==0)
            return false;
        
        if ($diff<0 && $diff + $this->adjustment >= 0)
            return true;
        else        
            return false;
    }
//------------------------------------------------------------------------    
    static function getMessageArray() {
        $yaml = new Parser();
        $value = $yaml->parse(file_get_contents('../app/config/fitting_feedback.yml'));
        return $value;
        
    }

}
