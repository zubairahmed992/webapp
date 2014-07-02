<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    
    
   public function installAction()
    {
        $app_specs= $this->get('shopify.helper')->appSpecs();
        $shop_domain = 'lovethatfit-2.myshopify.com';
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $app_specs['api_key'], array('read_products', 'write_content', 'write_themes', 'read_customer'));        
        return new Response($str);
        return $this->redirect($str);
    }
    #-------------------------------------->
     public function grantedAction()
    {
         $app_specs= $this->get('shopify.helper')->appSpecs();
         $specs['api_key'] = $app_specs['api_key'];
         $specs['shared_secret'] = $app_specs['shared_secret'];
         
         # to serve the shopify server app install request :--------------------
         #$specs['temp_code'] = $this->getRequest()->query->get('code');          
         #$specs['shop_domain'] = $this->getRequest()->query->get('shop');                 
         #$specs['access_token'] = \sandeepshetty\shopify_api\oauth_access_token($specs['shop_domain'], $specs['api_key'], $specs['shared_secret'], $specs['temp_code']);
         #~~~~~~>testing
         $specs['temp_code'] = 'davinci_code';          
         $specs['shop_domain'] = 'lovethatfit-2.myshopify.com';
         $specs['access_token'] = 'hush hush';         
         #----------------------------------------------------------------------
         # update in ltf database
         $this->get('admin.helper.retailer')->updateRetailShopSpecs($specs);
         return new Response(json_encode($specs));         
     }
    #-------------------------------------->
     public function syncProductsAction()
    {
        $app_specs= $this->get('shopify.helper')->appSpecs();        
        $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';        
        $shop_domain = 'lovethatfit-2.myshopify.com';        
        $shopify = \sandeepshetty\shopify_api\client($shop_domain, $access_token, $app_specs['api_key'], $app_specs['shared_secret']);
        $products = $shopify('GET', '/admin/products.json');
        return new Response($this->foo($products));
     }
    #---------------------------------------------------------------  
     
      private function foo($product_json) {
        #return $product_json;
        return $product_json;
        $products = json_decode($product_json, true);
        $str = '';
        foreach ($products['products'] as $p => $key) {
            $str = $str . ' __________ ' . $key['title'];
            foreach ($products['products'] as $pk => $pv) {
                $str = $str . '  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ';
                $str = $str . '  Title:' . $pv['title'];
                $str = $str . '  Id:' . $pv['id'];
                $str = $str . ',  variants: ';
                foreach ($pv['variants'] as $vk => $vv) {
                    $str = $str . $vv['option2'] . ' ' . $vv['option3'] . ' ' . $vv['option1'];
                    $str = $str . ' id:' . $vv['id'] . ' sku:' . $vv['sku'];
                }
            }

            return $str;
        }
    }
#---------------------------------------------------------------
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
    #--------------------------------------------------------------- 
     public function shopifySimulatorAction()
    {
        $latest = $this->get('admin.helper.product')->listByType(array('limit'=>5, 'list_type'=>'latest'));
         return $this->render('LoveThatFitShopifyBundle:Default:shopify_simulator.html.twig', array('products'=> $latest));
    }
    #---------------------------------------------------------------
       public function fittingRoomShowAction($product_id=0)
    {
        #$product=$product_id!=0?$this->get('admin.helper.product')->find($product_id):null;
         $product=$this->get('admin.helper.product')->find($product_id);
         return $this->render('LoveThatFitShopifyBundle:Default:fitting_room.html.twig', array('product'=>$product));
    }
 // User Sku ---------------------
    public function userCheckAction(Request $request,$user_id,$sku){ 
       
        $data = $request->request->all();
       // $user_id=$data['user_id'];
        //$sku=$data['sku'];
        $site_user=$this->get('admin.helper.retailer.site.user')->findByReferenceId($user_id);
      //  return new response(var_dump($site_user[0]->getUserReferenceId()));
        if (!$site_user){
          ;
            $retailer = $this->get('admin.helper.retailer')->find(1);
            $this->setNewUserSession($user_id, $retailer->getId(), $sku);
            #$user = $this->get('user.helper.user')->find(53);            
            #$site_user=$this->get('admin.helper.retailer.site.user')->addNew($retailer, $user, $user_id);
            return $this->redirect($this->generateUrl('external_login'), 301);             
        }else{
           
            $itemBySku=$this->get('admin.helper.productitem')->findItemBySku($sku);
            # render fitting room with alerts
            return new Response(var_dump($site_user));
        }
        
       
       
        
    }
    //-----------------------------------------
    public function setNewUserSession($site_user_id,$retailer_id, $sku){
         $session = $this->get("session");    
         $session->set('shopify_user', array('site_user_id'=>$site_user_id,
                    'retailer_id'=>$retailer_id, 
                    'sku'=>$sku));         
    }
    
    
}
