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
         $specs= $this->get('shopify.helper')->appSpecs();
         return new Response(json_encode($specs));
         #$specs['api_key'] = '584e96437b334b01028908e63a602204';
         #$specs['shared_secret'] = '0d694e4a176838a97fa10d3dd81491dd';
         #$specs['temp_code'] = $this->getRequest()->query->get('code');          
         #$specs['shop_domain'] = $this->getRequest()->query->get('shop');        
         
         #$specs['access_token'] = \sandeepshetty\shopify_api\oauth_access_token($specs['shop_domain'], $specs['api_key'], $specs['shared_secret'], $specs['temp_code']);
         
         $specs['api_key'] = '584e96437b334b01028908e63a602204';
         $specs['shared_secret'] = '0d694e4a176838a97fa10d3dd81491dd';
         $specs['temp_code'] = 'davinci_code';          
         $specs['shop_domain'] = 'lovethatfit-2.myshopify.com';
         $specs['access_token'] = 'hush hush hush';
         
        # update in ltf database
         $this->get('admin.helper.retailer')->updateRetailShopSpecs($specs);
         return new Response(json_encode($specs));
         
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
        return new Response($this->foo($products));
     }
      
     
     private function foo($product_json){
         #return $product_json;
         $products=  json_decode($product_json,true);
         $str='';
         foreach ($products['products'] as $p=>$key) {
            $str = $str .' __________ '. $key['title'];    
         }
         return $str;
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
     
     public function listAction()
    {
        $latest = $this->get('admin.helper.product')->listByType(array('limit'=>5, 'list_type'=>'latest'));
         return $this->render('LoveThatFitShopifyBundle:Default:index.html.twig', array('products'=> $latest));
    }
    
       public function fittingRoomShowAction($product_id=0)
    {
        #$product=$product_id!=0?$this->get('admin.helper.product')->find($product_id):null;
         $product=$this->get('admin.helper.product')->find($product_id);
         return $this->render('LoveThatFitShopifyBundle:Default:fitting_room.html.twig', array('product'=>$product));
    }
    
    
}
