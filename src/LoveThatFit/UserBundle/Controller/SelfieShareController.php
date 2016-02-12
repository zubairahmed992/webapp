<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelfieShareController extends Controller {

   

    public function feedbackEditAction($ref=null) {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);            
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_edit.html.twig', array('selfieshare' => $selfieshare));          
    }

    #----------------------------------------------

    public function feedbackUpdateAction() {
        $this->Request->request->all();

        $ra=$this->process_request();
        #return new Response(json_encode($ra));
        $selfieshare=$this->get('user.selfieshare.helper')->updateFeedback($ra);            
        return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }

    
    #----------------------------------------------
    public function feedbackShowAction($ref=null) {
         $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);            
         return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_show.html.twig', array('selfieshare' => $selfieshare));   
    }

    #----------------------------------------------
     private function process_request() {
        $request=$this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);

        if ($decoded == null) #if null (to be used for web service testing))
            $decoded = $request->request->all();

        return $decoded;
    }

}

?>