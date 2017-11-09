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
        if ($selfieshare != null) {
            return new Response($selfieshare->getFriendName().'provided feedback updated.');
        } else {
            return new Response($this->get('webservice.helper')->response_array(false, "some thing went wrong"));
        }
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

        $friendName = ($selfieshare->getFriendName() != "" ? $selfieshare->getFriendName() : $selfieshare->getFriendEmail());

        $user=$this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $push_notification = array();
        $baseurl = $this->getRequest()->getHost();
        $url = $baseurl . $this->generateUrl('selfieshare_feedback_review', array('ref' => $ra["ref"]));
        $push_notification["url"] = $url;
        $push_notification["notification_type"] = "friends_feedback";
        $json_data = json_encode($push_notification);
        $this->get('user.selfiesharefeedback.helper')->createWithParam($ra,$selfieshare);
        $push_response = $this->get('pushnotification.helper')->sendPushNotificationFeedback($user, $json_data, $friendName);


        return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }
    #----------------------------------------------
    public function feedbackReviewAction($ref=null) {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        #return new Response($selfieshare->getFriendName());
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_review.html.twig', array('selfieshare' => $selfieshare));
    }

    #----------------selfieshare_provide_feedback_v3: /selfieshare/provide_feedback

    public function provideFeedbackV3Action($ref=null)
    {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        $user  =$this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $name  = $user->getFullName();
        $image = $user->getImage();
        if(trim($name) == ''){
            $name = $user->getEmail();
        }
        $count = $this->container->get('user.selfiesharefeedback.helper')->findByShareId($selfieshare->getId());
        $getLink = $this->container->get('admin.helper.appstorelink')->find(1);
        $link = $getLink->getAppLink();
        if ($count == 0) {
            return $this->render('LoveThatFitUserBundle:Selfieshare:provide_feedback_v3.html.twig', array('selfieshare' => $selfieshare , 'name' => $name, 'image' => $image, "link" => $link));
        } else {
            return $this->render('LoveThatFitUserBundle:Selfieshare:v3thankyou.html.twig', array(
                'name' => $name,
                'link' => $link,
                )
            );
        }
    }

    #--------------------- selfieshare_submit_feedback_new: /selfieshare/submit_feedback_new

    public function submitFeedbackV3Action()
    {
        $ra=$this->getRequest()->request->all();
        $selfieshare = $this->get('user.selfieshare.helper')->findByRef($ra["ref"]);

        $friendName = ($selfieshare->getFriendName() != "" ? $selfieshare->getFriendName() : $selfieshare->getFriendEmail());

        $user = $this->container->get('user.helper.user')->find($selfieshare->getUser()->getId());
        $push_notification = array();
        $baseurl = $this->getRequest()->getHost();
        $url = $baseurl . $this->generateUrl('selfieshare_feedback_review_v3', array('ref' => $ra["ref"]));
        $push_notification["url"] = $url;
        $push_notification["notification_type"] = "friends_feedback";
        $json_data = json_encode($push_notification);
        $this->get('user.selfiesharefeedback.helper')->createWithParam($ra,$selfieshare);
        $push_response = $this->get('pushnotification.helper')->sendPushNotificationFeedbackV3($user, $json_data, $friendName);
        
        return new Response($selfieshare->getFriendName().' provided feedback updated.');
    }

    #----------------------------------------------provide thankyou page
    public function submitFeedbackThankyouV3Action($userid=null)
    {
        $user  =$this->container->get('user.helper.user')->find($userid);
        $name  = $user->getFullName();
        if(trim($name) == ''){
            $name = $user->getEmail();
        }
        $getLink = $this->container->get('admin.helper.appstorelink')->find(1);
        $link = $getLink->getAppLink();
        return $this->render('LoveThatFitUserBundle:Selfieshare:v3provide_thankyou.html.twig', array(
            'name' => $name,
            'link' => $link,
            )
        );
    }

    #----------------------------------------------
    public function feedbackReviewV3Action($ref=null)
    {
        $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);
        return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_review_v3.html.twig', array('selfieshare' => $selfieshare));
    }
}
?>