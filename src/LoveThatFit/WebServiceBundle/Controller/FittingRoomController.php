<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FittingRoomController extends Controller {

    #----------------------------------------------------Fitting Room Services Services -------------------------#


    // Add & Update single item in Fitting room
    public function addUpdateToFittingRoomAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if(empty($decoded["product_item_id"]) || empty($decoded["product_id"])){
                $resp = 'Either product id or product item id not found';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }

            $item_id = $decoded["product_item_id"];
            $product_id = $decoded["product_id"];
            $update_product_item_id = $decoded["update_product_item_id"];

            $qty = 1;
            if(isset($decoded["qty"])){
                $qty = $decoded["qty"];
            }

            //Get Product item Object by item_id and also verify thhe product item and product id
            $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);
            if($productItem == null){
                $resp = 'Product Item not entered Properly';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }
            $product = $productItem->getProduct();
            $product_id_for_verification = $product->getId();

            if($product_id_for_verification != $product_id){
                $resp = 'Product Item not Match with Product Id';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }

            //Get ProductItem Id
            $product_item_id = $productItem->getId();

            //Get Information on the Userfitting room table and added quantity with the given quantity
            $verified_entry = $this->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user, $product_id, $product_item_id);
            $get_qty = 0;
            if($verified_entry[0][1] != "0"){
                $get_qty = $verified_entry[0]['qty'];
                $qty = $qty;
            }

            if($qty < 1){
                $resp = 'Quantity is below than one, Please provide appropriate quantity value.';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }


            /* If Product item will update_product_item then update product_item with update_product_item_id*/
            if(!empty($update_product_item_id)){

                //Check Update Product item id on the product
                $updatedProductItem = $this->get('admin.helper.productitem')->getProductItemById($update_product_item_id);
                if($updatedProductItem == null){
                    $resp = 'Updated Product Item not entered Properly';
                    $res = $this->get('webservice.helper')->response_array(false, $resp);
                    return new Response($res);
                }
                $updatedProduct = $updatedProductItem->getProduct();
                $updated_product_id_for_verification = $updatedProduct->getId();

                if($updated_product_id_for_verification != $product_id){
                    $resp = 'Updated Product Item not Match with Product Id';
                    $res = $this->get('webservice.helper')->response_array(false, $resp);
                    return new Response($res);
                }

                //Add entry in userfittingroom table
                $this->get('site.helper.userfittingroomitem')->updateUserFittingRoomItemWithProductId($user, $product_id, $product_item_id, $update_product_item_id);
                $res = $this->get('webservice.helper')->response_array(true, 'Product Item has been updated');
                return new Response($res);
            }


            //Checked that item is already then remove this
            $this->get('site.helper.userfittingroomitem')->deleteByUserItemByProduct($user, $product_id, $product_item_id);
            //Add entry in userfittingroom table
            $this->get('site.helper.userfittingroomitem')->createUserFittingRoomItemWithProductId($user, $productItem, $product, $qty);
            $resp = 'Item has been Add/Update to Fitting Room Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }


    // Delete product in Fitting room
    public function deleteToFittingRoomAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            if(empty($decoded["product_id"])){
                $resp = 'Product id parameter not found';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
                return new Response($res);
            }


            $product_id = $decoded["product_id"];
            $product_item_id = $decoded["product_item_id"];

            //Get Product item Object by item_id and also verify thhe product item and product id
            $productItem = $this->get('admin.helper.productitem')->getProductItemById($product_item_id);
            if($productItem == null){
                $resp = 'Product Item not entered Properly';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }

            $product = $productItem->getProduct();
            $product_id_for_verification = $product->getId();

            if($product_id_for_verification != $product_id){
                $resp = 'Product Item not Match with Product Id';
                $res = $this->get('webservice.helper')->response_array(false, $resp);
                return new Response($res);
            }

            //Checked that item is already then remove this
            $response = $this->get('site.helper.userfittingroomitem')->deleteByUserItemByProduct($user, $product_id, $product_item_id);
            if ($response != null) {
                $resp = 'Products has been deleted from Fitting Room Successfully';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, "Products item or Product not deleted from Fitting Room");
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }



    // Delete product in Fitting room
    public function getAllFittingRoomAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $user_data =  $this->get('site.helper.userfittingroomitem')->getAllFittingRoom($user);
            $res = $this->get('webservice.helper')->response_array(true, 'success', true, $user_data);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }


    public function addUpdateToFittingRoomItemsAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {

            $items = isset($decoded["items"]) ? $decoded["items"] : "0";

            if ($items != 0) {
                $checked_Error = '';
                //Validate all changes before adding
                foreach ($items as $detail) {

                    if (empty($detail["product_item_id"]) || empty($detail["product_id"])) {
                        $checked_Error = 'error_contained';
                        $resp = 'Either product id or product item id not found';
                        $res = $this->get('webservice.helper')->response_array(false, $resp);
                        return new Response($res);
                    }
                    $item_id = $detail["product_item_id"];
                    $product_id = $detail["product_id"];
                    $qty = 1;
                    if(isset($detail["qty"])){
                        $qty = $detail["qty"];
                    }

                    //Get Product item Object by item_id and also verify thhe product item and product id
                    $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);
                    if($productItem == null){
                        $checked_Error = 'error_contained';
                        $resp = 'Product Item not entered Properly';
                        $res = $this->get('webservice.helper')->response_array(false, $resp);
                        return new Response($res);
                    }
                    $product = $productItem->getProduct();
                    $product_id_for_verification = $product->getId();

                    if($product_id_for_verification != $product_id){
                        $checked_Error = 'error_contained';
                        $resp = 'Product Item not Match with Product Id';
                        $res = $this->get('webservice.helper')->response_array(false, $resp);
                        return new Response($res);
                    }

                    //Get ProductItem Id
                    $product_item_id = $productItem->getId();

                    //Get Information on the Userfitting room table and added quantity with the given quantity
                    $verified_entry = $this->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user, $product_id, $product_item_id);
                    $get_qty = 0;
                    if($verified_entry[0][1] != "0"){
                        $get_qty = $verified_entry[0]['qty'];
                        //$qty = $qty + $get_qty;
                        $qty = $qty;
                    }

                    if($qty < 1){
                        $checked_Error = 'error_contained';
                        $resp = 'Quantity is below than one, Please provide appropriate quantity value.';
                        $res = $this->get('webservice.helper')->response_array(false, $resp);
                        return new Response($res);
                    }
                }

                if($checked_Error == ''){

                    // Adding Data after successfull validation
                    foreach ($items as $detail_add) {
                        $item_id = $detail_add["product_item_id"];
                        $product_id = $detail_add["product_id"];
                        $qty = 1;
                        if(isset($detail_add["qty"])){
                            $qty = $detail_add["qty"];
                        }

                        //Get Product item Object by item_id and also verify thhe product item and product id
                        $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);

                        $product = $productItem->getProduct();
                        $product_id_for_verification = $product->getId();

                        //Get ProductItem Id
                        $product_item_id = $productItem->getId();

                        //Get Information on the Userfitting room table and added quantity with the given quantity
                        $verified_entry = $this->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user, $product_id, $product_item_id);
                        $get_qty = 0;
                        if($verified_entry[0][1] != "0"){
                            $get_qty = $verified_entry[0]['qty'];
                            $qty = $qty + $get_qty;
                        }

                        //Checked that item is already then remove this
                        $this->get('site.helper.userfittingroomitem')->deleteByUserItemByProduct($user, $product_id, $product_item_id);
                        //Add entry in userfittingroom table
                        $this->get('site.helper.userfittingroomitem')->createUserFittingRoomItemWithProductId($user, $productItem, $product, $qty);
                    }

                    $resp = 'Item has been Add/Update to Fitting Room Successfully';
                    $res = $this->get('webservice.helper')->response_array(true, $resp);
                    return new Response($res);
                }

            } else {
                $res = $this->get('webservice.helper')->response_array(false, 'Array Item not found');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }
}

