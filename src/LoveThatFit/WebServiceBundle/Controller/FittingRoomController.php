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
                $res = $this->get('webservice.helper')->response_array(true, $resp);
                return new Response($res);
            }

            $item_id = $decoded["product_item_id"];
            $product_id = $decoded["product_id"];

            //Get Product item Object by item_id and also verify thhe product item and product id
            $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);
            if($productItem == null){
                $resp = 'Product Item not entered Properly';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
                return new Response($res);
            }
            $product = $productItem->getProduct();
            $product_id_for_verification = $product->getId();

            if($product_id_for_verification != $product_id){
                $resp = 'Product Item not Match with Product Id';
                $res = $this->get('webservice.helper')->response_array(true, $resp);
                return new Response($res);
            }

            //Checked that item is already then remove this
            $this->get('site.helper.userfittingroomitem')->deleteByUserItemByProduct($user, $product_id);

            //Add entry in userfittingroom table
            $this->get('site.helper.userfittingroomitem')->createUserFittingRoomItemWithProductId($user, $productItem, $product);
            $resp = 'Item has been deleted to Fitting Room Successfully';
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

            //Checked that item is already then remove this
            $this->get('site.helper.userfittingroomitem')->deleteByUserItemByProduct($user, $product_id);
            $resp = 'Producthas been deleted from Fitting Room Successfully';
            $res = $this->get('webservice.helper')->response_array(true, $resp);
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
}

