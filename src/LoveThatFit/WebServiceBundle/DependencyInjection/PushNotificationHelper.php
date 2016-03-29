<?php

namespace LoveThatFit\WebServiceBundle\DependencyInjection;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


class PushNotificationHelper{


     public function __construct(){
        
    }
 
   //-------------------------------------------------------

    public function sendPushNotification($deviceToken,$msg){
    //print_r($deviceToken);die;
	$pass = '';
   // Get the parameters from http get or from command line

  $message=$msg;
   $badge = 1 ;
  $sound = 'default';
   // Construct the notification payload
  //foreach($msg as $messages=>$key){
    //  $message.=$key;
 // }
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

   $cert= 'ck.pem';
   }
	//echo $cert;
   if($server=='production'){ 
      $appleServer='ssl://gateway.sandbox.push.apple.com:2195';
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

        //foreach($deviceToken as $token=>$key){
         $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)).$payload;

        fwrite($fp, $msg);
       // }
        fclose($fp);
        return "sending message :" . $payload;
    }
 

 #-------------------------------------------
 
 
 
}
