<?php

namespace LoveThatFit\WebServiceBundle\DependencyInjection;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
class PushNotificationHelper{


  public function __construct(Container $container) {
	$this->container = $container;

  }
   //-------------------------------------------------------

    public function sendPushNotification($user, $msg=''){
    //echo "test";
	  //print_r($deviceToken);die;
	  $pass = '';
	  #$msg='This is my third message';
   // Get the parameters from http get or from command line
	  //$id = $this->get('security.context')->getToken()->getUser()->getId();
	  //echo $id;

	  $deviceToken = $user->getDeviceTokenArrayByDevice('iphone');
	  /*
	  if(count($notification_val) == 1){
		$deviceToken = $notification_val[0];
	  }else{
		$deviceToken = implode(",",$notification_val);
	  }
	  */
	  //echo $deviceToken;
	  //die;
	  $message ='Your image has been Caliborated';
	  //$message=$msg;
	   $badge = 1 ;
	  $sound = 'default';
	   // Construct the notification payload
	  //foreach($msg as $messages=>$key){
		//  $message.=$key;
	 // }
	   $body = array();
	   $body['aps'] = array('alert' => $message  ,'data'=> $msg);

	  if ($badge)
		 $body['aps']['badge'] = $badge;
	  if ($sound)
		 $body['aps']['sound'] = $sound;

	   /* End of Configurable Items */
	   $server = 'developement';
	  if($server=='production'){
	   $cert= dirname(__FILE__).'/pushcert.pem';
	   }else{

	   $cert= dirname(__FILE__).'/SSPush.pem';
	   }

	//echo $cert;
   //die;
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

        if ($deviceToken){
        
        foreach($deviceToken as $token){
         $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token)) . pack("n",strlen($payload)).$payload;

        fwrite($fp, $msg);
        }
        fclose($fp);
        return "sending message :" . $payload;
        }else{
            return "device token not found: notification not sent.";
        }
    }
 

 #-------------------------------------------
 
 
 
}
