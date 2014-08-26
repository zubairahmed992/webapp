<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use LoveThatFit\WebServiceBundle\Form\Type\ServiceFormType;
class ServiceInterfaceController extends Controller {

    
    public function indexAction() {
        $conf = $this->get('webservice.helper')->getServiceDetails();
        $names = $this->get('webservice.helper')->stripToNameArray($conf);
        $form = $this->createForm(new ServiceFormType($names), array('message' => 'web service type'));
        
        #$str=json_encode($this->get('webservice.helper')->getServiceNames());
        #return new Response(json_encode($conf));
         return $this->render('LoveThatFitWebServiceBundle:ServiceInterface:index.html.twig', array(
                    'form' => $form->createView(),
                    'service_conf'=>$conf,
                ));
    }

    public function hitAction() {
    
    #$str=json_encode($this->get('webservice.helper')->getServiceDetails());
        $str=json_encode($this->get('webservice.helper')->stripToNameArray($this->get('webservice.helper')->getServiceDetails()));
    return new Response($str);
        return new Response('has been hit');
    }
   

}