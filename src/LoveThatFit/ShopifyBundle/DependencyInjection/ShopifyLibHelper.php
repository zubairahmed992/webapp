<?php

namespace LoveThatFit\ShopifyBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;


class ShopifyLibHelper {

    protected $conf;

    //--------------------------------------------------------------------
    public function __construct() {
        
    }

    public function install($app_specs) {
        return $this->permission_url($app_specs['shop_domain'], $app_specs['api_key'], array('read_products', 'write_themes', 'write_content'));
    }

    #------------Cleint.php-----------------------------------------------#

    private function permission_url($shop, $api_key, $scope = array(), $redirect_uri = '') {
        $scope = empty($scope) ? '' : '&scope=' . implode(',', $scope);
        $redirect_uri = empty($redirect_uri) ? '' : '&redirect_uri=' . urlencode($redirect_uri);
        return "https://$shop/admin/oauth/authorize?client_id=$api_key$scope$redirect_uri";
    }

    public function oauth_access_token($shop, $api_key, $shared_secret, $code) {
        return $this->_api('POST', "https://$shop/admin/oauth/access_token", NULL, array('client_id' => $api_key, 'client_secret' => $shared_secret, 'code' => $code));
    }

    public function client($shop, $shops_token, $api_key, $shared_secret, $private_app = false) {
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

                    return $this->_api($method, $url, $query, $payload, $request_headers, $response_headers);
                };
    }

#-------------------------------------------------------------------------------#

    function _api($method, $url, $query = '', $payload = '', $request_headers = array(), &$response_headers = array()) {
        try {
            $response = wcurl($method, $url, $query, $payload, $request_headers, $response_headers);
        } catch (WcurlException $e) {
            throw new CurlException($e->getMessage(), $e->getCode());
        }

        $response = json_decode($response, true);
        // print_r($response);
        ///  die();

        if (isset($response['errors']) or ($response_headers['http_status_code'] >= 400))
            throw new Exception(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));
        //  throw new ApiException(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));

        return (is_array($response) and !empty($response)) ? array_shift($response) : $response;
    }

    #----------------------------------------------------------------------------#
    #~~~~~~~~~~~~~~~~~~~~~~ PRIVATES ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>

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
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'wcurl',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CAINFO => 'd://wamp/www/shopify1/cacert.pem',
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
    
  #---------------------------Customer ---------------------------------------#
    private function getShopifyObject($specs){
        $app_specs = $this->get('shopify.helper')->appSpecs();
        $specs['api_key'] = $app_specs['api_key'];
        $specs['shared_secret'] = $app_specs['shared_secret'];
      //  $specs['shop_domain']='lovethatfit-2.myshopify.com';
       // $specs['access_token']='fc2d5efc0b57962219093084ba4c80fd';
       return $shopify =$this->client($specs['shop_domain'], $specs['access_token'], $specs['api_key'], $specs['shared_secret']);
 }
 #--------------------------------------------->
    public function getCustomerList($specs){
        $shopify=$this->getShopifyObject($specs);
        $customerOrders = $shopify('GET','/admin/customers/'.$specs['customer_id'].'.json');
        return new response(json_encode($customerOrders));  
    }
 

}

?>
