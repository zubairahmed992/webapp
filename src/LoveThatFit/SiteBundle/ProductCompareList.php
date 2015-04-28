<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;
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
              $item = $productItem->getProductItemById($value['item_id']);
             
             $product=$item->getProduct();
              #$fe = new AvgAlgorithm($user,$product);
              $fe = new FitAlgorithm2($user,$product);
              $feed_back[$key]=$product->getDetailArray()+$fe->getFeedBack();
              $feed_back[$key]['current_item']=$value['item_id'];
        }
          return $feed_back;
     
            
        #list with feedback
        #loop through list & add feedback from algorithm
        #use for rendering fot to store in session
        $compareable_list=null;
        return $compareable_list;
    }
    
//---------------------------------------------------------------------
    function addItemToList($item) {
      $product= $item->getProduct();
        if ($this->productDoseNotExist($product->getId(), $item->getId())){            
            $this->list[$product->getId()]['id']=$product->getId();
            $this->list[$product->getId()]['item_id']=$item->getId();
            $this->list[$product->getId()]['product_name']=$product->getName();
            $this->list[$product->getId()]['image']=$item->getProductColor()->getWebPath();
            if(count($this->list)>3){
                #array_shift($this->list);            
                $keys = array_keys($this->list);
                unset($this->list[$keys[0]]);
            }
            
        }
        return $this->list;        
    }
    //---------------------------------------------------------------------
    function removeItemFromList($product_id) {        
        unset($this->list[$product_id]);
        return $this->list;        
    }
    #-------------------------------------->
    private function productDoseNotExist($product_id, $item_id){
        foreach($this->list as $p) { 
            if(array_key_exists('id', $p) && $product_id==$p['id']){
                $this->list[$product_id]['item_id']=$item_id;
                return false;
            }
        }
        return true;
    }

}
