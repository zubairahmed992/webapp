<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use LoveThatFit\UserBundle\Entity\User;

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
        #return new Response(json_encode($shopify('GET', '/admin/themes.json')));
        
        
        $contents="<style>
        .full_screen {

	text-align:center;

	width:100%;

	height:100%;

	position:fixed;

	left:0;

	top:0;

	z-index:80000;

    background:url({{ 'trans_dot.png' | asset_url }}) 0 0 repeat;
    
    
	display:none;
    

}

.full_screen .inner_div {

	width:814px;

	height:584px;

	margin:0 auto;

	margin-top:20px;
	
	overflow:hidden;

}

.full_screen img {

	float:left;

}

.full_screen a.close_me,  .full_screen a.close_me:visited {

    /*background:url(close.png) 0 0 no-repeat;*/

	width:32px;

	height:32px;

	float:right;

	display:block;
    margin-right:8px;

}
</style>




<div id='ajax_request'>
<!--Login code--
<input type=‘button’ onclick=‘loadXMLDoc()’ value=‘go’></input> 
<input type=‘button’ onclick=‘loadhtmliniframe()’ value=‘FR’></input> 

-->
{% unless customer %}
    {% if template contains 'customers' %}
        {% assign send_to_login = false %}
    {% else %}
        {% assign send_to_login = true %}
    {% endif %}
{% endunless %}

{% if send_to_login %}
<meta content=‘0; url=/account/login?checkout_url={{ shop.url }}’ http-equiv=‘refresh’ />
{% else %} 
<input type=‘button’ onclick=‘open_me()’ value=‘Fitting Room’></input> 
{% endif %}



<div class=‘full_screen’>
  	<div class=‘inner_div’>
       	<a class=‘close_me’ href=‘#’>Close</a>
        
      <div id='fitting_room'>        
        <iframe  style=‘border: 6px inset #ccc; height:568px; background:#fff;’ id='ext_fitting_room' width=‘740’ heigth=‘568’ name='ext_fitting_room' marginheight=‘0’ marginwidth=‘0’ scrolling=‘yes’></iframe>
      </div>
      
    </div>
</div>




    
    
<script type=‘text/javascript’>
  
 
	function open_me() {
		jQuery('.full_screen').fadeIn();
      loadfittingroom();
	}
function close_me() {
		jQuery('.full_screen').fadeOut();
	}
jQuery('.full_screen .close_me').click(function (){
		  close_me();
	  });

 function loadfittingroom(){
  
   //  $(‘#fitting_room’).toggle();
   //$('.full_screen').fadeIn(500);
     loadhtmliniframe();
 }

  function loadhtmliniframe() {
    var json_product ={{ product | json }}
  	var product_select=$('#product-select').val();
 	var curnt_sku=$('#sku'+product_select).text();
    var user_id={{ customer.id | json }}
   
    var url= 'shopify/user_check'+'/'+user_id+'/'+curnt_sku;
  
    window.frames['ext_fitting_room'].location = 'http://e003298.ngrok.com/webapp/web/app_dev.php/'+url;
  }
  
  
  function loadXMLDoc() {
    
var xmlhttp;
  var url='/apps/fitting_room';

    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject(‘Microsoft.XMLHTTP’);
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 ) {
           if(xmlhttp.status == 200){
             document.getElementById(‘fitting_room’).innerHTML = xmlhttp.responseText;
           }
           else if(xmlhttp.status == 400) {
              alert('There was an error 400')
           }
           else {
               alert('something else other than 200 was returned')
           }
        }
    }

    xmlhttp.open(‘GET’, url, true);
    xmlhttp.send();
}

    </script>
";
        $response=$this->shopifyAPI_assets($shopify, '8753791', $contents, 'formulaone.liquid');
        return new Response($response);
        $products = $shopify('GET', '/admin/products.json');
        return new Response($this->foo($products));
     }
     
     private function shopifyAPI_assets($shopify,$id,$contents,$name){
    try{
      $request = array(
          "asset" => array(
            "key" => "snippets/".$name,
            "value" => $contents
          )
      );
      $response = $shopify("PUT","/admin/themes/{$id}/assets.json",$request);
      return var_dump($response);
    }catch (ShopifyApiException $e){
      return $e;
    }
  }
    #---------------------------------------------------------------  
     
      private function foo($product_json) {
        #return $product_json;
        return json_encode($product_json, true);
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
     // $user_id=$data['user_id'];
      #return new Response('$user_id='.$user_id.'  $sku='.$sku);
       if($user_id==null){
           return $this->redirect($this->generateUrl('external_login'), 301);             
       } 
        
       $site_user=$this->get('admin.helper.retailer.site.user')->findByReferenceId($user_id);
       
       if (is_object($site_user)){
           
            $itemBySku=$this->get('admin.helper.productitem')->findItemBySku($sku);
            if($itemBySku==null || empty($itemBySku)){
               return new response('Unable to find product ');
            }
            #return new response($itemBySku->getProduct()->getName());
            return $this->redirect($this->generateUrl('inner_shopify_index',array('sku'=>$sku,'user_id'=>$site_user->getId())), 301);             
           
       
          
        }else{
            //$retailer = $this->get('admin.helper.retailer')->find(1);
            $this->setNewUserSession($user_id, $sku);
           return $this->redirect($this->generateUrl('external_login'), 301); 
            
        }
        
       
       
        
    }
    //-----------------------------------------
    public function setNewUserSession($site_user_id,$sku){
         $session = $this->get("session");    
         $session->set('shopify_user', array('site_user_id'=>$site_user_id,
                    'sku'=>$sku));         
    }
  //------------------------------------------------------------
    public function getFittingAlertAction(){
        $sku=5;
        $user_id=117;
        $user = $this->get('user.helper.user')->find($user_id);
        $productItem = $this->get('admin.helper.productitem')->findItemBySku($sku);
       // return new response(json_encode($productItem->getId()));
        
        if (!$productItem) return new Response("Product not found!");
        
        $product_size = $productItem->getProductSize();
        //return new response(json_encode($product_size->getId()));
        $product=$productItem->getProduct();
        $comp = new AvgAlgorithm($user,$product);
        $fb=$comp->getSizeFeedBack($product_size);
        $fits=$fb['feedback']['fits'];        
        $json_feedback=  json_encode($fb['feedback']);
        //$this->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user,$product->getId(), $productItem, $json_feedback, $fits);    

        return $this->render('LoveThatFitShopifyBundle:Default:_fitting_feedback.html.twig', 
                array('product' => $productItem->getProduct(), 
                        'product_item' => $productItem, 
                            'data' => $fb));
    }
    
    // ------------------Create HTML File ----------------------------
    public function createHTMLDocAction(){
        
        $stringData = "<p>whatever you want inside the html file</p>";
        $fullPath =  "fileName.html";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fullPath);
        $response->setContent($stringData);
        return $response;
        exit(); 
    }
    
    
}
