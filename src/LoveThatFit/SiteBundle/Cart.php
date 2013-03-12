<?php

namespace LoveThatFit\SiteBundle;

class Cart {

    //id, Description, Quantity, price, image_url
    var $cart = array();

    public function __construct($cart) {
        if ($cart)
            $this->cart = $cart;
    }

    function getTotal($product) {
        $total = 0;
        foreach ($this->cart as $key => $value) {
            $total = $total + ($this->cart[$key]['quantity'] * $this->cart[$key]['price']);
        }

        return $total;
    }

    function getCart() {
        return $this->cart;
    }

    function removeFromCart($product) {

        foreach ($this->cart as $key => $value) {
            if ($value['id'] == $product->getId()) {
                if ($value['quantity'] > 1) {
                    $this->cart[$key]['quantity'] = $this->cart[$key]['quantity'] - 1;
                } else {
                    unset($this->cart[$key]);
                }
                return true;
            }
        }
        return false;
    }

    function addToCart($product) {

        if ($product) {
            if (!$this->addToExistingProduct($product)) {
                $this->addNewProduct($product);
            }
            return true;
        }
        else
            return false;
    }

    private function addToExistingProduct($product) {
        foreach ($this->cart as $key => $value) {
            if ($value['id'] == $product->getId()) {
                $this->cart[$key]['quantity'] = $this->cart[$key]['quantity'] + 1;
                return true;
            }
        }
        return false;
    }

    private function addNewProduct($product) {
        array_push(
                $this->cart, array(
            "id" => $product->getId(),
            "title" => $product->getName(),
            "brand" => $product->getBrand()->getName(),
            "clothingType" => $product->getClothingType()->getName(),
            "quantity" => 1,
            "price" => '$99.99',
            "image" => $product->getImage(),
                )
        );
    }

}
