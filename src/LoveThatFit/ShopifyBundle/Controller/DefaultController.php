<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    #   temp-code = '8d9e6d19e48e3e002af09b19ea016081' 

    #   $api_key = '1464612a653803d72f4d6d998e59d785';
    #    $shared_secret = '497d438714a4fc95f688850fc476caa5';
    #    $shop_domain = 'lovethatfit-2.myshopify.com';    
    #    $access_token = '019860835bfd8ac1c5259702c5b953a9';
    
   public function indexAction()
    {
        $api_key='1464612a653803d72f4d6d998e59d785';
        $shop_domain = 'lovethatfit-2.myshopify.com';
        
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $api_key, array('read_products'));        
        return $this->redirect($str);
    }
    
     public function grantedAction()
    {
         $api_key = '1464612a653803d72f4d6d998e59d785';
         $shared_secret = '497d438714a4fc95f688850fc476caa5';
         $code = $this->getRequest()->query->get('code');          
         $shop_domain = $this->getRequest()->query->get('shop');        
         $access_token = \sandeepshetty\shopify_api\oauth_access_token($shop_domain, $api_key, $shared_secret, $code);
         return new Response('code :  ' .$code .'   access token: '. $access_token);
     }

     public function productsAction()
    {
        $api_key = '1464612a653803d72f4d6d998e59d785';
        $shared_secret = '497d438714a4fc95f688850fc476caa5';
        $shop_domain = 'lovethatfit-2.myshopify.com';    
        $access_token = '019860835bfd8ac1c5259702c5b953a9';
        
        $shopify = \sandeepshetty\shopify_api\client($shop_domain, $access_token, $api_key, $shared_secret);
        $products = $shopify('GET', '/admin/products.json', array('published_status'=>'published'));
        return new Response(json_encode($products));
     }
      
     public function fittingRoomAction()
    {
        #$str='/admin/themes/launchpad/assets.json?asset[key]=templates/product.liquid&theme_id=828155753';
        #return $this->redirect($str);

         return $this->render('LoveThatFitShopifyBundle:Default:fitting_room.html.twig');
     }
}
