<?php

namespace LoveThatFit\SiteBundle;

class Cart {

    //id, title, brand, clothingType, Quantity, price, image
    var $cart = array();

    public function __construct($cart) {
        if ($cart)
            $this->cart = $cart;
    }
//---------------------------------------------------------------------
    function getTotal() {
        $total = 0;
        foreach ($this->cart as $key => $value) {
            $total = $total + ($this->cart[$key]['quantity'] * $this->cart[$key]['price']);
        }

        return $total;
    }
//---------------------------------------------------------------------
    function getCart() {
        return $this->cart;
    }
//---------------------------------------------------------------------
    function removeFromCart($product) {
       return $this->removeMultipleFromCart($product, 1);
    }
    
//---------------------------------------------------------------------
    function addToCart($product) {
       return $this->addMultipleToCart($product, 1);
    }
//---------------------------------------------------------------------
    function removeMultipleFromCart($product, $qty) {

        foreach ($this->cart as $key => $value) {
            if ($value['id'] == $product->getId()) {
                if ($value['quantity'] > $qty) {
                    $this->cart[$key]['quantity'] = $this->cart[$key]['quantity'] - $qty;
                } else {
                    unset($this->cart[$key]);
                }
                return true;
            }
        }
        return false;
    }
//---------------------------------------------------------------------
    function addMultipleToCart($product, $qty) {

        if ($product) {
            if (!$this->addToExistingProduct($product, $qty)) {
                $this->addNewProducts($product, $qty);
            }
            return true;
        }
        else
            return false;
    }
//---------------------------------------------------------------------    
    private function addToExistingProduct($product, $qty) {
        foreach ($this->cart as $key => $value) {
            if ($value['id'] == $product->getId()) {
                $this->cart[$key]['quantity'] = $this->cart[$key]['quantity'] + $qty;
                return true;
            }
        }
        return false;
    }
//---------------------------------------------------------------------
    private function addNewProducts($product, $qty) {
       $imgPath=$product->getImagePaths()['icon'];
        array_push(
                $this->cart, array(
            "id" => $product->getId(),
            "title" => $product->getName(),
            "brand" => $product->getBrand()->getName(),
            "clothingType" => $product->getClothingType()->getName(),
            "quantity" => $qty,
            "price" => 99.99,
            "image" => $imgPath,
                )
        );
    }

}
