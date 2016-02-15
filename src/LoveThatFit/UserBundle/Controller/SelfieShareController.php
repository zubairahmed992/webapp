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
        $ra=$this->getRequest()->request->all();
        #return new Response(json_encode($ra));
        $selfieshare=$this->get('user.selfieshare.helper')->updateFeedback($ra);  
        
        $user=$selfieshare->getUser();
        $ss_ar['to_email'] = $user->getEmail();
        $ss_ar['template']='LoveThatFitAdminBundle::email/selfieshare.html.twig';
        $ss_ar['template_array']=array('user'=>$user, 'selfieshare'=>$selfieshare);
        $ss_ar['subject']='SelfieStrler friend share';
        $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
            
        return new Response($selfieshare->getFriendName().'provided feedback updated.');
    }

    
    #----------------------------------------------
    public function feedbackShowAction($ref=null) {
         $selfieshare=$this->get('user.selfieshare.helper')->findByRef($ref);            
         return $this->render('LoveThatFitUserBundle:Selfieshare:feedback_show.html.twig', array('selfieshare' => $selfieshare));   
    }

}

?>