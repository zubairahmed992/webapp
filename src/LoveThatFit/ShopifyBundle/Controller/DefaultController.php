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

    public function installAction() {
        $app_specs = $this->get('shopify.helper')->appSpecs();
        $shop_domain = 'lovethatfit-2.myshopify.com';
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $app_specs['api_key'], array('read_products','write_themes','write_content','read_customers','read_orders'));
        return $this->redirect($str);
    }

    #-------------------------------------->

    public function grantedAction() {
       
        $app_specs = $this->get('shopify.helper')->appSpecs();
        $specs['api_key'] = $app_specs['api_key'];
        $specs['shared_secret'] = $app_specs['shared_secret'];

        # to serve the shopify server app install request :--------------------
        $specs['temp_code'] = $this->getRequest()->query->get('code');          
        $specs['shop_domain'] = $this->getRequest()->query->get('shop');                 
        $specs['access_token'] = \sandeepshetty\shopify_api\oauth_access_token($specs['shop_domain'], $specs['api_key'], $specs['shared_secret'], $specs['temp_code']);
        $specs['shop_type'] = 'shopify';          
       
#~~~~~~>testing
      // $specs['temp_code'] = '0de3b3fb61b001da821e81883b453537';
      //  $specs['shop_domain'] = 'bicycles-122.myshopify.com';
      // $specs['access_token'] = '3874b59ffd8c7c42bc18f28cc04d3e71';
        #----------------------------------------------------------------------
        # update in ltf database
        
        if($this->get('admin.helper.retailer')->updateRetailShopSpecs($specs)){
            
            $shopify = \sandeepshetty\shopify_api\client($specs['shop_domain'], $specs['access_token'], $specs['api_key'], $specs['shared_secret']);
            $content = trim(preg_replace('/\s\s+/', '\n ', $this->getContent($specs)));
            $resp=json_encode($this->writeFile('snippets/foo1.liquid', $content,$shopify));
           // return new Response($resp);
             return new Response("<html><body>Congratulation! The LTF app has been successfully installed at your store .
             <br>
             <a href=http://".$specs['shop_domain']." >Click here </a>
            </body></html>");
          
        }else{
            return new Response("Some thing went wrong!");
        }
        
    }

#-------------------------------------->

    public function fooAction($option_array=null) {
        $option_array['access_token']='8b14eb6efcf7c5fa7b0c76e9b329d06e';
        $content = trim(preg_replace('/\s\s+/', '\n ', $this->getContent($option_array)));
        $resp=json_encode($this->writeFile('snippets/foo.liquid', $content));
        return new Response($resp);
    }

    #~~~~~~~~~~~~~~~~~~~~~~ PRIVATES ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>

    private function getShopMainTheme($shopify) {
        $themes = $this->getShopThemes($shopify);
        
       if(is_array( $themes)){
            $is_arr=$themes;
           
        }else{
            $th_array=json_decode($themes, true);
            $is_arr=$th_array['themes'];
        }
        $main_theme = null;
        foreach ($is_arr as $t) {
            $main_theme = $t['role'] == 'main' ? $t : $main_theme;
        }
        
        
        return $main_theme;
    }

    #--------------------------------------->

    private function getShopThemes($shopify) {
      //$shopify = $this->getShopifyObject($shop_specs);
        $themes = $shopify('GET', '/admin/themes.json');
        return $themes;
    }

    #--------------------------------------->

    private function getShopProducts($shopify) {
      //  $shopify = $this->getShopifyObject();
        $themes = $shopify('GET', '/admin/products.json');
        return $themes;
    }
    
    #--------------------------------------------->
    public function getCustomerListAction(){
        $shopify=$this->get('shopifylib.helper')->getShopifyObject();
        $customerOrders = $shopify('GET','/admin/customers/240179475.json');
        return new response(json_encode($customerOrders));  
    }
 
    #-------------------------------------->
    private function writeFile($full_name, $content,$shopify) {
       
      $main_theme=$this->getShopMainTheme($shopify);
      //$shopify = $this->getShopifyObject();

        try {
            $request = array(
                "asset" => array(
                    "key" => $full_name,
                    "value" => $content,
                )
            );
            $response = $shopify("PUT", "/admin/themes/{$main_theme['id']}/assets.json", $request);
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
    

#-----------------------------------------------------------------------
  private function getContent($option_array=null) {
       return "<style>
.full_screen {text-align:center;width:100%;height:100%;position:fixed;left:0;top:0;z-index:80000; background:url({{ 'trans_dot.png' | asset_url }}) 0 0 repeat;display:none;}
.full_screen .inner_div {width:814px;height:584px;margin:0 auto;margin-top:36px;overflow:hidden;}
.full_screen img {float:left;}
.full_screen a.close_me,  .full_screen a.close_me:visited {width:32px;height:32px;float:right;display:block;margin-right:8px;}
#fr_button {border:1px solid #666;}
</style>
<div id='ajax_request'>
{% unless customer %}
    {% if template contains 'customers' %}
        {% assign send_to_login = false %}
    {% else %}
        {% assign send_to_login = true %}
    {% endif %}
{% endunless %}

{% if send_to_login %}
<meta content='0; url=/account/login?checkout_url={{ shop.url }}' http-equiv='refresh' />
{% else %} 
<input id='fr_button' type='button' onclick='open_me()' value='Fitting Room'></input> 
{% endif %}
<div class='full_screen'>
<div class='inner_div'>
<a class='close_me' href='#'>Close</a>
<div id='fitting_room'>        
<iframe  style='border: 6px inset #ccc; height:568px; background:#fff;' id='ext_fitting_room' width='820' heigth='568' name='ext_fitting_room' marginheight='0' marginwidth='0' scrolling='yes'></iframe>
</div>
</div>
</div>
<script type='text/javascript'>
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
     loadhtmliniframe();
 }
  function loadhtmliniframe() {
 var json_product ={{ product | json }}
var product_select=$('#product-select').val();
 var curnt_sku=$('#sku'+product_select).text();
 var user_id={{ customer.id | json }}
 var access_token='".$option_array['access_token']."';
 var url= 'shopify/user_check/'+access_token+'/'+user_id+'/'+curnt_sku;
 window.frames['ext_fitting_room'].location = 'http://474ec863.ngrok.com/webapp/web/app_dev.php/'+url;
  }
  function loadXMLDoc() {
  var xmlhttp;
  var url='/apps/fitting_room';

    if (window.XMLHttpRequest) {
      
        xmlhttp = new XMLHttpRequest();
    } else {
       
        xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 ) {
           if(xmlhttp.status == 200){
             document.getElementById('fitting_room').innerHTML = xmlhttp.responseText;
           }
           else if(xmlhttp.status == 400) {
              alert('There was an error 400')
           }
           else {
               alert('something else other than 200 was returned')
           }
        }
    }

    xmlhttp.open('GET', url, true);
    xmlhttp.send();
}

$( document ).ready(function() {
      var product_select=$('#product-select').val();
  var curnt_sku=$('#sku'+product_select).text();
 var xmlhttp;
    var url='/apps/fitting_room/'+curnt_sku;
 if (window.XMLHttpRequest) {
 xmlhttp = new XMLHttpRequest();
 } else {
 xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
 }
 xmlhttp.onreadystatechange = function() {
 if (xmlhttp.readyState == 4 ) {
 if(xmlhttp.status == 200){
   if(xmlhttp.responseText=='no'){
   	$('#fr_button').hide();
   }
 }
 else if(xmlhttp.status == 400) {
 alert('There was an error 400')
 }
 else {
 alert('something else other than 200 was returned')
 }
 }
 }
 xmlhttp.open('GET', url, true);
 xmlhttp.send();
   
});
</script>
";
    }

}
