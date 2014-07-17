<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller {

    public function installAction() {
        $app_specs = $this->get('shopify.helper')->appSpecs();
        $shop_domain = 'lovethatfit-2.myshopify.com';
        $str = \sandeepshetty\shopify_api\permission_url($shop_domain, $app_specs['api_key'], array('read_products', 'write_content', 'write_themes', 'read_customer'));
        return new Response($str);
        return $this->redirect($str);
    }

    #-------------------------------------->

    public function grantedAction() {
        $app_specs = $this->get('shopify.helper')->appSpecs();
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

    public function fooAction() {
        #$resp=json_encode($this->getShopProducts());
        $content = trim(preg_replace('/\s\s+/', '\n ', $this->getContent()));
        $resp=json_encode($this->writeFile('snippets/foo.liquid', $content));
        return new Response($resp);
    }

    #--------------------------------------------------------------- 

    public function shopifySimulatorAction() {
        $latest = $this->get('admin.helper.product')->listByType(array('limit' => 5, 'list_type' => 'latest'));
        return $this->render('LoveThatFitShopifyBundle:Default:shopify_simulator.html.twig', array('products' => $latest));
    }

    #--------------------------------------------------------------- 

    public function userCheckAction(Request $request, $user_id, $sku) {
        if ($user_id == null) {
            return $this->redirect($this->generateUrl('external_login'), 301);
        }

        $site_user = $this->get('admin.helper.retailer.site.user')->findByReferenceId($user_id);

        if (is_object($site_user)) {
            $itemBySku = $this->get('admin.helper.productitem')->findItemBySku($sku);
            if ($itemBySku == null || empty($itemBySku)) {
                return new response('Unable to find product ');
            }
            return $this->redirect($this->generateUrl('inner_shopify_index', array('sku' => $sku, 'user_id' => $site_user->getId())), 301);
        } else {
            //$retailer = $this->get('admin.helper.retailer')->find(1);
            $this->setNewUserSession($user_id, $sku);
            return $this->redirect($this->generateUrl('external_login'), 301);
        }
    }

    //-----------------------------------------
    public function setNewUserSession($site_user_id, $sku) {
        $session = $this->get("session");
        $session->set('shopify_user', array('site_user_id' => $site_user_id,
            'sku' => $sku));
    }

    // ------------------Create HTML File ----------------------------
    /*
      $stringData = "<p>whatever you want inside the html file</p>";
      $fullPath =  "fileName.html";
      $response = new Response();
      $response->headers->set('Content-Type', 'text/csv');
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$fullPath);
      $response->setContent($stringData);

     * return $response;
     */



    #~~~~~~~~~~~~~~~~~~~~~~ PRIVATES ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>

    private function getShopMainTheme() {
        $themes = $this->getShopThemes();
        $main_theme = null;
        foreach ($themes as $t) {
            $main_theme = $t['role'] == 'main' ? $t : $main_theme;
        }
        return $main_theme;
    }

    #--------------------------------------->

    private function getShopThemes() {
        $shopify = $this->getShopifyObject();
        $themes = $shopify('GET', '/admin/themes.json');
        return $themes;
    }

    #--------------------------------------->

    private function getShopProducts() {
        $shopify = $this->getShopifyObject();
        $themes = $shopify('GET', '/admin/products.json');
        return $themes;
    }

    #--------------------------------------->

    private function getShopifyObject() {
        $app_specs = $this->get('shopify.helper')->appSpecs();
        $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';
        $shop_domain = 'lovethatfit-2.myshopify.com';
        $shopify = \sandeepshetty\shopify_api\client($shop_domain, $access_token, $app_specs['api_key'], $app_specs['shared_secret']);
        return $shopify;
    }

    #-------------------------------------->

    private function writeFile($full_name, $content) {
        $main_theme = $this->getShopMainTheme();
        $shopify = $this->getShopifyObject();

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

#-----------------------------------------------------------------------

    private function getContent() {

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
 var url= 'shopify/user_check'+'/'+user_id+'/'+curnt_sku;
 window.frames['ext_fitting_room'].location = 'http://26830d5c.ngrok.com/webapp/web/app_dev.php/'+url;
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
</script>
";
    }

}
