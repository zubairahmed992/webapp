<?php

namespace LoveThatFit\SiteBundle;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Algorithm {

    var $user;
    var $product;
    var $user_measurement;
    var $product_measurement;
    var $adjustment;
    var $msg_array;

    function __construct($user, $product_item) {
            $this->setUser($user);            
            $this->setProduct($product_item);   
    }

//------------------------------------------------------------------------

    function setProduct($product_item) {        
        if ($product_item) {
            $this->product = $product_item->getProduct();
            $this->product_measurement = $product_item->getProductSize();
            }        
    }
    //------------------------------------------------------------------------
    function setUser($user) {
        if ($user) {
            $this->user = $user;
            $this->user_measurement = $this->user->getMeasurement();
        }
    }
    
//------------------------------------------------------------------------

    function setProductMeasurement($product_size) {        
        if ($product_size) {
            $this->product_measurement = $product_size;            
           $this->product = $product_size->getProduct();
            }        
    }
    

//------------------------------------------------------------------------

    function getFeedBackJson() {
        return json_encode($this->getFeedBackArray());
    }

    //------------------------------------------------------------------------
    function getFeedBackArray() {
        if (!$this->user_measurement)
            return "Please update your profile in order to get suggetions.";

        if (!$this->product_measurement)
            return "Product not found.";

        return $this->filter();
    }

    // ------------------------------------------------------

    function filter() {
        
        if ($this->user->getGender() == 'm') {
            if ($this->product->getClothingType()->getTarget() == 'Top') {
                //chest neck & sleeve* / back, waist                
                return array(
                    "neck" => $this->compareNeck(),
                    "chest" => $this->compareChest(),
                    
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                //waist & inseam / outseam
                    return array(
                    "waist" => $this->compareWaist(),
                                        
                );
                    
            } else {
                return null;
            }
        } elseif ($this->user_measurement->getGendet() == 'w') {

            if ($this->product->getClothingType()->getTarget() == 'Top') {
                //bust, waist, back & sleeve*                
                 return array(
                    "bust" => $this->compareBust(),
                    "waist" => $this->compareWaist(),
                                        
                );
                
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                //waist, hip, inseam / outseam
                    return array(
                    "waist" => $this->compareWaist(),
                    "hip" => $this->compareHip(),                    
                    
                );
                
            } elseif ($this->product->getClothingType()->getTarget() == 'Dress') {
                //bust, waist, back, hip & sleeve*
                return array(
                    "bust" => $this->compareBust(),
                    "waist" => $this->compareWaist(),
                    "hip" => $this->compareHip(),                    
                );
            } else {
                return null;
            }
        } else {
            return null;
        }
        
    }
    //------------------------------------------------------------------------

    public function fit() {
         
        if (!$this->user_measurement)
            return false;

        if (!$this->product_measurement)
            return false;

        $sug_array=$this->filter();
        
        foreach ($sug_array as $key => $value) {
            if ($value["fit"] == false )
                return false;
        }
        return true;
    }

//------------------- comparison methods
    //neck back chest bust sleeve waist outseam inseam hip length 

    public function compareNeck() {
        return $this->getArrayFill('neck', $this->compare($this->user_measurement->getNeck(), $this->product_measurement->getNeckMin(), $this->product_measurement->getNeckMax()));
    }

    public function compareBack() {
        return $this->getArrayFill('back', $this->compare($this->user_measurement->getBack(), $this->product_measurement->getBackMin(), $this->product_measurement->getBackMax()));
    }

    public function compareChest() {
        return $this->getArrayFill('chest', $this->compare($this->user_measurement->getChest(), $this->product_measurement->getChestMin(), $this->product_measurement->getChestMax()));
    }

    public function compareBust() {
        return $this->getArrayFill('bust', $this->compare($this->user_measurement->getBust(), $this->product_measurement->getBustMin(), $this->product_measurement->getBustMax()));
    }

    public function compareSleeve() {
        return $this->getArrayFill('sleeve', $this->compare($this->user_measurement->getSleeve(), $this->product_measurement->getSleeveMin(), $this->product_measurement->getSleeveMax()));
    }

    public function compareWaist() {
        return $this->getArrayFill('waist', $this->compare($this->user_measurement->getWaist(), $this->product_measurement->getBustMin(), $this->product_measurement->getBustMax()));
    }

    public function compareOutseam() {
        return $this->getArrayFill('outseam', $this->compare($this->user_measurement->getOutseam(), $this->product_measurement->getOutseamMin(), $this->product_measurement->getOutseamMax()));
    }

    public function compareInseam() {
        // should fill an array element will message & values and return it
        //return $this->getArrayFill('inseam', $this->compare($u, $p_min, $p_max));
        return $this->getArrayFill('inseam', $this->compare($this->user_measurement->getInseam(), $this->product_measurement->getInseamMin(), $this->product_measurement->getInseamMax()));
    }

    public function compareHip() {
        return $this->getArrayFill('hip', $this->compare($this->user_measurement->getHip(), $this->product_measurement->getHipMin(), $this->product_measurement->getHipMax()));
    }

     
//----------------------------------------------------------------------    
    public function getArrayFill($measuring_point, $comparison_result) {


        if (is_null($measuring_point) || strlen($measuring_point) == 0) {
            return null;
        }

        $this->setMessageArray();

        if (is_null($comparison_result)) {
            return array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['np'], 'fit' => false);
        }

        if ($comparison_result > 0) {
            //add loose message //add diff //fits boolean false
            return array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['loose'], 'fit' => false);
        } elseif ($comparison_result < 0) {
            //add tight message //add diff //fits boolean false
            return array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['tight'], 'fit' => false);
        } else {
            //get love message //add 0 or inclination //fits boolean true
            return array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['fit'], 'fit' => true);
        }
    }

//----------------------------------------------------------------------

    protected function compare($u, $p_min, $p_max) {
        if (is_null($u) || is_null($p_min) || is_null($p_max)) {
            return null;
        }

        if ($u <= $p_max && $u >= $p_min) {
            return 0; //love
        } elseif ($u > $p_max) {
            return $p_max - $u; //tight: returns a negative value, difference of measurement in inches
        } elseif ($u < $p_min) {
            return $p_min - $u; //loose: returns a positive value, difference of measurement in inches
        } else {
            return null;
        }
    }

    //------------------------------------------------------------------------    
    function setMessageArray() {
        if (is_null($this->msg_array)) {
            $yaml = new Parser();
            $this->msg_array = $yaml->parse(file_get_contents('../app/config/fitting_feedback.yml'));
        }
    }

}
