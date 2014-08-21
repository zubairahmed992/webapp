<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WebhooksController extends Controller {

        public function orderCreateAction(Request $request) {
             $data = $request->request->all();
            $this->get('site.helper.usertryitemhistory')->updateJSON(1, json_encode($data));
            return new Response(json_encode($request));
        }
        #----------------------------------------------------------
        public function cartCreateAction(Request $request) {
             $data = $request->request->all();
            $this->get('site.helper.usertryitemhistory')->updateJSON(1, json_encode($data));
            return new Response(json_encode($request));
        }

}
