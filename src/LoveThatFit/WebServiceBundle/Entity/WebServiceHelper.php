<?php

namespace LoveThatFit\WebServiceBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class WebServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    #------------------------ User -----------------------

    public function loginService($request_array) {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('webservice.helper.user')->matchPassword($user, $request_array['password'])) {
                $response_array = null;
                if (array_key_exists('user_detail', $request_array) && $request_array['user_detail'] == 'true') {
                    $response_array['user'] = $user->toDataArray(true, $request_array['deviceType']);
                    $response_array['user']['user_id'] = $response_array['user']['id'];
                    unset($response_array['user']['id']);
                }
                if (array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand'] == 'true') {
                    $retailer_brands = $this->container->get('admin.helper.brand')->getBrandRetailerList();
                    $response_array['retailer'] = $retailer_brands['retailer'];
                    $response_array['brand'] = $retailer_brands['brand'];
                }

                return $this->response_array(true, 'user found', true, $response_array);
            } else {
                return $this->response_array(false, 'Invalid Password');
            }
        } else {
            return $this->response_array(false, 'Invalid Email');
        }
    }

    #--------------------------------User Detail Array -----------------------------#

    private function getBasePath($request) {
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    }

    #----------------------------------------------------------------------------------------

    public function response_array($success, $message = null, $json = true, $data = null) {
        $ar = array(
            'data' => $data,
            'message' => $message,
            'success' => $success,
        );
        return $json ? json_encode($ar) : $ar;
    }

    #----------------------------------------------------------------------------------------

    public function emailExists($email) {
        $user = $this->container->get('user.helper.user')->findByEmail($email);
        return $user ? true : false;
    }
    #----------------------------------------------------------------------------------------
    
    public function sizeChartsService($request_array) {
        $sc = $this->container->get('admin.helper.sizechart')->getBrandSizeTitleArray($request_array['gender']);
        if (count($sc) > 0) {
                return $this->response_array(true, 'Size charts', true, $sc);
        } else {
            return $this->response_array(false, 'Size Charts not found');            
        }
    }
    
}