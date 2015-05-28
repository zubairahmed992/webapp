<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

use LoveThatFit\WebServiceBundle\Form\Type\ServiceFormType;
class ServiceInterfaceController extends Controller {

    
    public function indexAction() {
        $conf = $this->getServiceDetails();
        $names = $this->stripToNameArray($conf);
        $form = $this->createForm(new ServiceFormType($names), array('message' => 'web service type'));
        $user_list= $this->get('user.helper.user')->getListWithPagination(0,'email');
        
        return $this->render('LoveThatFitWebServiceBundle:ServiceInterface:index.html.twig', array(
                    'form' => $form->createView(),
                    'services_array'=>$conf,
                    'services_json'=>json_encode($conf),
                    'users'=>$user_list['users'],
                ));
    }
    #------------------------------------------------------
    public function hitAction() {    
        $str=json_encode($this->stripToNameArray($this->getServiceDetails()));
        return new Response($str);       
    }
   #------------------------------------------------------
     public function userDetailAction($email) {
         return new Response(json_encode($this->get('webservice.helper.user')->getDetailArrayByEmail($email)));
    }
    #------------------------------------------------------
    public function fooAction() {
         
        return $this->render('LoveThatFitWebServiceBundle:ServiceInterface:foo.html.twig');
        #return new Response('oohoeyy');
    }
    #------------------------------------------
    public function barAction() {
        return new Response('oohoeyy');
    }
    
    
    #------------------------------------------------------------------------
    private function getServiceNames(){
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/routing.yml'));
        $ar=array();
        foreach($conf as $k=>$v){            
            if (preg_match("!ws_(.*)!", $k)) {                
                array_push($ar, str_replace("ws_","",$k));    
            }            
        }
        return $ar;
    }
#------------------------------------------------------------------------
    private function stripToNameArray($conf){
        $ar=array();
        foreach($conf as $k=>$v){            
                array_push($ar, $k);                
        }
        return $ar;
    }
#------------------------------------------------------------------------    
    private function getServiceDetails(){
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/services_list.yml'));        
        return $conf;
    }

    
}