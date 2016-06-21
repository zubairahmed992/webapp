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

    #----------------------------- selfieshare_feedback_update: /selfieshare/feedback_update

    public function feedbackUpdateAction() {
        $ra=$this->getRequest()->request->all();                
        $selfieshare=$this->get('user.selfieshare.helper')->updateFeedback($ra);  
        return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }

    
    #----------------------------------------------
    public function feedbackShowAction($ref=null) {
         $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);            
         #return new Response($selfieshare->getFriendName());
         return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_show.html.twig', array('selfieshare' => $selfieshare));   
    }

    #----------------------------- selfieshare_provide_feedback: /selfieshare/provide_feedback

    public function provideFeedbackAction($ref=null) {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        return $this->render('LoveThatFitUserBundle:Selfieshare:provide_feedback.html.twig', array('selfieshare' => $selfieshare));
        //$selfieshare=$this->get('user.selfieshare.helper')->updateFeedback($ra);
        //return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }

    #----------------------------- selfieshare_submit_feedback: /selfieshare/submit_feedback

    public function submitFeedbackAction() {
        $ra=$this->getRequest()->request->all();
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ra["ref"]);
        $this->get('user.selfiesharefeedback.helper')->createWithParam($ra,$selfieshare);
        return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }
    #----------------------------------------------
    public function feedbackReviewAction($ref=null) {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        #return new Response($selfieshare->getFriendName());
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_review.html.twig', array('selfieshare' => $selfieshare));
    }
}

?>