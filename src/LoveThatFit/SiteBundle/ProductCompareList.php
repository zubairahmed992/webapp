<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
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
    function getCompareableList($user,$productItem) {
        $feed_back=array();
        
        foreach($this->list as $key=>$value){
              $item = $productItem->getProductItemById($value['itemid']);
              $product=$item->getProduct();
              $fe = new AvgAlgorithm($user,$product);
              $feed_back[$key]=$product->getDetailArray()+$fe->getFeedBack();
              $feed_back[$key]['current_item']=$value['itemid'];
        }
          return $feed_back;
     
            
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
