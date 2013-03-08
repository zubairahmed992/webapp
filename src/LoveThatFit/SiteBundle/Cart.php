<?php

namespace LoveThatFit\SiteBundle;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Cart {
    
    var $total;
    //id, Description, Quantity, price, image_url
    var $cart=array();
    
    function __construct() {
        
    }
    
    function getTotal() {
        return $this->total;
    }
    
    function getCart() {
        return $this->cart;
    }
    
    
    function removeFromCart($id) {
        return $this->cart;
    }
    
     function addToCart($product) {
        
        if ($product) {
            array_push(
                    $this->cart, array(
                $product->getId() => array(
                    "title" => $product->getName(),
                    "brand" => $product->getBrand()->getName(),
                    "clothingType" => $product->getClothingType()->getName(),
                    "quantity" => 1,
                    "price" => '$99.99',
                    "image" => $product->getImage(),
                )
                    )
            );
            
        }
        return $this->cart;
    }
    
}
