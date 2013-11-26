<?php

namespace LoveThatFit\SiteBundle;

class FitEngine {

    private $user;
    private $product_item;

    function __construct($user) {
        $this->setUser($user);
    }

    function setProduct($product_item) {
        $this->product_item = $product_item;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function getProduct() {
        return $this->product_item;
    }

    function getUser() {
        return $this->user;
    }

    function fit() {
        return 'what eva..';
    }
}
