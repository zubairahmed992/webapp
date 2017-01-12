<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FittingRoomController extends Controller {

    #----------------------------------------------------Fitting Room Services Services -------------------------#
    // Add Single Item to Cart
    public function addToFittingRoomAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $item_id = $decoded["product_item_id"];
            //Get Product item Object by item_id
            $productItem = $this->get('admin.helper.productitem')->getProductItemById($item_id);

            //Checked that item is already added or not
            $records = $this->get('site.helper.userfittingroomitem')->getItemArrayByUser($user, $item_id);
            if($records > 0){
                $resp = 'Item has been already added to Fitting Room Successfully';
            }else{
                //Add entry in userfittingroom table
                $this->get('site.helper.userfittingroomitem')->createUserFittingRoomItem($user, $productItem);
                $resp = 'Item has been added to Fitting Room Successfully';
            }
            $res = $this->get('webservice.helper')->response_array(true, $resp);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
}

