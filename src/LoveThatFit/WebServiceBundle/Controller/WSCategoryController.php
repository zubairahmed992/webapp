<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class WSCategoryController extends Controller {



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
    
    #---------------------------------- /ltf_ws/get_category_list ------------------------------------------
    public function getBannerCategoryListAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $base_path = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';



        if (array_key_exists('gender', $decoded)) {
            $categoryList = $this->get('admin.helper.Categories')->getCategoryListForService($base_path, $decoded['gender']);
        } else {
            $categoryList = $this->get('admin.helper.Categories')->getCategoryListForService($base_path);
        }

        if (array_key_exists('auth_token', $decoded)) {
            $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
            $user_id = $user->getId();
            $bannerList = $this->get('admin.helper.Banner')->getBannerListForService($base_path, 'shop', $user_id);
        } else {
            $bannerList = $this->get('admin.helper.Banner')->getBannerListForService($base_path, 'shop');
        }

        $bannerconf= array(
            'data' => $bannerList,
            'count'=> count($bannerList),
            'message' => 'Banner list',
            'success' => 'true',
        );

        $categoryconf= array(
            'data' => $categoryList,
            'count'=> count($categoryList),
            'message' => 'Category list',
            'success' => 'true',
        );

        $data = array(
            'count'=> 2,
            'message' => 'Success Result',
            'success' => 1,
        );
        $data['banner'] = $bannerconf;
        $data['category'] = $categoryconf;

        return new Response(json_encode($data));
    }



    public function getBannerBrandProductsAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $user_id = $user->getId();
        $brand_id = $decoded['brand_id'];
        $res = $this->get('webservice.helper')->getBannerBrandProduct($brand_id, $user_id);
        return new Response($res);
    }

    public function getBannerFilterProductsAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $user_id = $user->getId();
        $banner_id = $decoded['banner_id'];
        $filter =  json_decode($this->get('admin.helper.banner')->find($banner_id)->getBannerFilter(), true);
        $res = $this->get('webservice.helper')->getProductListBannerFilter(json_decode($filter, true), $user_id);
        return new Response($res);
    }
    
}

