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
#Non embedded app    
#----------------------------------------------------------
    #    $api_key = '584e96437b334b01028908e63a602204';
    #    $shared_secret = '0d694e4a176838a97fa10d3dd81491dd';
    #    $shop_domain = 'lovethatfit-2.myshopify.com';    
    #    $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';
    
   public function indexAction()
    {
        $api_key='584e96437b334b01028908e63a602204';
        $shop_domain = 'lovethatfit-2.myshopify.com';        
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $api_key, array('read_products', 'write_content', 'write_themes', 'read_customer'));        
        return new Response($str);
        return $this->redirect($str);
    }
    
     public function grantedAction()
    {
         $api_key = '584e96437b334b01028908e63a602204';
         $shared_secret = '0d694e4a176838a97fa10d3dd81491dd';
         $code = $this->getRequest()->query->get('code');          
         $shop_domain = $this->getRequest()->query->get('shop');        
         $access_token = \sandeepshetty\shopify_api\oauth_access_token($shop_domain, $api_key, $shared_secret, $code);
         return new Response('code :  ' .$code .'   access token: '. $access_token);
     }

     public function productsAction()
    {
        #$api_key = '1464612a653803d72f4d6d998e59d785';
        #$shared_secret = '497d438714a4fc95f688850fc476caa5';
        #$access_token = '019860835bfd8ac1c5259702c5b953a9';
    
        #------------ unembedded -------------- 
        $api_key = '584e96437b334b01028908e63a602204';
        $shared_secret = '0d694e4a176838a97fa10d3dd81491dd';
        $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';
               
        
        $shop_domain = 'lovethatfit-2.myshopify.com';                  
        $shopify = \sandeepshetty\shopify_api\client($shop_domain, $access_token, $api_key, $shared_secret);
        $products = $shopify('GET', '/admin/products.json');
        return new Response(json_encode($products));
     }
      
     public function fittingRoomAction()
    {
         
        $response = new Response();        
        $response->headers->set('Content-Type', 'application/liquid');        
        $response->setContent('Hoooaa the boy is alive!');
        return $response; 
         return new Response('Hooaa!');
         return $this->render('LoveThatFitShopifyBundle:Default:fitting_room.html.twig');
         
          $api_key = '584e96437b334b01028908e63a602204';
        $shared_secret = '0d694e4a176838a97fa10d3dd81491dd';
        $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';
        $shop_domain = 'lovethatfit-2.myshopify.com';         
        #$str='/admin/themes/launchpad/assets.json?asset[key]=templates/product.liquid&theme_id=828155753';
        #return $this->redirect($str);
#GET /admin/themes/#{id}/assets.json
         $shopify = \sandeepshetty\shopify_api\client($shop_domain, $access_token, $api_key, $shared_secret);
           $products = $shopify('GET', '/admin/themes/8753791/assets.json');
           return new Response(json_encode($products));
         return $this->render('LoveThatFitShopifyBundle:Default:fitting_room.html.twig');
     }
}
