<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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


class DefaultController extends Controller {

    
    public function installAction(){
         return $this->render('LoveThatFitShopifyBundle:Default:install_form.html.twig');
    }
    
    public function createAction() {
       
        //$shop_domain = 'lovethatfit-2.myshopify.com';
        #$str = \sandeepshetty\shopify_api\permission_url($shop_domain, $app_specs['api_key'], array('read_products','write_themes','write_content','read_customers','read_orders'));
       // $str = \sandeepshetty\shopify_api\permission_url($_POST['shop_domain'], $app_specs['api_key'], $app_specs['app_scopes']);
        $str=$this->get('shopify.helper')->install($_POST['shop_domain']);
        return $this->redirect($str);
    }

    #-------------------------------------->

    public function grantedAction() {
        $specs['temp_code'] = $this->getRequest()->query->get('code');          
        $specs['shop_domain'] = $this->getRequest()->query->get('shop'); 
       return new response( $this->get('shopify.helper')->granted($specs));
    }

#-------------------------------------->

    public function fooAction($option_array=null) {
        
        return new response(json_encode($this->get('shopify.helper')->getRetailerProducts(3)));
        #return new response(json_encode($this->get('shopify.helper')->getArrayCustomerCount(3)));
        $option_array['access_token']='8b14eb6efcf7c5fa7b0c76e9b329d06e';
        $content = trim(preg_replace('/\s\s+/', '\n ', $this->getContent($option_array)));
        $resp=json_encode($this->writeFile('snippets/foo.liquid', $content));
        return new Response($resp);
    }

    #------------------------------------------------------------------------#
    public function webHookCallAction($base_url=null){
        //return new response(json_encode($base_url));
        return new response(json_encode($this->defineAllWebHooks($base_url)));
    }
    public function getCartAction(){
        
    }
    public function getWebHooksListAction(){
         $shopify = $this->getShopifyObject();
         $response = $shopify("GET", "/admin/webhooks.json");
         return new response(json_encode($response));
    }
    public function deleteWebHookAction($id){
        $shopify = $this->getShopifyObject();
        $response = $shopify("DELETE", "/admin/webhooks/$id.json");
         return new response("Delete");
    }
     #-------------------------------------->
   
    private function getShopifyObject(){
       $specs['shop_domain']='lovethatfit-2.myshopify.com';
       $specs['access_token']='fc2d5efc0b57962219093084ba4c80fd'; 
       return $this->get('shopify.helper')->getShopifyObject($specs); 
    }
    private function defineAllWebHooks($base_url){
        
      $app_specs = $this->get('shopify.helper')->appSpecs();
      $shopify = $this->getShopifyObject();//$this->get('shopify.helper')->getShopifyObject($specs);  
      $response_array=array();
      #complete base url
      //$base_url=$this->getRequest()->getSchemeAndHttpHost().$this->getRequest()->getBaseURL();
     $base_url='http://24474d38.ngrok.com/webapp/web/app_dev.php';
      foreach($app_specs['webhooks'] as $k=>$v){
        $response_array[$k] = $this->defineWebHook($shopify, $base_url.$v['address'], $v['topic']);
      #$response_array[$k]=$v['address'].'   @  '. $v['topic'];
          
      } 
      return $response_array;
      
    }
    
    private function defineWebHook($shopify, $address, $topic) {
       
        try {
            $request = array(
                "webhook" => array(
                    "topic" => $topic,
                    "address" => $address,
                    "format" => "json",
                )
            );
            $response = $shopify("POST", "/admin/webhooks.json", $request);
            return $response;
        } catch (ShopifyApiException $e) {
            return $e;
        }
    }
#---------------------------------Check Sku ---------------------------#
public function checkSkuAction($sku=null){
   
 if($this->get('admin.helper.retailer')->findOneByShopDomain($_REQUEST['shop'])){
  $itemBySku = $this->get('admin.helper.productitem')->findItemBySku($sku);
    if ($itemBySku == null || empty($itemBySku)) {
       return new response("no");
    }   else{
        return new response("Available");
    }
    }else{
        return new response("no");
        
    }
}
    



}
