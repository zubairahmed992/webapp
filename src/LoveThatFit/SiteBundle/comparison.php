<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;

class comparison
{
    var $user_measurement;
    var $product;
    
    function __construct($measurement, $outfit) {
    $this->user_measurement=$measurement;
    $this->product=$outfit;
    
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
       
       return $this->compare_waist();
       
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
   
    function compare_waist(){
        $str="";
        $difference =$this->user_measurement->getWaist();# $this->product->getWaist() - $this->user_measurement->getWaist();
         switch($difference)
       {
           case 0:
               $str="perfect match";
               break;
           case 1:
               $str="1 inch bigger";               
               break;
           case ($difference > 1 && $difference < 2):
                $str="more than 1  inch bigger";
               break;
            case 2:
               $str="2  inch bigger";
               break;
           case ($difference > 2):
               $str="more than 2 inch bigger";
               break;
           case ($difference > -1 && $difference < 0):
               $str="smaller";
                break;
           case -1:
               $str="1  inch smaller";
               break;
           case ($difference > -1 && $difference < -2):
               $str="more than 1 inch smaller";
                break;
            case -2:
               $str="2 inch smaller";
               break;
           case ($difference < -2):
               $str="more than 2 inch smaller";
               break;
       }
            
         return $str . " * " . $difference;   
        
                
    }
    
    function getDifference(){
          if (!$this->user_measurement)
           return "Measurement not found.";
       
       if (!$this->product)
           return "Product not found.";
       $msg=$this->getMessageArray();
        $array = array(
        "waist" => array("diff" => $this->product->getWaist() - $this->user_measurement->getWaist(), "msg"=>$msg["waist"][$this->get_key($this->product->getWaist() - $this->user_measurement->getWaist())]),
        "hip" => array("diff" => $this->product->getHip() - $this->user_measurement->getHip(), "msg"=>$msg["hip"][$this->get_key($this->product->getHip() - $this->user_measurement->getHip())]),
        "bust" => array("diff" => $this->product->getBust() - $this->user_measurement->getBust(), "msg"=>$msg["bust"][$this->get_key($this->product->getBust() - $this->user_measurement->getBust())]),
        "arm" => array("diff" => $this->product->getArm() - $this->user_measurement->getArm(), "msg"=>$msg["arm"][$this->get_key($this->product->getArm() - $this->user_measurement->getArm())]),
        "leg" => array("diff" => $this->product->getLeg() - $this->user_measurement->getLeg(), "msg"=>$msg["leg"][$this->get_key($this->product->getLeg() - $this->user_measurement->getLeg())]),
        "inseam" => array("diff" => $this->product->getInseam() - $this->user_measurement->getInseam(), "msg"=>$msg["inseam"][$this->get_key($this->product->getInseam() - $this->user_measurement->getInseam())]),
        "back" => array("diff" => $this->product->getBack() - $this->user_measurement->getBack(), "msg"=>$msg["back"][$this->get_key($this->product->getBack() - $this->user_measurement->getBack())]),  
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
