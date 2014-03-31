<?php

namespace LoveThatFit\WebServiceBundle;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ProductEvent;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\Response;


class PushNotificationHelper{

   
    public function __construct()
    {
        
        
    }
 
    public function sendPushNotification($deviceToken,$msg,$request){
    $deviceToken = $deviceToken ; //OR //$deviceToken = $argv[2];//$_GET['token'] or $deviceToken = $argv[2] ;//or $deviceToken reason
   // Passphrase for the private key (ck.pem file)
    $pass = '123456';
   // Get the parameters from http get or from command line

   $message = 'test';
   $badge = 1 ;
  $sound = 'default';
   // Construct the notification payload
   $body = array();
   $body['aps'] = array('alert' => $message);

  if ($badge)
     $body['aps']['badge'] = $badge;
  if ($sound)
     $body['aps']['sound'] = $sound;
  
 

   /* End of Configurable Items */
   $server = 'developement';
  if($server=='production'){
   $cert= dirname(__FILE__).'/cerpro.pem';
   }else{
   $cert= dirname(__FILE__).'/cert.pem';
   }

   if($server=='production'){ 
      $appleServer='ssl://gateway.push.apple.com:2195';
      $certpem = $cert;
   }
   else{
      $appleServer='ssl://gateway.sandbox.push.apple.com:2195';
      $certpem = $cert;
    }
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
         stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        $payload = json_encode($body);
        $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
        fwrite($fp, $msg);
        fclose($fp);
        return "sending message :" . $payload;
    }
 
}
