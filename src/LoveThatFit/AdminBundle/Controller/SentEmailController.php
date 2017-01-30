<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;

class SentEmailController extends Controller
{

    public function showAction($email_type, $id)
    {
        $userParentChilds = $this->get('user.helper.parent.child')->find($id);
        #return new Response($userParentChilds->getEmail());
        return $this->render('LoveThatFitAdminBundle::email/parent_registration.html.twig', array('entity' => $userParentChilds, 'reset_link' => ''));
    }

    #-------------------------------------
    public function selfieshareAction($ref)
    {
        $selfieshare = $this->get('user.selfieshare.helper')->findByRef($ref);
        return $this->render('LoveThatFitAdminBundle::email/selfieshare.html.twig', array('user' => $selfieshare->getUser(), 'selfieshare' => $selfieshare));
    }

    #-------------------------------------
    public function sendTestEmailAction()
    {
        $current = new \DateTime('now');
        $datetime1 = new \DateTime('now');
        $datetime2 = new \DateTime('2009-10-13');
        $interval = $datetime1->diff($datetime1);
        return new Response($interval->format('%R%a'));

        $ss = $this->get('user.selfieshare.helper')->findByRef('56bdf1491f14e');
        $ss_ar['to_email'] = 'waqas.muddasir@centricsource.com';
        $ss_ar['template'] = 'LoveThatFitAdminBundle::email/selfieshare.html.twig';
        $ss_ar['template_array'] = array('user' => $ss->getUser(), 'selfieshare' => $ss);
        $ss_ar['subject'] = 'SelfieStrler friend share';
        $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
        return $this->render('LoveThatFitAdminBundle::email/selfieshare.html.twig', array('user' => $ss->getUser(), 'selfieshare' => $ss));
    }
}
