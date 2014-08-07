<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller {

    public function installAction() {
        
        $app_specs = $this->get('shopify.helper')->appSpecs();
        //$app_specs['api_key']='c3fdd1592f0d8152d11d8b3aa039fa56';
        $app_specs['shop_domain']='bicycles-122.myshopify.com';// Dont use http or https prefix 
        $str=$this->get('shopifylib.helper')->install($app_specs);
        //$str = $this->permission_url($app_specs['shop_domain'], $app_specs['api_key'], array('read_products','write_themes','write_content'));
        return $this->redirect($str);
    }
 
        
   #-------------Call back url redirect here ---------------------------------#
        public function grantedAction() {
        //$app_specs = $this->get('shopify.helper')->appSpecs();
        $specs['api_key'] = 'c3fdd1592f0d8152d11d8b3aa039fa56';
        $specs['shared_secret'] = '2e347229dc81bcd7ce44871b51a99345';

        # to serve the shopify server app install request :--------------------
        $specs['temp_code'] = $this->getRequest()->query->get('code');          
        $specs['shop_domain'] = 'bicycles-122.myshopify.com';
        $specs['access_token'] = $this->get('shopifylib.helper')->oauth_access_token($specs['shop_domain'], $specs['api_key'], $specs['shared_secret'], $specs['temp_code']);
        
        $specs['shop_type'] = 'shopify';          
        if($specs['access_token']){
            
            $shopify = $this->get('shopifylib.helper')->client($specs['shop_domain'], $specs['access_token'], $specs['api_key'], $specs['shared_secret']);
            $content = trim(preg_replace('/\s\s+/', '\n ', $this->get('shopifylib.helper')->getContent($specs)));
            $resp=json_encode($this->get('shopifylib.helper')->writeFile('snippets/foo.liquid', $content,$shopify));
             return new Response("<html><body>Congratulation! The LTF app has been successfully installed at your store .
             <br>
             <a href=http://".$specs['shop_domain']." >Click here </a>
            </body></html>");
          }else{
            return new Response("Some thing went wrong!");
        }
        
    }
   
}
