<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelfieShareController extends Controller {

   

    public function feedbackEditAction($ref=null) {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        $user=$this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $name = $user->getFullName();
        if(trim($name) == ''){
            $name = $user->getEmail();
        }
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_edit.html.twig', array('selfieshare' => $selfieshare , 'name' => $name));
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
        $user=$this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $name = $user->getFullName();
        if(trim($name) == ''){
            $name = $user->getEmail();
        }
        return $this->render('LoveThatFitUserBundle:Selfieshare:provide_feedback.html.twig', array('selfieshare' => $selfieshare , 'name' => $name));
        //$selfieshare=$this->get('user.selfieshare.helper')->updateFeedback($ra);
        //return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }

    #----------------------------- selfieshare_submit_feedback: /selfieshare/submit_feedback

    public function submitFeedbackAction() {
        $ra=$this->getRequest()->request->all();
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ra["ref"]);
        $user=$this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $push_notification = array();
        $baseurl = $this->getRequest()->getHost();
        $url = $baseurl . $this->generateUrl('selfieshare_feedback_review', array('ref' => $ra["ref"]));
        $push_notification["url"] = $url;
        $push_notification["notification_type"] = "friends_feedback";
        $json_data = json_encode($push_notification);
        $this->get('user.selfiesharefeedback.helper')->createWithParam($ra,$selfieshare);
        $push_response = $this->get('pushnotification.helper')->sendPushNotificationFeedback($user, $json_data);
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