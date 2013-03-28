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

    private function sendEmail($from, $to, $body, $subject = '',$user,$reset_link='')
    {
               
              
        $message = \Swift_Message::newInstance()
               
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setContentType("text/html")
             ->setBody(
            $this->templating->render($body,array('entity' => $user,'reset_link'=>$reset_link) ));
            
       
       //  $this->mailer->send($message);
       
    }
    
    public function sendRegistrationEmail($user)
    { 
        
        $from=$this->conf['parameters']['mailer_user'];
        $to=$user->getEmail();
        $body="LoveThatFitAdminBundle::email/registration.html.twig";
        $subject='LoveThatFit: Thank you for Registering with us. ';
        $name=$user->getUsername();
        $this->sendEmail($from, $to, $body, $subject,$user);
         return true;
                
    }
    
     public function sendPasswordResetLinkEmail($user, $reset_link)
    { 
        $from=$this->conf['parameters']['mailer_user'];
        $to=$user->getEmail();
        $body="LoveThatFitAdminBundle::email/password_reset.html.twig";
       // $body='To reset you LTF password please go to the following link'.$reset_link;
        $subject='LoveThatFit: Password Reset';
        
        $this->sendEmail($from, $to, $body, $subject,$user,$reset_link);
        return true;
        
    }
    


}
