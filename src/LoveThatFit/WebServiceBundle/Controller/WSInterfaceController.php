<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class WSInterfaceController extends Controller {

    
    public function indexAction() {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/ws_details.yml'));        
        
        $user_list= $this->get('user.helper.user')->getListWithPagination(0,'email');
        return $this->render('LoveThatFitWebServiceBundle:WSInterface:index.html.twig', array(
                    'services'=>$conf,
                    'users'=>$user_list['users'],
                ));
    }
    
}