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
use LoveThatFit\UserBundle\Entity\PushNotification;


class PushNotificationHelper{
     /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;
    private $container;

    protected $conf;
    
     public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        
    }

   //-------------------------------------------------------

    public function savePushNotification(PushNotification $PushNotification) {
      $PushNotification->setUpdatedAt(new \DateTime('now'));   
      $PushNotification->setCreatedAt(new \DateTime('now'));
       
       $this->em->persist($PushNotification);
       $this->em->flush();
    }
 
    public function sendPushNotification($deviceToken,$msg){
        
    $deviceToken =$deviceToken; //OR //$deviceToken = $argv[2];//$_GET['token'] or $deviceToken = $argv[2] ;//or $deviceToken reason
   // Passphrase for the private key (ck.pem file)
    $pass = '123456';
   // Get the parameters from http get or from command line

 $message=null;
   $badge = 1 ;
  $sound = 'default';
   // Construct the notification payload
  foreach($msg as $messages=>$key){
      $message.=$key;
  }
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
        foreach($deviceToken as $token=>$key){
        $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $key)) . pack("n",strlen($payload)).$payload;
        fwrite($fp, $msg);
        }
        fclose($fp);
        return "sending message :" . $payload;
    }
 
#-----------Fetch PushNotification type from YML ------------------------------#   
  public function getNotificationType($type){
    
       $allNotification= $this->conf["PushNotification"]["type"];
        foreach($allNotification as $singleNotification=>$key){
           if($singleNotification==$type and $key['status']=="true"){
            return array('status'=>$key['status'],'message'=>$key['message'],'type'=>$singleNotification);
           }
        }
       
  }
  #---------------Save the notification in DB---------------------------#
  public function setNotificationInDB($type, $msg){
        $PushNotification = new PushNotification();
        $PushNotification->setMessage($msg);
        $PushNotification->setNotificationType($type);
        $PushNotification->setUserId(0);
        $PushNotification->setNotificationLimit(0);
        $PushNotification->setIsActive(1);
        $this->savePushNotification($PushNotification);
        return array('msg'=>' Notifcation Saved Successfully');
  }
#---------------Update Push Notification Table Base on User Id ----------------#
 public function updatePushNotification (){
     $userId='0';
     $notificationData=$this->findByUserId($userId);
     $msg=array();
     foreach($notificationData as $keyData){
         $msg[]=$keyData->getMessage();
     }
     
     #------------ GEt first 100 User with Device Type-------------------------#
     $firstHundredUser= $this->container->get('webservice.helper.user')->getFirstHundredUSerWithDeviceType();
     $device_type_array=array();
     foreach($firstHundredUser as $device_type){
         $device_type_array[]=$device_type['deviceName'];
     }
 
    $sendMsg=$this->sendPushNotification($device_type_array, $msg);
    return  $sendMsg;
    
     
     if(count($notificationData)>0){
         
        foreach($notificationData as $singleNotification){
            $singleNotification->setUserId($notificationReceiverUserId);
            $this->savePushNotification($singleNotification);
        }
     }else{
         return array('status'=>'false');
     }
     
     
 }
 #----------Find PushNotification Base On User Id------------------------------#
 public function findByUserId($userId){
  return $this->repo->findByUserId($userId);
 }
 
    
}
