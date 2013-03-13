<?php

namespace LoveThatFit\AdminBundle;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Templating\EngineInterface;

class MailHelper {

    protected $mailer;
    protected $templating;
    var $conf;
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {    
         $this->mailer = $mailer;
         $yaml = new Parser();
         $this->conf = $yaml->parse(file_get_contents('../app/config/parameters.yml'));
         $this->templating = $templating;
       
    }

    private function sendEmail($from, $to, $body, $subject = '')
    {
               
              
        $message = \Swift_Message::newInstance()
               
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setContentType("text/html")
             ->setBody(
            $this->renderView(
                'LoveThatFitAdminBundle::email/registration.html.twig',
                array('name' => 'name')
            )
        );
           
        $this->mailer->send($message);
    }
    
    public function sendRegistrationEmail($sendTo)
    { 
        
        
      
        $from=$this->conf['parameters']['mailer_user'];
        $to=$sendTo;
        $body='LoveThatFitAdminBundle::email/registration.txt.twig';
        $subject='LoveThatFit: Thank you for Registering with us. ';
       
 
        if($this->sendEmail($from, $to, $body, $subject))
        {
        return "Mail Sent";    
        }else{
        return "Please Try Again!!";
        }
        
        
    }
    
     public function sendPasswordResetLinkEmail($sendTo, $reset_link)
    { 
        $from=$this->conf['parameters']['mailer_user'];
        $to=$sendTo;
        $body='To reset you LTF password please go to the following link'.$reset_link;
        $subject='LoveThatFit: Password Reset';
        $this->sendEmail($from, $to, $body, $subject);
        return true;
        
    }
    


}
