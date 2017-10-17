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

        $yaml   = new Parser();
        $static_category_banner = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/category_banner.yml'))['category_banner'];       

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

        $i = 1;
        $flag = true;
        foreach($bannerList as $value){
            if($i==$static_category_banner['sorting'])
            {
             $arrBannerList[] = $static_category_banner;  
             $flag = false;       
            }   
            $arrBannerList[] = $value; 
          $i++;      
        }
        if($flag){
          $arrBannerList[] = $static_category_banner;  
        }

        $bannerconf= array(
            'data' => $arrBannerList,
            'count'=> count($arrBannerList),
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
        $entity = $this->get('admin.helper.banner')->find($banner_id);
        if ($entity) {
            if ($entity->getBannerType() == 8) {
                $filter = json_decode($entity->getBannerFilter(), true);
                $res = $this->get('webservice.helper')->getProductListBannerFilter(json_decode($filter, true), $user_id);
            } else {
                $ar = array(
                    'data' => null,
                    'count' => 0,
                    'message' => "invalid banner id",
                    'success' => false,
                );
                $res = json_encode($ar);
            }
        } else {
            $ar = array(
                'data' => null,
                'count' => 0,
                'message' => "invalid banner id",
                'success' => false,
            );
            $res = json_encode($ar);
        }

        return new Response($res);
    }

    public function getBrandStyleListAction()
    {

        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        //Find the user against token id
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $brandList = $this->get('admin.helper.Brand')->getBrandListEnable();
            $styleList = $this->get('admin.helper.Product')->getStyleListEnable();

            $brandconf= array(
                'data' => $brandList,
                'count'=> count($brandList),
                'message' => 'Brand list',
                'success' => 'true',
            );

            $styleconf= array(
                'data' => $styleList,
                'count'=> count($styleList),
                'message' => 'Style list',
                'success' => 'true',
            );

            $data = array(
                'count'=> 2,
                'message' => 'Success Result',
                'success' => 1,
            );
            $data['brand'] = $brandconf;
            $data['style'] = $styleconf;

            return new Response(json_encode($data));

        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }
    
}

