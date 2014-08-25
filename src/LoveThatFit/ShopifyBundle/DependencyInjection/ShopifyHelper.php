<?php

namespace LoveThatFit\ShopifyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;

class ShopifyHelper {

    protected $conf;
    private $container;

    //--------------------------------------------------------------------
    public function __construct(Container $container) {
        $this->container = $container;
    }

    //--------------------------------------------------------------------
    public function appSpecs() {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/ShopifyBundle/Resources/config/shopify_app.yml'));
    }

    //--------------------------------------------------------------------
    public function appScopes() {
        $specs = $this->appSpecs();
        return $specs['app_scopes'];
    }

    //--------------------------------------------------------------------
    public function appWebHooks() {
        $specs = $this->appSpecs();
        return $specs['webhooks'];
    }

    #------------Shopify App installation ----------------------------------------#

    public function install($shop_domain) {
        $app_specs = $this->appSpecs();
        return permission_url($shop_domain, $app_specs['api_key'], array('read_products', 'write_themes', 'write_content', 'read_customers', 'read_orders'));
    }

    #--------------------------------------->

    public function granted($specs) {
        $app_specs = $this->appSpecs();
        $specs['api_key'] = $app_specs['api_key'];
        $specs['shared_secret'] = $app_specs['shared_secret'];
        $specs['access_token'] = oauth_access_token($specs['shop_domain'], $specs['api_key'], $specs['shared_secret'], $specs['temp_code']);
        $specs['shop_type'] = 'shopify';
        if ($this->container->get('admin.helper.retailer')->updateRetailShopSpecs($specs)) {
            $shopify = client($specs['shop_domain'], $specs['access_token'], $specs['api_key'], $specs['shared_secret']);
            $content = trim(preg_replace('/\s\s+/', '\n ', $this->getContent($specs)));
            $resp = json_encode($this->writeFile('snippets/foo2.liquid', $content, $shopify));
            return ("<html><body>Congratulation! The LTF app has been successfully installed at your store .
             <br>
             <a href=http://" . $specs['shop_domain'] . " >Click here </a>
            </body></html>");
        } else {
            return ("Some thing went wrong!");
        }
    }

       #-------------------------------------->    

    public function writeFile($full_name, $content, $shopify) {

        $main_theme = $this->getShopMainTheme($shopify);
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

    public function checkSkuAction($sku = null) {

        if ($this->get('admin.helper.retailer')->findOneByShopDomain($_REQUEST['shop'])) {
            $itemBySku = $this->get('admin.helper.productitem')->findItemBySku($sku);
            if ($itemBySku == null || empty($itemBySku)) {
                return new response("no");
            } else {
                return new response("Available");
            }
        } else {
            return new response("no");
        }
    }

     
    #--------------------------------------->
    public function getRetailerShopifySpecsArray($id) {
        $retailer = $this->container->get('admin.helper.retailer')->find($id);
        $app_specs = $this->appSpecs();
        $specs['shop_domain'] = $retailer->getShopDomain();
        $specs['access_token'] = $retailer->getAccessToken();
        $specs['api_key'] = $app_specs['api_key'];
        $specs['shared_secret'] = $app_specs['shared_secret'];
        return $specs;
    }
    
  #-------------------------------------------------------  
    private function getShopMainTheme($shopify) {
        $themes = $this->getShopThemes($shopify);
        if (is_array($themes)) {
            $is_arr = $themes;
        } else {
            $th_array = json_decode($themes, true);
            $is_arr = $th_array['themes'];
        }
        $main_theme = null;
        foreach ($is_arr as $t) {
            $main_theme = $t['role'] == 'main' ? $t : $main_theme;
        }
        return $main_theme;
    }
#-----------------------------------------------------------------------
    public function getContent($option_array = null) {
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
{% if customer %} 
<input id='fr_button' type='button' onclick='open_me()' value='Fitting Room'></input> 
{% endif %}
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
 var access_token='" . $option_array['access_token'] . "';
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
    #---------------------------Customer ---------------------------------------#
    public function getShopifyObject($specs = null) {
        $app_specs = $this->appSpecs();
        $specs['api_key'] = $app_specs['api_key'];
        $specs['shared_secret'] = $app_specs['shared_secret'];
        //   $specs['shop_domain']='lovethatfit-2.myshopify.com';
        // $specs['access_token']='fc2d5efc0b57962219093084ba4c80fd';
        return $shopify = client($specs['shop_domain'], $specs['access_token'], $specs['api_key'], $specs['shared_secret']);
    }
    #--------------------------------------------->
    public function getArrayCustomerCount($ret_id = null) {
        if (!$ret_id) {
            return array('status' => FALSE,
                'msg' => 'Reatiler id not found');
        }
        $retailer = $this->container->get('admin.helper.retailer')->find($ret_id);


        if ($retailer) {

            $specs['shop_domain'] = $retailer->getShopDomain();
            $specs['access_token'] = $retailer->getAccessToken();
            $customer_count = $this->getCustomerCount($specs);
            if ($customer_count['error']) {
                return
                        array('msg' => "Problem with app installtaion or api permission",
                            'status' => false);
            }


            return
                    array(
                        'customer_count' => $this->getCustomerCount($specs),
                        'status' => true,
                        'msg' => 'Total number of Customer we found'
            );
        } else {
            return array('status' => FALSE,
                'msg' => 'Reatiler not found');
        }
    }

    #--------------------------------------->
    public function getRetailerProducts($id) {
        $shopify = $this->getShopifyObject($this->getRetailerShopifySpecsArray($id));
        return $this->getShopProducts($shopify);
    }
    #--------------------------------------->
    public function getRetailerWebhooks($id) {
        $shopify = $this->getShopifyObject($this->getRetailerShopifySpecsArray($id));
        return $this->getWebhooks($shopify);
    }
    #--------------------------------------->
    public function getOrders($id) {
        $shopify = $this->getShopifyObject($this->getRetailerShopifySpecsArray($id));
        return $this->getShopOrders($shopify);
    }


# Shopify API Calls
  #--------------------------------------->

    public function getShopProducts($shopify = null) {

        $themes = $shopify('GET', '/admin/products.json');
        return $themes;
    }
#-----------------------------------------------------------
    public function getShopOrders($shopify = null) {
        $orders = $shopify('GET', '/admin/orders.json');
        return $orders;
    }
    #-----------------------------------------------------------
    public function getCustomerList($specs) {
        $shopify = $this->getShopifyObject($specs);
        $customerOrders = $shopify('GET', '/admin/customers/' . $specs['customer_id'] . '.json');
        return $customerOrders;
    }

    #------------------------------------------------->

    private function getCustomerCount($specs = null) {

        try {
            $shopify = $this->getShopifyObject($specs);
            $customerCount = $shopify('GET', '/admin/customers/count.json');
        } catch (Exception $e) {
            $customerCount['error'] = true;
        }

        return $customerCount;
    }

    #--------------------------------------->

    private function getShopThemes($shopify) {
        $themes = $shopify('GET', '/admin/themes.json');
        return $themes;
    }
   #----------------------------------------------------------
   private function getWebhooks($shopify) {            
        $webhooks = $shopify("GET", "/admin/webhooks.json");
        return $webhooks;         
   }
   #----------------------------------------------------------
     public function createRetailerWebHooks($id){
      $shopify = $this->getShopifyObject($this->getRetailerShopifySpecsArray($id));
      $app_specs = $this->container->get('shopify.helper')->appSpecs();      
      $response_array=array();
      #complete base url
      //$base_url=$this->getRequest()->getSchemeAndHttpHost().$this->getRequest()->getBaseURL();
      $base_url='http://24474d38.ngrok.com/webapp/web/app_dev.php';
      foreach($app_specs['webhooks'] as $k=>$v){
        $response_array[$k] = $this->createWebHook($shopify, $base_url.$v['address'], $v['topic']);
      } 
      return $response_array;      
    }
    
    private function createWebHook($shopify, $address, $topic) {       
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
    
     public function deleteRetailerWebHooks($id){
      $shopify = $this->getShopifyObject($this->getRetailerShopifySpecsArray($id));
      $response_array=array();
      
      
    }
    
 private function deleteWebHook($shopify, $hook_id) {       
        try {        
            return  $shopify("DELETE", "/admin/webhooks/#{$hook_id}.json");            
        } catch (ShopifyApiException $e) {
            return $e;
        }
    }
 
#-----------------------------Webhook data for Cart and Order parsing-------# 
   #------------Save Data in DB in table of Order ---------------------#
    public function parseOrderJson($json_data=null){
            $json_data=json_decode('{"buyer_accepts_marketing":true,"cancel_reason":null,"cancelled_at":null,"cart_token":"f5bac9381ec1d36fe606d15a9134468b","checkout_token":"6a758d6338d89204c34adedf5489ffca","closed_at":null,"confirmed":true,"created_at":"2014-08-25T12:43:44-04:00","currency":"CAD","email":"skamrani2002@gmail.com","financial_status":"authorized","fulfillment_status":null,"gateway":"bogus","id":265782647,"landing_site":"\/collections\/all","location_id":null,"name":"#1004","note":"","number":4,"reference":null,"referring_site":"http:\/\/lovethatfit-2.myshopify.com\/","source_identifier":null,"source_name":"web","source_url":null,"subtotal_price":"45.00","taxes_included":false,"test":true,"token":"2d826409a22cc19e7c41915e1b0e7d49","total_discounts":"0.00","total_line_items_price":"45.00","total_price":"63.00","total_price_usd":"57.43","total_tax":"0.00","total_weight":0,"updated_at":"2014-08-25T12:43:45-04:00","user_id":null,"browser_ip":"115.186.127.162","landing_site_ref":null,"order_number":1004,"discount_codes":[],"note_attributes":[],"processing_method":"direct","source":"browser","checkout_id":328964611,"tax_lines":[],"tags":"","line_items":[{"fulfillment_service":"manual","fulfillment_status":null,"gift_card":false,"grams":0,"id":474194791,"price":"45.00","product_id":305410983,"quantity":1,"requires_shipping":true,"sku":"22322","taxable":true,"title":"Horizon Modern Capri Back Yoke","variant_id":711927743,"variant_title":"12 \/ black \/ Regular","vendor":"GAP","name":"Horizon Modern Capri Back Yoke - 12 \/ black \/ Regular","variant_inventory_management":"","properties":[],"product_exists":true,"fulfillable_quantity":1,"tax_lines":[]}],"shipping_lines":[{"code":"International Shipping","price":"18.00","source":"shopify","title":"International Shipping","tax_lines":[]}],"billing_address":{"address1":"243 ghh","address2":"This is net address","city":"Karachi","company":"","country":"Pakistan","first_name":"skamrani","last_name":"suresh","latitude":24.92671,"longitude":67.03437,"phone":"","province":"Sindh","zip":"12332","name":"skamrani suresh","country_code":"PK","province_code":null},"shipping_address":{"address1":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","address2":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","city":"Karachi","company":"Centric Source ","country":"United States","first_name":"skamrani","last_name":"suresh","latitude":40.09639,"longitude":-75.912857,"phone":"610-273-7400","province":"Florida","zip":"32003","name":"skamrani suresh","country_code":"US","province_code":"FL"},"fulfillments":[],"client_details":{"accept_language":"en-US,en;q=0.8","browser_height":null,"browser_ip":"115.186.127.162","browser_width":null,"session_hash":"aacafee69515662461de82aad6bbcaee1788ce398d49035c75f671d1baf0d1bb","user_agent":"Mozilla\/5.0 (Windows NT 6.1; WOW64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/36.0.1985.143 Safari\/537.36"},"refunds":[],"payment_details":{"avs_result_code":null,"credit_card_bin":"1","cvv_result_code":null,"credit_card_number":"\u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 1","credit_card_company":"Bogus"},"customer":{"accepts_marketing":true,"created_at":"2014-07-08T07:02:14-04:00","email":"skamrani2002@gmail.com","first_name":"Suresh","id":240184227,"last_name":"Kumar","last_order_id":null,"multipass_identifier":null,"note":null,"orders_count":0,"state":"enabled","total_spent":"0.00","updated_at":"2014-08-25T12:43:45-04:00","verified_email":true,"tags":"","last_order_name":null,"default_address":{"address1":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","address2":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","city":"Karachi","company":"Centric Source ","country":"United States","first_name":"skamrani","id":368779595,"last_name":"suresh","phone":"610-273-7400","province":"Florida","zip":"32003","name":"skamrani suresh","province_code":"FL","country_code":"US","country_name":"United States","default":true}}}',true);
            
         //chk email from retailer 
            if($json_data['email']){
                
                
            }
                $ar[]=$json_data['email'];
         
            return $ar;
        }
        
  #---------------------------Saving Data in Cart Table -------------------#
        public function parseCartJson($json_data=null){
          
            
        }
        
}





####################################################################################
####################################################################################
####################################################################################








class WcurlException extends \Exception {
    
}

class CurlException extends \Exception {
    
}

class ApiException extends \Exception {
    
}

class Exception extends \Exception {

    protected $info;

    function __construct($info) {
        $this->info = $info;
        parent::__construct($info['response_headers']['http_status_message'], $info['response_headers']['http_status_code']);
    }

    function getInfo() {
        $this->info;
    }

}

######################################################################
#-----------App installation Api calls-------------------------------------#

function permission_url($shop, $api_key, $scope = array(), $redirect_uri = '') {
    $scope = empty($scope) ? '' : '&scope=' . implode(',', $scope);
    $redirect_uri = empty($redirect_uri) ? '' : '&redirect_uri=' . urlencode($redirect_uri);
    return "https://$shop/admin/oauth/authorize?client_id=$api_key$scope$redirect_uri";
}

#------------------------------------------------------------

function oauth_access_token($shop, $api_key, $shared_secret, $code) {
    return _api('POST', "https://$shop/admin/oauth/access_token", NULL, array('client_id' => $api_key, 'client_secret' => $shared_secret, 'code' => $code));
}

#-------------------------------------------------------------   

function client($shop, $shops_token, $api_key, $shared_secret, $private_app = false) {
    $password = $shops_token;
    $baseurl = "https://$shop/";

    return function ($method, $path, $params = array(), &$response_headers = array()) use ($baseurl, $shops_token) {
                $url = $baseurl . ltrim($path, '/');
                $query = in_array($method, array('GET', 'DELETE')) ? $params : array();
                $payload = in_array($method, array('POST', 'PUT')) ? stripslashes(json_encode($params)) : array();

                $request_headers = array();
                array_push($request_headers, "X-Shopify-Access-Token: $shops_token");
                if (in_array($method, array('POST', 'PUT')))
                    array_push($request_headers, "Content-Type: application/json; charset=utf-8");

                return _api($method, $url, $query, $payload, $request_headers, $response_headers);
            };
}

#-------------------------------------------------------

function _api($method, $url, $query = '', $payload = '', $request_headers = array(), &$response_headers = array()) {
    try {
        $response = wcurl($method, $url, $query, $payload, $request_headers, $response_headers);
    } catch (WcurlException $e) {
        throw new CurlException($e->getMessage(), $e->getCode());
    }

    $response = json_decode($response, true);
    // print_r($response);
    //  die();

    if (isset($response['errors']) or ($response_headers['http_status_code'] >= 400))
        throw new Exception(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));
    // throw new ApiException(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));

    return (is_array($response) and !empty($response)) ? array_shift($response) : $response;
}

#########################################################################
#----------------------wcurlp.hp---------------------------------------#

function wcurl($method, $url, $query = '', $payload = '', $request_headers = array(), &$response_headers = array(), $curl_opts = array()) {
    $ch = curl_init(wcurl_request_uri($url, $query));
    wcurl_setopts($ch, $method, $payload, $request_headers, $curl_opts);
    $response = curl_exec($ch);
    $curl_info = curl_getinfo($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($errno)
        throw new WcurlException($error, $errno);
    $header_size = $curl_info["header_size"];
    $msg_header = substr($response, 0, $header_size);
    $msg_body = substr($response, $header_size);
    $response_headers = wcurl_response_headers($msg_header);
    return $msg_body;
}

function wcurl_request_uri($url, $query) {
    if (empty($query))
        return $url;
    if (is_array($query))
        return "$url?" . http_build_query($query);
    else
        return "$url?$query";
}

function wcurl_setopts($ch, $method, $payload, $request_headers, $curl_opts) {
    $default_curl_opts = array
        (
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT => 'wcurl',
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSLVERSION => 3,
    );

    if ('GET' == $method) {
        $default_curl_opts[CURLOPT_HTTPGET] = true;
    } else {
        $default_curl_opts[CURLOPT_CUSTOMREQUEST] = $method;

        // Disable cURL's default 100-continue expectation
        if ('POST' == $method)
            array_push($request_headers, 'Expect:');

        if (!empty($payload)) {
            if (is_array($payload)) {
                $payload = http_build_query($payload);
                array_push($request_headers, 'Content-Type: application/x-www-form-urlencoded; charset=utf-8');
            }

            $default_curl_opts[CURLOPT_POSTFIELDS] = $payload;
        }
    }

    if (!empty($request_headers))
        $default_curl_opts[CURLOPT_HTTPHEADER] = $request_headers;

    $overriden_opts = $curl_opts + $default_curl_opts;
    foreach ($overriden_opts as $curl_opt => $value)
        curl_setopt($ch, $curl_opt, $value);
}

function wcurl_response_headers($msg_header) {

    $multiple_headers = preg_split("/\r\n\r\n|\n\n|\r\r/", trim($msg_header));
    $last_response_header_lines = array_pop($multiple_headers);
    $response_headers = array();

    $header_lines = preg_split("/\r\n|\n|\r/", $last_response_header_lines);
    list(, $response_headers['http_status_code'], $response_headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
    foreach ($header_lines as $header_line) {
        list($name, $value) = explode(':', $header_line, 2);
        $response_headers[strtolower($name)] = trim($value);
    }

    return $response_headers;
}
#-------------------------------------------------------------------------------#

?>
