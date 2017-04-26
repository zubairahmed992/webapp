<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class WSProductController extends Controller {

    public function productsAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if (array_key_exists('gender', $decoded)) {
            $res = $this->get('webservice.helper')->productSync($decoded['gender'], array_key_exists('date', $decoded) ? $decoded['date'] : null, $user);
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
    
    public function productLikeDefaultItemAction() {
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
    public function productLikeAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        
        if ($user) {
            $res = $this->get('webservice.helper')->likeUnlikeItem($user, $decoded);
         } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
        
    }
      #------------------------------------------------------------------------  
    public function userLikedProductsAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        
        if ($user) {            
            $res = $this->get('webservice.helper')->userLikedProductIds($user->getId());
         } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
        
    }
    #------------------------------------------------------------------------
      public function brandRetailerAction() {
        $decoded['base_path'] = $this->getRequest();
        #return new Response(json_encode($decoded) );
        $brand_retailer = $this->get('admin.helper.brand')->getBrandListForService();
        $brand_retailer['device_config'] = $this->get('admin.helper.device')->getDeviceConfig();
        $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        $brand_retailer['clothing_type'] = $this->get('admin.helper.clothingtype')->getDescriptionArray('f',$base_path);
        $brand_retailer['brand_top'] =  $this->get('admin.helper.brand')->getBrandListWithBannerForService(1);
        $brand_retailer['brand_bottom'] =  $this->get('admin.helper.brand')->getBrandListWithBannerForService(0);
        #return new Response(json_encode($brand_retailer['clothing_type']) );
        $res = $this->get('webservice.helper')->response_array(true, 'list of brand & retailers', true, $brand_retailer);                    
        return new Response($res);
    }
    #----------------------------------------------------
    public function productDetailAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $res = $this->get('webservice.helper')->productDetail($decoded['product_id'], $user);
         } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
        
    }


//*********************************************
// Webservice For 3.0
//**********************************************
    #----------------------------------------------------
    public function productDetailWithImagesAction() {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $res = $this->get('webservice.helper')->productDetailWithImages($decoded['product_id'], $user);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);

    }
    
}

