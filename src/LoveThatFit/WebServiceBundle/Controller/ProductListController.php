<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class ProductListController extends Controller {
    #---------------------------------- /ltf_ws/get_category_products_list_with_banner ------------------------------------------
    public function getCategoryProductsListBannerAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $user_id = $user->getId();
        $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';

        $productlist = $this->get('webservice.helper')->getProductListByCategoryBanner($decoded['gender'], $decoded['category_ids'], $user_id);
        if (array_key_exists('display_screen', $decoded)) {
            $bannerlist = $this->get('admin.helper.Banner')->getBannerListForService($base_path,$decoded['display_screen']);
        } else {
            $bannerlist = $this->get('admin.helper.Banner')->getBannerListForService($base_path);
        }

        $bannerconf= array(
            'data' => $bannerlist,
            'count'=> count($bannerlist),
            'message' => 'Banner list',
            'success' => 'true',
        );

        $productconf= array(
            'data' => $productlist,
            'count'=> count($productlist),
            'message' => 'Product List',
            'success' => 'true',
        );

        $data = array(
            'count'=> 2,
            'message' => 'Success Result',
            'success' => 1,
        );
        $data['productlist'] = $productconf;
        $data['bannerlist'] = $bannerconf;

    return new Response(json_encode($data));
    }
	
	
	    #---------------------------------- /ltf_ws/get_category_products_list ------------------------------------------
    public function getCategoryProductsListAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $user_id = $user->getId();
        $productlist = $this->get('webservice.helper')->getProductListByCategory($decoded['gender'], $decoded['category_ids'], $user_id);
    return new Response($productlist);
    }
        
}

