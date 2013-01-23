<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;

class comparison
{
    var $user_measurement;
    var $product;
    var $adjustment;
    
    function __construct($measurement, $outfit) {
    $this->user_measurement=$measurement;
    $this->product=$outfit;    
    $this->adjustment=$this->product->getAdjustment();    
    }
    
   function determine(){
    
       if (!$this->user_measurement)
           return "Measurement not found.";
       
       if (!$this->product)
           return "Product not found.";
       
       
       $target= $this->product->getClothingType()->getTarget();
       switch($target)
       {
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
   
   function determine_top(){
       
       return "Top aai";
       
   }
   
   function determine_bottom(){
       
       return "bottom aai";
   }
   function determine_dress(){
       
       return "dress aai";
   }
   
   function compare($us,$os){
       return $os-$us;
   }
   
   function getDifference(){
          if (!$this->user_measurement)
           return "Measurement not found.";
          
       
       if (!$this->product)
           return "Product not found.";
       
       	$waist_diff = $this->product->getWaist() - $this->user_measurement->getWaist();
        $hip_diff = $this->product->getHip() - $this->user_measurement->getHip();
        $bust_diff = $this->product->getBust() - $this->user_measurement->getBust();
        $arm_diff = $this->product->getArm() - $this->user_measurement->getArm();
        $leg_diff = $this->product->getLeg() - $this->user_measurement->getLeg();
        $inseam_diff = $this->product->getInseam() - $this->user_measurement->getInseam();
        $back_diff = $this->product->getBack() - $this->user_measurement->getBack();
       
       $msg=$this->getMessageArray();
        $array = array(
        "waist" => array("diff" => $waist_diff, "msg"=>$msg["waist"][$this->get_key($waist_diff)]),
        "hip" => array("diff" => $hip_diff, "msg"=>$msg["hip"][$this->get_key($hip_diff)]),
        "bust" => array("diff" => $bust_diff, "msg"=>$msg["bust"][$this->get_key($bust_diff)]),
        "arm" => array("diff" => $arm_diff, "msg"=>$msg["arm"][$this->get_key($arm_diff)]),
        "leg" => array("diff" => $leg_diff, "msg"=>$msg["leg"][$this->get_key($leg_diff)]),
        "inseam" => array("diff" => $inseam_diff, "msg"=>$msg["inseam"][$this->get_key($inseam_diff)]),
        "back" => array("diff" => $this->product->getBack() - $this->user_measurement->getBack(), "msg"=>$msg["back"][$this->get_key($back_diff)]),  
);
    
        return $array;
    }
    
    function get_key($difference)
    {
        $str="";
          switch($difference)
       {
           case 0:
               $str="zero";
               break;
           case 1:
               $str="one";               
               break;
            case 2:
               $str="two";
               break;
           case ($difference > 2):
               $str="more_than_two";
               break;
           case -1:
               $str="one_minus";
               break;
            case -2:
               $str="two_minus";
               break;
           case ($difference < -2):
               $str="less_than_two_minus";
               break;
       }
            
        return $str;
    }
    
    function getMessageArray(){
          return     array (
	"sku" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
        
    "waist" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      
    "hip" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),

    "bust" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),

    "arm" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      

    "leg" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      
    
    "inseam" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      

    "outseam" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      

    "hem" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      

    "back" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
      
      
    "length" => array(
      "less_than_two_minus" => 'Less than two inch short',
      "two_minus" => 'two inch short',
      "one_minus" => 'one inch short',
      "zero" => 'Exact fit',
      "one" =>  'One inch bigger',
      "two" =>  'two inch bigger',
      "more_than_two" =>  'more than two inch bigger'),
	  );
      

    }
   

}
