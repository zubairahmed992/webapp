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
        $page_no = (isset($decoded['page_no'])) ? $decoded['page_no'] : 1 ;
        $result_set = $this->get('webservice.helper')->getProductListByCategoryBanner($decoded['gender'], $decoded['category_ids'], $user_id, $page_no);
        $productlist = $result_set['product_list'];
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

        $page_count = $result_set['page_count'];
        if (($page_count != 0 && $page_no < 1) || ($page_count != 0 && $page_no > $page_count)) {
            $productconf= array(
                'data' => null,
                /*'page_count' => $result_set['page_count'],*/
                'count'=> 0,
                'message' => 'Invalid Page No',
                'success' => 'false',
            );
        } else {
            $productconf= array(
                'data' => $productlist,
                'page_count' => $result_set['page_count'],
                'count'=> count($productlist),
                'message' => 'Product List',
                'success' => 'true',
            );
        }

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
        $page_no = (isset($decoded['page_no'])) ? $decoded['page_no'] : 1 ;
        $productlist = $this->get('webservice.helper')->getProductListByCategory($decoded['gender'], $decoded['category_ids'], $user_id, $page_no);
    return new Response($productlist);
    }
        
}

