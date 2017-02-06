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

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating) {
        $this->mailer = $mailer;
        $yaml = new Parser();
        $this->conf = $yaml->parse(file_get_contents('../app/config/parameters.yml'));
        $this->templating = $templating;
        $this->server = isset($this->conf['parameters']['server'])?$this->conf['parameters']['server']:'';
    }

    private function sendEmail($from, $to, $body, $user, $subject = '', $reset_link='') {

        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setContentType("text/html")
                ->setBody(
                $this->templating->render($body, array('entity' => $user, 'reset_link' => $reset_link)));
        if($this->server!='local')
        {
          try {
              $this->mailer->send($message);
          } catch (\Swift_TransportException $e) {
              $result = array(
                  false,
                  'There was a problem sending email: ' . $e->getMessage()
              );
              return $result;
          }


          $result = array(
              true,
              'email has been sent.'
          );
        }else{
          $result = array(
              true,
              'email not sent.'
          );
        }
        return $result;
    }

    private function sendHtmlEmail($from, $to, $body, $subject = '', $dataArray = array()) {

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setContentType("text/html")
            ->setBody(
                $this->templating->render($body, array( 'dataArray' => $dataArray)));
        if($this->server!='local')
        {
            try {
                $this->mailer->send($message);
            } catch (\Swift_TransportException $e) {
                $result = array(
                    false,
                    'There was a problem sending email: ' . $e->getMessage()
                );
                return $result;
            }


            $result = array(
                true,
                'email has been sent.'
            );
        }else{
            $result = array(
                true,
                'email not sent.'
            );
        }
        return $result;
    }

    public function sendRegistrationEmail($user) {

        $from = $this->conf['parameters']['mailer_user'];
        $to = $user->getEmail();
        $body = "LoveThatFitAdminBundle::email/registration.html.twig";
        $subject = 'SelfieStyler: Thank you for registering with us. ';
        //return 'emailing is currently disabled';
        return $this->sendEmail($from, $to, $body, $user, $subject);
                            
        
    }

    public function sendSuccessPurchaseEmail( $user, $dataArray )
    {
        $from = $this->conf['parameters']['mailer_user'];
        $to = $dataArray['email'];

        $body = "LoveThatFitAdminBundle::email/user_purchase.html.twig";
        $subject = 'SelfieStyler: Thank you very much for your order. ';

        return $this->sendHtmlEmail($from, $to, $body, $subject, $dataArray);
    }

    public function sendPurchaseEmailToAdmin( $user, $dataArray )
    {
        $from = $this->conf['parameters']['mailer_user'];
        // $to = $dataArray['email'];

        $to = "milwaukeestore1@selfiestyler.com";

        $body = "LoveThatFitAdminBundle::email/email_order_detail.html.twig";
        $subject = 'SelfieStyler: New order has been received. ';

        return $this->sendHtmlEmail($from, $to, $body, $subject, $dataArray);
    }

    public function sendParentRegistrationEmail($user) {

        $from = $this->conf['parameters']['mailer_user'];
        $to = $user->getEmail();
        $body = "LoveThatFitAdminBundle::email/parent_registration.html.twig";
        $subject = 'SelfieStyler: Thank you for registering parent email. ';
        //return 'emailing is currently disabled';
        return $this->sendEmail($from, $to, $body, $user, $subject);
        
    }
    
    
    public function sendPasswordResetLinkEmail($user, $reset_link) {
        $from = $this->conf['parameters']['mailer_user'];
        $to = $user->getEmail();
        $body = "LoveThatFitAdminBundle::email/password_reset.html.twig";
        $subject = 'SelfieStyler: Password Reset';
        //return 'emailing is currently disabled';
        return $this->sendEmail($from, $to, $body, $user, $subject, $reset_link);
        
    }

	public function sendOrderConfirmationEmail($user,$order,$cart) {

	  $from = $this->conf['parameters']['mailer_user'];
	  //$to = $user->getEmail();
	  $to = "ovais.rafique@centricsource.com";
	  $body = "LoveThatFitAdminBundle::email/order_receipt.html.twig";
	  $subject = 'SelfieStyler: Thank you for the order. ';
	  //return 'emailing is currently disabled';

	  $message = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom($from)
		->setTo($to)
		->setContentType("text/html")
		->setBody(
		  $this->templating->render($body, array('entity' => $user, 'reset_link' => '', 'order' => $order, 'cart'=>$cart)));
	  try {
		$this->mailer->send($message);
	  } catch (\Swift_TransportException $e) {
		$result = array(
		  false,
		  'There was a problem sending email: ' . $e->getMessage()
		);
		return $result;
	  }

	  $result = array(
		true,
		'email has been sent.'
	  );
	  return $result;
	}

  public function sendFeedbackEmail($user,$content) {

	//$from = $user->getEmail();
	$from = $this->conf['parameters']['mailer_user'];
	//$to = $user->getEmail();
	$to = "membersupport@selfiestyler.com";
	$body = "Following feedback sent by ".$user->getEmail()."<br><br>".$content;
	$subject = 'SelfieStyler: Feedback Received. ';
	//return 'emailing is currently disabled';
      $message = \Swift_Message::newInstance()
          ->setSubject($subject)
          ->setFrom($from)
          ->setTo($to)
          ->setContentType("text/html")
          ->setBody($body)
          ->setReplyTo($user->getEmail());

    if($this->server!='local') {


      try {
        $this->mailer->send($message);
      } catch (\Swift_TransportException $e) {
        $result = array(
            false,
            'There was a problem sending email: ' . $e->getMessage()
        );
        return $result;
      }

      $result = array(
          true,
          'email has been sent.'
      );
    }else{
      $result = array(
          true,
          'email not sent.'
      );
    }
	return $result;
  }

   public function sendEmailWithTemplate($arr) {
        $from = $this->conf['parameters']['mailer_user'];
        $message = \Swift_Message::newInstance()
                ->setSubject($arr['subject'])
                ->setFrom($from)
                ->setTo($arr['to_email'])
                ->setContentType("text/html")
                ->setBody($this->templating->render($arr['template'], $arr['template_array']));
     if($this->server!='local') {
       try {
         $this->mailer->send($message);
       } catch (\Swift_TransportException $e) {
         $result = array(
             false,
             'There was a problem sending email: ' . $e->getMessage()
         );
         return $result;
       }

       $result = array(
           true,
           'email has been sent.'
       );
     }else{
       $result = array(
           true,
           'email not sent.'
       );
     }
        return $result;
    }

}
