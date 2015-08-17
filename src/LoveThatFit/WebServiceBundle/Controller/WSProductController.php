<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSProductController extends Controller {

    public function productsAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        
        if (array_key_exists('gender', $decoded)) {
            $res = $this->get('webservice.helper')->productSync($decoded['gender'], array_key_exists('date', $decoded) ? $decoded['date'] : null);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'gender not specified');
        }
        return new Response($res);
    }

    #----------------------------------------------------------------------------------------
    
    public function productListAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $res = $this->get('webservice.helper')->productList($user, array_key_exists('list_type', $decoded) ? $decoded['list_type'] : null);
         } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
        
    }
    
    #----------------------------------------------------------------------------------------
    
    private function authenticate(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
             $res = $this->get('webservice.helper')->response_array(true, 'User Authenticated', false, $user);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }
    
    #----------------------------------------------------------------------------------------
    
    public function loveItemAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $res = $this->get('webservice.helper')->loveItem($user, $decoded);
         } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
        
    }
    #------------------------------------------------------------------------
      public function brandRetailerAction() {
          #return new Response('dddddddd');
        $brand_retailer = $this->container->get('admin.helper.brand')->getBrandListForService();
        $res = $this->get('webservice.helper')->response_array(true, 'list of brand & retailers', true, $brand_retailer);                    
        return new Response($res);
    }
}
