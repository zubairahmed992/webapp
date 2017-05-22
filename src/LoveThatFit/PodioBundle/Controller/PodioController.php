<?php

namespace LoveThatFit\PodioBundle\Controller;

use LoveThatFit\PodioBundle\Entity\PodioOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class PodioController extends Controller {

	private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;        
    }

    public function indexAction()
    {
    	die('podio');
    }

}

?>