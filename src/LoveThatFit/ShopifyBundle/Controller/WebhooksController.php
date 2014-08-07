<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WebhooksController extends Controller {

        public function orderCreateAction(Request $request) {
            return json_encode($request);
        }
        #----------------------------------------------------------
        public function cartCreateAction(Request $request) {
            return json_encode($request);
        }

}
