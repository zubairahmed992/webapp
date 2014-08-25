<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WebhooksController extends Controller {

        public function orderCreateCallbackAction(Request $request) {
         $data = $request->request->all();
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
             
            $this->get('site.helper.usertryitemhistory')->updateJSON(2, json_encode($decoded));
            return new Response(json_encode($request));
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
