<?php

namespace LoveThatFit\AdminBundle;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class MailHelper {

    protected $mailer;
var $conf;
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $yaml = new Parser();
        $this->conf = $yaml->parse(file_get_contents('../app/config/parameters.yml'));
    }

    private function sendEmail($from, $to, $body, $subject = '')
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body);

        $this->mailer->send($message);
    }
    
    public function sendRegistrationEmail($sendTo)
    { 
        $from=$this->conf['parameters']['mailer_user'];
        $to=$sendTo;
        $body='email sent in the result of the registrattion process. thanks any way.';
        $subject='LoveThatFit: Thank you for Registering with us. ';
        
        $this->sendEmail($from, $to, $body, $subject);
        return true;
        
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
