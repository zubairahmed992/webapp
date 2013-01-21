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
           
               
       return $target;
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
        $difference = $this->product->getWaist() - $this->user_measurement->getWaist();
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
           case ($difference > -2):
               $str="more than 2 inch smaller";
               break;
       }
            
         return $str . " * " . $difference;   
        
                
    }
   

}
