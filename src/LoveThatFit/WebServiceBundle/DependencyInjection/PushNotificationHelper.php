<?php

namespace LoveThatFit\WebServiceBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PushNotificationHelper
{

    protected $env;
    private $fileNamesArray;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    //-------------------------------------------------------

    public function sendPushNotification($user, $msg = '')
    {
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
        $message = 'Your image has been calibrated';
        //$message=$msg;
        $badge = 1;
        $sound = 'default';
        // Construct the notification payload
        //foreach($msg as $messages=>$key){
        //  $message.=$key;
        // }
        $body = array();
        $body['aps'] = array('alert' => $message, 'data' => $msg);

        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;

        /* End of Configurable Items */
        $server = 'developement';
        /*if ($server == 'production') {
            $cert = dirname(__FILE__) . '/pushcert.pem';
        } else {

            $cert = dirname(__FILE__) . '/SSPush.pem';
        }*/

        $certpem = $this->getCertificateFile();
        $appleServer = $this->getPushNotificationPath();

        //echo $cert;
        //die;
        /*if ($server == 'production') {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        } else {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        }*/
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $payload = json_encode($body);

        if ($deviceToken) {

            foreach ($deviceToken as $token) {
                if ($token != '') {
                    $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $token)) . pack("n", strlen($payload)) . $payload;

                    fwrite($fp, $msg);
                }
            }
            fclose($fp);
            return "sending message :" . $payload;
        } else {
            return "device token not found: notification not sent.";
        }
    }


    #-------------------------------------------

    public function sendPushNotificationWithDeviceToken($deviceToken, $data = '')
    {
        $pass = '';
        $message = 'Your image has been calibrated';
        $badge = 1;
        $sound = 'default';
        $body = array();
        if ($data) {
            $body['aps'] = array('alert' => $message, 'data' => $data);
        } else {
            $body['aps'] = array('alert' => $message);
        }
        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;

        /* End of Configurable Items */
        $server = 'developement';
        /*if ($server == 'production') {
            $cert = dirname(__FILE__) . '/pushcert.pem';
        } else {
            $cert = dirname(__FILE__) . '/SSPush.pem';
        }*/

        $certpem = $this->getCertificateFile();
        $appleServer = $this->getPushNotificationPath();

        //echo $cert;
        //die;
        /*if ($server == 'production') {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        } else {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        }*/
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $payload = json_encode($body);

        //echo $deviceToken . "====";
        //die();
        if ($deviceToken) {
            //foreach($deviceToken as $token){
            $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;

            fwrite($fp, $msg);
            //  }
            fclose($fp);
            return "sending message :" . $payload;
        } else {
            return "device token not found: notification not sent.";
        }
    }

    //-------------------------------------------------------

    public function sendPushNotificationFeedback($user, $msg = '')
    {
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
        $message = 'You received the feedback';
        //$message=$msg;
        $badge = 1;
        $sound = 'default';
        // Construct the notification payload
        //foreach($msg as $messages=>$key){
        //  $message.=$key;
        // }
        $body = array();
        $body['aps'] = array('alert' => $message, 'data' => $msg);

        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;

        /* End of Configurable Items */
        $server = 'developement';
        /*if ($server == 'production') {
            $cert = dirname(__FILE__) . '/pushcert.pem';
        } else {

            $cert = dirname(__FILE__) . '/SSPush.pem';
        }*/

        $certpem = $this->getCertificateFile();
        $appleServer = $this->getPushNotificationPath();

        //echo $cert;
        //die;
        /*if ($server == 'production') {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        } else {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        }*/
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $payload = json_encode($body);

        if ($deviceToken) {

            foreach ($deviceToken as $token) {
                if($token != '') {
                    $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $token)) . pack("n", strlen($payload)) . $payload;
                    fwrite($fp, $msg);
                }
            }
            fclose($fp);
            return "sending message :" . $payload;
        } else {
            return "device token not found: notification not sent.";
        }
    }


    public function sendNotifyClothingType($deviceToken)
    {
        $pass = '';
        $message = 'Clothing type has been updated';
        $dataMsg = array('notification_type' => "clothing_type");
        $badge = 1;
        $sound = 'default';
        $body = array();
        $body['aps'] = array('alert' => $message, 'data' => json_encode($dataMsg));

        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;

        $server = 'developement';

        /*if ($server == 'production') {
            $cert = dirname(__FILE__) . '/pushcert.pem';
        } else {

            //$cert= dirname(__FILE__).'/certificates.pem';
            $cert = dirname(__FILE__) . '/SSPush.pem';
        }*/

        $certpem = $this->getCertificateFile();
        $appleServer = $this->getPushNotificationPath();

        /*if ($server == 'production') {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        } else {
            $appleServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $certpem = $cert;
        }*/
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $payload = json_encode($body);

        if ($deviceToken) {
            $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;

            fwrite($fp, $msg);
            fclose($fp);
            return "sending message :" . $payload;
        } else {
            return "device token not found: notification not sent.";
        }

    }

    protected function getCertificateFile(){
        $yaml = new Parser();
        $env  = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['push_envior'];

        $directory = dirname(__FILE__);
        if($env == "v3qa" || $env == "qa" ){
            return $directory. "/Production-QA-Certificate.pem";
        }else if($env == "dev"){
            return $directory. "/SelfieStyler3-QA.pem";
        }else if( $env == "prod" ){
            return $directory. "/pushcert.pem";
        }else{
            return $directory. "/pushcert.pem";
        }
    }

    protected function getPushNotificationPath(){
        $yaml = new Parser();
        $env  = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['push_envior'];

        if($env == "v3qa" || $env == "qa" ){
            return "ssl://gateway.push.apple.com:2195";
        }else if($env == "dev"){
            return "ssl://gateway.sandbox.push.apple.com:2195";
        }else if( $env == "prod" ){
            return "ssl://gateway.push.apple.com:2195";
        }else{
            return "ssl://gateway.push.apple.com:2195";
        }
    }




    public function sendPushNotificationFeedbackV3($user, $dataMsg)
    {
        $pass = '';
        $message = 'You received the feedback';

        $badge = 1;
        $sound = 'default';
        $body = array();
        $body['aps'] = array('alert' => $message, 'data' => $dataMsg);
        $deviceToken = $user->getDeviceTokenArrayByDevice('iphone');

        if ($badge)
            $body['aps']['badge'] = $badge;
        if ($sound)
            $body['aps']['sound'] = $sound;

        $server = 'developement';

        $certpem = $this->getCertificateFile();
        $appleServer = $this->getPushNotificationPath();

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $payload = json_encode($body);

        if ($deviceToken[0]) {
            $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken[0])) . pack("n", strlen($payload)) . $payload;

            fwrite($fp, $msg);
            fclose($fp);
            return "sending message :" . $payload;
        } else {
            return "device token not found: notification not sent.";
        }

    }

}
