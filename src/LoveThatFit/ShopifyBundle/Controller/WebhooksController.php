<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WebhooksController extends Controller {

        public function orderCreateCallbackAction(Request $request) {
        // return new response(json_encode($this->get('shopify.helper')->parseOrderJson(1)));
        
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
          //  $json_data=json_decode('{"buyer_accepts_marketing":true,"cancel_reason":null,"cancelled_at":null,"cart_token":"f5bac9381ec1d36fe606d15a9134468b","checkout_token":"6a758d6338d89204c34adedf5489ffca","closed_at":null,"confirmed":true,"created_at":"2014-08-25T12:43:44-04:00","currency":"CAD","email":"skamrani2002@gmail.com","financial_status":"authorized","fulfillment_status":null,"gateway":"bogus","id":265782647,"landing_site":"\/collections\/all","location_id":null,"name":"#1004","note":"","number":4,"reference":null,"referring_site":"http:\/\/lovethatfit-2.myshopify.com\/","source_identifier":null,"source_name":"web","source_url":null,"subtotal_price":"45.00","taxes_included":false,"test":true,"token":"2d826409a22cc19e7c41915e1b0e7d49","total_discounts":"0.00","total_line_items_price":"45.00","total_price":"63.00","total_price_usd":"57.43","total_tax":"0.00","total_weight":0,"updated_at":"2014-08-25T12:43:45-04:00","user_id":null,"browser_ip":"115.186.127.162","landing_site_ref":null,"order_number":1004,"discount_codes":[],"note_attributes":[],"processing_method":"direct","source":"browser","checkout_id":328964611,"tax_lines":[],"tags":"","line_items":[{"fulfillment_service":"manual","fulfillment_status":null,"gift_card":false,"grams":0,"id":474194791,"price":"45.00","product_id":305410983,"quantity":1,"requires_shipping":true,"sku":"22322","taxable":true,"title":"Horizon Modern Capri Back Yoke","variant_id":711927743,"variant_title":"12 \/ black \/ Regular","vendor":"GAP","name":"Horizon Modern Capri Back Yoke - 12 \/ black \/ Regular","variant_inventory_management":"","properties":[],"product_exists":true,"fulfillable_quantity":1,"tax_lines":[]}],"shipping_lines":[{"code":"International Shipping","price":"18.00","source":"shopify","title":"International Shipping","tax_lines":[]}],"billing_address":{"address1":"243 ghh","address2":"This is net address","city":"Karachi","company":"","country":"Pakistan","first_name":"skamrani","last_name":"suresh","latitude":24.92671,"longitude":67.03437,"phone":"","province":"Sindh","zip":"12332","name":"skamrani suresh","country_code":"PK","province_code":null},"shipping_address":{"address1":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","address2":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","city":"Karachi","company":"Centric Source ","country":"United States","first_name":"skamrani","last_name":"suresh","latitude":40.09639,"longitude":-75.912857,"phone":"610-273-7400","province":"Florida","zip":"32003","name":"skamrani suresh","country_code":"US","province_code":"FL"},"fulfillments":[],"client_details":{"accept_language":"en-US,en;q=0.8","browser_height":null,"browser_ip":"115.186.127.162","browser_width":null,"session_hash":"aacafee69515662461de82aad6bbcaee1788ce398d49035c75f671d1baf0d1bb","user_agent":"Mozilla\/5.0 (Windows NT 6.1; WOW64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/36.0.1985.143 Safari\/537.36"},"refunds":[],"payment_details":{"avs_result_code":null,"credit_card_bin":"1","cvv_result_code":null,"credit_card_number":"\u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 1","credit_card_company":"Bogus"},"customer":{"accepts_marketing":true,"created_at":"2014-07-08T07:02:14-04:00","email":"skamrani2002@gmail.com","first_name":"Suresh","id":240184227,"last_name":"Kumar","last_order_id":null,"multipass_identifier":null,"note":null,"orders_count":0,"state":"enabled","total_spent":"0.00","updated_at":"2014-08-25T12:43:45-04:00","verified_email":true,"tags":"","last_order_name":null,"default_address":{"address1":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","address2":"Honey Brook Chiropractic  2549 Conestoga Ave. Honey Brook, PA 19344","city":"Karachi","company":"Centric Source ","country":"United States","first_name":"skamrani","id":368779595,"last_name":"suresh","phone":"610-273-7400","province":"Florida","zip":"32003","name":"skamrani suresh","province_code":"FL","country_code":"US","country_name":"United States","default":true}}}',true);
          $str_response =  $this->get('retailer.order.track.helper')->saveValues(json_encode($decoded));
            #$this->get('site.helper.usertryitemhistory')->updateJSON(2, json_encode($decoded));
            return new Response($str_response);
        }
        #----------------------------------------------------------
        public function cartCreateCallbackAction(Request $request) {
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
         $this->get('site.helper.usertryitemhistory')->updateJSON(2, json_encode($decoded));
         return new Response(json_encode($request));
        }
        #---------------------------------------------------------------
        public function cartUpdateCallbackAction(Request $request) {
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
         $this->get('site.helper.usertryitemhistory')->updateJSON(4, json_encode($decoded));
         return new Response(json_encode($request));
        }
        
          #-----------------------------------------------------------
        public function orderUpdatedCallbackAction(Request $request){
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $this->get('site.helper.usertryitemhistory')->updateJSON(3, json_encode($decoded));
        return new Response(json_encode($request));
        }
        #--------------------------------------------------------------
        public function orderPaidCallbackAction(Request $request){
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $this->get('site.helper.usertryitemhistory')->updateJSON(4, json_encode($decoded));
        return new Response(json_encode($request));
        }
        #----------------------------------------------------------------
        public function orderFulfilledCallbackAction(Request $request){
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $this->get('site.helper.usertryitemhistory')->updateJSON(5, json_encode($decoded));
        return new Response(json_encode($request));
        }
        #----------------------------------------------------------------
        public function orderCancelledCallbackAction(Request $request){
        $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        $this->get('site.helper.usertryitemhistory')->updateJSON(6, json_encode($decoded));
        return new Response(json_encode($request));
        }

        
        #----------------------------------------------------------
        public function listAllAction($retailer_id) {
            $webhooks=$this->get('shopify.helper')->getRetailerWebhooks($retailer_id);
            return new response(json_encode($webhooks));         
        }

         #----------------------------------------------------------
        public function createAllAction($retailer_id) {
            $webhooks=$this->get('shopify.helper')->createRetailerWebhooks($retailer_id);
            return new response(json_encode($webhooks));         
        }
       
}
