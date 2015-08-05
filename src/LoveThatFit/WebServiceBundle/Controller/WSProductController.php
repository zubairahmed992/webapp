<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSProductController extends Controller {

    public function productsAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            $res = $this->get('webservice.helper')->productSync($user, array_key_exists('date', $decoded) ? $decoded['date'] : null);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }
        return new Response($res);
    }

    #----------------------------------------------------------------------------------------
    
    
}

