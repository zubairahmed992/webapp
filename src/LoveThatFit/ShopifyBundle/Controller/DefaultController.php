<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
   public function indexAction()
    {
        $api_key='1464612a653803d72f4d6d998e59d785';
        $shop_domain = 'lovethatfit-2.myshopify.com';
        //$str = \sandeepshetty\shopify_api\install_url($shop_domain, $api_key);
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $api_key, array('read_products'));
        //return $this->redirect('https://'.$shop_domain.'/admin/oauth/authorize?client_id='.$api_key);
        return $this->redirect($str);
        
        
        
        $shared_secret='497d438714a4fc95f688850fc476caa5';
        $access_token = \sandeepshetty\shopify_api\oauth_access_token($shop_domain, $api_key, $shared_secret, $api_key);
        $str+=' you are da real MVP';
        
        return $this->render('LoveThatFitShopifyBundle:Default:index.html.twig', array('data'=> $str));
    }
    
     public function grantedAction()
    {
         $params = $this->getRequest()->request->all();
         return new Response(json_encode($params));
     }
}
