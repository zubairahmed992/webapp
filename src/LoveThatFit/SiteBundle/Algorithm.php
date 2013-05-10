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
        
        if($user){
        $this->user = $user;
        $this->user_measurement = $this->user->getMeasurement();
        }
        if($product_item){
        $this->product = $product_item->getProduct();
       $this->product_measurement = $product_item->getProductSize();
        }
        
    }

//------------------------------------------------------------------------

    function setProductMeasurement($product_measurement) {
        $this->product_measurement = $product_measurement;
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
    
    function filter(){
        
        if ($this->user->getGender()=='m'){
            if ($this->product->getClothingType()->getTarget()=='Top'){
                //chect neck & sleeve* / back, waist
            }elseif ($this->product->getClothingType()->getTarget()=='Bottom'){
                //waist & inseam / outseam
                return $this->compareInseam();
            }else{
                return null;
            }
        }elseif ($this->user_measurement->getGendet()=='m'){
            
            if ($this->product->getClothingType()->getTarget()=='Top'){
                //bust, waist, back & sleeve*
            }elseif ($this->product->getClothingType()->getTarget()=='Bottom'){
                //waist, hip, inseam / outseam
                return $this->compareInseam();
            }elseif ($this->product->getClothingType()->getTarget()=='Dress'){
                //bust, hip, waist, back & sleeve*
            }else{
                return null;
            }
        }else{
               return null;
        }
    }
    
//------------------- comparison methods
    
    //inseam outseam hip bust chest back length waist neck sleeve
    
    
    public function compareInseam()
    {
        // should fill an array element will message & values and return it
        //return $this->getArrayFill('inseam', $this->compare($u, $p_min, $p_max));
        return $this->getArrayFill('inseam', $this->compare($this->user_measurement->getInseam(), 
                $this->product_measurement->getInseamMin(), 
                $this->product_measurement->getInseamMax()));
        
    }
    
    
    public function compareOutseam()
    {
        return $this->getArrayFill('inseam', $this->compare($this->user_measurement->getInseam(), 
                $this->product_measurement->getInseamMin(), 
                $this->product_measurement->getInseamMax()));
    }
    
//----------------------------------------------------------------------    
    public function getNullFill($measuring_point){
        // incase if any of  the value is null fill null statement
        
        return null;
    }
    
//----------------------------------------------------------------------    
    public function getArrayFill($measuring_point, $comparison_result){
        if (is_null($comparison_result)){
            return $this->getNullFill($measuring_point);
        }
        
        if (is_null($measuring_point) || strlen($measuring_point)==0){
            return null;
        }
            $this->setMessageArray();
        if ($comparison_result>0){
            //add loose message //add diff //fits boolean false
        return array("{$measuring_point}" => array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['loose'], 'fit' => false));
        }elseif($comparison_result<0){
            //add tight message //add diff //fits boolean false
            return array("{$measuring_point}" => array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['tight'], 'fit' => false));
        }else{
            //get love message //add 0 or inclination //fits boolean true
            return array("{$measuring_point}" => array("diff" => $comparison_result, "msg" => $this->msg_array["{$measuring_point}"]['fit'], 'fit' => true));
        }
    }
//----------------------------------------------------------------------

    protected function compare($u, $p_min, $p_max)
    {
        if (is_null($u) || is_null($p_min) || is_null($p_max)){
            return null;
        }
        
        if ($u<=$p_max && $u>=$p_min){
            return 0; //love
            
        }elseif ($u>$p_max){
            return $p_max - $u; //tight: returns a negative value, difference of measurement in inches
            
        }elseif ($u<$p_min){
            return $p_min- $u; //loose: returns a positive value, difference of measurement in inches
            
        }else{
         return null;   
        }
    }

    //------------------------------------------------------------------------    
     function setMessageArray() {
        $yaml = new Parser();
        $this->msg_array = $yaml->parse(file_get_contents('../app/config/fitting_feedback.yml'));        
    }

    
}
