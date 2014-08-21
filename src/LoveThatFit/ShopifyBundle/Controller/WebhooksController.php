<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WebhooksController extends Controller {

        public function orderCreateAction(Request $request) {
            $this->get('site.helper.usertryitemhistory')->updateJSON(1, json_encode($request));
            return new Response(json_encode($request));
        }
        #----------------------------------------------------------
        public function cartCreateAction(Request $request) {
            $this->get('site.helper.usertryitemhistory')->updateJSON(1, json_encode($request));
            return new Response(json_encode($request));
        }

}
