<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class WSInterfaceController extends Controller {

    
    public function indexAction() {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/ws_details.yml'));

        /*echo "<pre>";
        print_r( $conf);
        echo "</pre>";
        die;*/
        
        $user_list= $this->get('user.helper.user')->getListWithPagination(0,'email');
        return $this->render('LoveThatFitWebServiceBundle:WSInterface:index.html.twig', array(
                    'services'=>$conf,
                    'users'=>$user_list['users'],
                ));
    }
    #--------------------------------------------------------
    public function userAction($email) {
        $user=$this->get('user.helper.user')->findByEmail($email);
        return new Response(json_encode($user->toDataArray()));
    }

    public function brainTreeTestTransactionAction()
    {
        return $this->render('LoveThatFitWebServiceBundle:WSInterface:braintree.html.twig', array());
    }
    
}