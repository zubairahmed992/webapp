<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\SiteBundle\AvgAlgorithm;
class ProductCompareList {

    //id, title, brand, clothingType, Quantity, price, image
    var $list = array();

    public function __construct($list) {
        if ($list)
            $this->list = $list;  
    }
//---------------------------------------------------------------------
    function getList() {
        return $this->list;
    }
    //---------------------------------------------------------------------
    function getCompareableList($user) {
        #list with feedback
        #loop through list & add feedback from algorithm
        #use for rendering fot to store in session
        $compareable_list=null;
        return $compareable_list;
    }

//---------------------------------------------------------------------
    function removeItemFromList($item) {
    #remove this item
        return $this->list;
    }
    
//---------------------------------------------------------------------
    function addItemToList($item) {
        $product=$item->getProduct();
        $this->list[$product->getId()]['product_id']=$item->getId();
        $this->list[$product->getId()]['item_id']=$item->getId();
        $this->cutExtraItem();
        return $this->list;        
    }
    private function cutExtraItem() {
        if(count($this->list)>3)
            return $true;# remove first item
    }
    

}
